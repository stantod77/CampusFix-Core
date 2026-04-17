/*______________________________________________________________________________
____    Written by: Mohamad Al Kary                                         ____
____    Date Written: 01/28/2026                                            ____
____    Title: Utility Functions                                            ____
______________________________________________________________________________*/

#include <iostream>
#include <string>
#include <vector>
#include <stdexcept>
#include </opt/homebrew/include/mysqlx/xdevapi.h>
#include "models.h"

using namespace std;

mysqlx::Session get_session(string host, int protocol, string user, string password){
    try{
        mysqlx:: Session session(
            host, 
            protocol, 
            user,
            password
        );
        
        return session;
    } catch(const mysqlx::Error& e){
        cerr << "MySQLX Error: " << e.what() << endl;
        throw;
    }
}


mysqlx::Schema get_database(mysqlx::Session* session, string database_name) {
    try{
        mysqlx::Schema database = session->getSchema(database_name);
        cout << "Retrieved database: " << database_name << endl;
        return database;
    } catch(const mysqlx::Error& e){
        cerr << "MySQLX Error: " << e.what() << endl;
        throw runtime_error("Failed to access database: " + database_name);
    }
}

// Get building criticality score from database
int get_building_criticality(mysqlx::Schema& database, int building_id) {
    try {
        auto table = database.getTable("buildings");
        auto result = table.select("criticality_score")
                          .where("building_id = :id")
                          .bind("id", building_id)
                          .execute();
        
        auto row = result.fetchOne();
        if (row) {
            return row[0].get<int>();
        } else {
            cerr << "Warning: Building ID " << building_id << " not found in database." << endl;
            return 1;  // Default criticality
        }
    } catch(const mysqlx::Error& e){
        cerr << "MySQLX Error: " << e.what() << endl;
        return 1;
    }
}

// Get all open tickets from database
vector<Ticket> get_open_tickets(mysqlx::Schema& database) {
    vector<Ticket> tickets;
    
    try {
        auto table = database.getTable("tickets");
        // Query: SELECT ticket_id, severity_level, asset_id, description WHERE status = 'open'
        auto result = table.select("ticket_id", "severity_level", "asset_id", "description")
                          .where("status = 'open'")
                          .execute();
        
        for (auto row : result) {
            int ticket_id = row[0].get<int>();
            int severity = row[1].get<int>();
            int asset_id = 0;
            string description = row[3].get<string>();
            int building_id = 0;
            
            // Get asset_id if not null
            if (!row[2].isNull()) {
                asset_id = row[2].get<int>();
                
                // Look up building_id from assets table
                try {
                    auto assets_table = database.getTable("assets");
                    auto asset_result = assets_table.select("building_id")
                                                   .where("asset_id = :id")
                                                   .bind("id", asset_id)
                                                   .execute();
                    
                    auto asset_row = asset_result.fetchOne();
                    if (asset_row && !asset_row[0].isNull()) {
                        building_id = asset_row[0].get<int>();
                    }
                } catch(...) {
                    // If lookup fails, use default
                    building_id = 1;
                }
            } else {
                // No asset_id, use default building
                building_id = 1;
            }
            
            // Create ticket with building_id derived from asset_id
            Ticket ticket(ticket_id, severity, building_id, TicketCategory::OTHER, description);
            tickets.push_back(ticket);
        }
        
        cout << "Retrieved " << tickets.size() << " open tickets from database." << endl;
    } catch(const mysqlx::Error& e){
        cerr << "MySQLX Error in get_open_tickets: " << e.what() << endl;
        throw runtime_error("Failed to fetch tickets from database");
    } catch(const exception& e){
        cerr << "Error in get_open_tickets: " << e.what() << endl;
        throw runtime_error("Failed to fetch tickets from database");
    }
    
    return tickets;
}

// Get ticket category from asset_id
TicketCategory get_ticket_category(mysqlx::Schema& database, int ticket_id) {
    try {
        // First, get asset_id from ticket
        auto tickets_table = database.getTable("tickets");
        auto ticket_result = tickets_table.select("asset_id")
                                         .where("ticket_id = :id")
                                         .bind("id", ticket_id)
                                         .execute();
        
        auto ticket_row = ticket_result.fetchOne();
        if (!ticket_row || ticket_row[0].isNull()) {
            return TicketCategory::OTHER;
        }
        
        int asset_id = ticket_row[0].get<int>();
        
        // Now get category from assets table
        auto assets_table = database.getTable("assets");
        auto asset_result = assets_table.select("category")
                                       .where("asset_id = :id")
                                       .bind("id", asset_id)
                                       .execute();
        
        auto asset_row = asset_result.fetchOne();
        if (!asset_row) {
            return TicketCategory::OTHER;
        }
        
        string category_str = asset_row[0].get<string>();
        
        // Convert string to enum
        if (category_str == "hvac") return TicketCategory::HVAC;
        if (category_str == "plumbing") return TicketCategory::PLUMBING;
        if (category_str == "electrical") return TicketCategory::ELECTRICAL;
        if (category_str == "it") return TicketCategory::IT;
        if (category_str == "furniture") return TicketCategory::FURNITURE;
        
        return TicketCategory::OTHER;
    } catch(const mysqlx::Error& e){
        cerr << "MySQLX Error: " << e.what() << endl;
        return TicketCategory::OTHER;
    }
}

// Update ticket with priority score and assigned trade in database
bool update_ticket_priority(mysqlx::Schema& database, int ticket_id, int priority_score, Trade assigned_trade) {
    try {
        // Convert trade enum to technician category for lookup
        string trade_category;
        switch(assigned_trade) {
            case Trade::HVAC_TECH: trade_category = "hvac"; break;
            case Trade::PLUMBER: trade_category = "plumbing"; break;
            case Trade::ELECTRICIAN: trade_category = "electrical"; break;
            case Trade::SYSADMIN: trade_category = "it"; break;
            case Trade::GENERAL_MAINTENANCE: trade_category = "maintenance"; break;
            default: trade_category = "general"; break;
        }
        
        auto table = database.getTable("tickets");
        table.update()
             .set("priority_score", priority_score)
             .set("status", "assigned")
             .where("ticket_id = :id")
             .bind("id", ticket_id)
             .execute();
        
        return true;
    } catch(const mysqlx::Error& e){
        cerr << "MySQLX Error when updating ticket: " << e.what() << endl;
        return false;
    }
}

// Get building name by ID
string get_building_name(mysqlx::Schema& database, int building_id) {
    try {
        auto table = database.getTable("buildings");
        auto result = table.select("name")
                          .where("building_id = :id")
                          .bind("id", building_id)
                          .execute();
        
        auto row = result.fetchOne();
        if (row) {
            return row[0].get<string>();
        }
    } catch(const mysqlx::Error& e){
        cerr << "MySQLX Error: " << e.what() << endl;
    }
    
    return "Unknown Building";
}