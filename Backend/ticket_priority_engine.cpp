/*______________________________________________________________________________
____    Written by: Mohamad Al Kary                                         ____
____    Date Written: 01/28/2026                                            ____
____    Title: Ticket Priority Engine - Implementation                      ____
______________________________________________________________________________*/

#include "ticket_priority_engine.h"
#include "utility.h"
#include <iostream>

using namespace std;

// Constructor (fallback/demo mode)
TicketPriorityEngine::TicketPriorityEngine() : db_schema(nullptr) {
    initializeBuildingDatabase();
}

// Constructor with database connection
TicketPriorityEngine::TicketPriorityEngine(mysqlx::Schema* database) : db_schema(database) {
    // Initialize fallback data, but will use database when available
    initializeBuildingDatabase();
}

// Initialize building database with sample data
void TicketPriorityEngine::initializeBuildingDatabase() {
    // Sample buildings with criticality scores (fallback)
    building_database[1] = Building(1, "Server Room Alpha", 10);
    building_database[2] = Building(2, "Student Center", 5);
    building_database[3] = Building(3, "Dormitory B", 2);
    building_database[4] = Building(4, "Hospital Wing", 9);
    building_database[5] = Building(5, "Library", 4);
    building_database[6] = Building(6, "Maintenance Building", 3);
}

// Calculate priority score: Severity × Criticality
int TicketPriorityEngine::calculatePriorityScore(const Ticket& ticket) {
    int criticality = getBuildingCriticality(ticket.building_id);
    int priority_score = ticket.severity_level * criticality;
    return priority_score;
}

// Get building criticality score by ID (uses DB if available)
int TicketPriorityEngine::getBuildingCriticality(int building_id) {
    // Try database first if available
    if (db_schema) {
        try {
            return get_building_criticality(*db_schema, building_id);
        } catch(...) {
            cerr << "Database lookup failed, falling back to cache." << endl;
        }
    }
    
    // Fallback to cached data
    auto it = building_database.find(building_id);
    if (it != building_database.end()) {
        return it->second.criticality_score;
    }
    
    cerr << "Warning: Building ID " << building_id << " not found. Using default criticality 1." << endl;
    return 1;
}

// Map ticket category to assigned trade
Trade TicketPriorityEngine::assignTrade(TicketCategory category) {
    switch (category) {
        case TicketCategory::HVAC:
            return Trade::HVAC_TECH;
        case TicketCategory::PLUMBING:
            return Trade::PLUMBER;
        case TicketCategory::ELECTRICAL:
            return Trade::ELECTRICIAN;
        case TicketCategory::IT:
            return Trade::SYSADMIN;
        case TicketCategory::FURNITURE:
            return Trade::GENERAL_MAINTENANCE;
        default:
            return Trade::UNASSIGNED;
    }
}

// Main dispatcher function - processes a ticket and returns assignment
Assignment TicketPriorityEngine::processTicket(const Ticket& ticket) {
    int priority_score = calculatePriorityScore(ticket);
    Trade assigned_trade = assignTrade(ticket.category);
    
    Assignment assignment(ticket.ticket_id, priority_score, assigned_trade);
    
    // Update database if connected
    if (db_schema) {
        try {
            update_ticket_priority(*db_schema, ticket.ticket_id, priority_score, assigned_trade);
        } catch(...) {
            cerr << "Warning: Could not update ticket " << ticket.ticket_id << " in database." << endl;
        }
    }
    
    return assignment;
}

// Helper function to print assignment details
void TicketPriorityEngine::printAssignment(const Assignment& assignment, const Ticket& ticket) {
    // Get building name from database or fallback
    string building_name = "Unknown";
    if (db_schema) {
        try {
            building_name = get_building_name(*db_schema, ticket.building_id);
        } catch(...) {
            auto it = building_database.find(ticket.building_id);
            if (it != building_database.end()) {
                building_name = it->second.name;
            }
        }
    } else {
        auto it = building_database.find(ticket.building_id);
        if (it != building_database.end()) {
            building_name = it->second.name;
        }
    }
    
    cout << "\n========== TICKET DISPATCH ASSIGNMENT ==========" << endl;
    cout << "Ticket ID: " << assignment.ticket_id << endl;
    cout << "Building: " << building_name << endl;
    cout << "Severity Level: " << ticket.severity_level << endl;
    cout << "Building Criticality: " << getBuildingCriticality(ticket.building_id) << endl;
    cout << "Category: " << categoryToString(ticket.category) << endl;
    cout << "Description: " << ticket.description << endl;
    cout << "\n--- CALCULATED RESULTS ---" << endl;
    cout << "Priority Score: " << assignment.priority_score << endl;
    cout << "  (Calculation: " << ticket.severity_level << " × " 
         << getBuildingCriticality(ticket.building_id) << " = " 
         << assignment.priority_score << ")" << endl;
    cout << "Assigned Trade: " << tradeToString(assignment.assigned_trade) << endl;
    cout << "==============================================\n" << endl;
}

// Helper to convert enum to string
string TicketPriorityEngine::tradeToString(Trade trade) {
    switch (trade) {
        case Trade::HVAC_TECH:
            return "HVAC Tech";
        case Trade::PLUMBER:
            return "Plumber";
        case Trade::ELECTRICIAN:
            return "Electrician";
        case Trade::SYSADMIN:
            return "SysAdmin";
        case Trade::GENERAL_MAINTENANCE:
            return "General Maintenance";
        case Trade::UNASSIGNED:
            return "Unassigned";
        default:
            return "Unknown Trade";
    }
}

string TicketPriorityEngine::categoryToString(TicketCategory category) {
    switch (category) {
        case TicketCategory::HVAC:
            return "HVAC";
        case TicketCategory::PLUMBING:
            return "Plumbing";
        case TicketCategory::ELECTRICAL:
            return "Electrical";
        case TicketCategory::IT:
            return "IT";
        case TicketCategory::FURNITURE:
            return "Furniture";
        case TicketCategory::OTHER:
            return "Other";
        default:
            return "Unknown Category";
    }
}
