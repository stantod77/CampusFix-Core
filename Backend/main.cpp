/*______________________________________________________________________________
____    Written by: Mohamad Al Kary                                         ____
____    Date Written: 01/28/2026                                            ____
____    Title: Main - Ticket Priority Dispatch System (Configurable)        ____
______________________________________________________________________________*/

#include <iostream>
#include <string>
#include <vector>
#include <algorithm>
#include "config.h"
#include "utility.h"
#include "ticket_priority_engine.h"
#include "models.h"

using namespace std;

int main(int argc, char* argv[]){
    // Initialize configuration with defaults
    Config config;
    
    // Parse command-line arguments
    for (int i = 1; i < argc; i++) {
        string arg = argv[i];
        
        if (arg == "--help" || arg == "-h") {
            printUsage(argv[0]);
            return 0;
        }
        else if (arg == "--config" && i + 1 < argc) {
            config.loadFromFile(argv[++i]);
        }
        else if (arg == "--host" && i + 1 < argc) {
            config.set("host", argv[++i]);
        }
        else if (arg == "--port" && i + 1 < argc) {
            config.set("port", argv[++i]);
        }
        else if (arg == "--user" && i + 1 < argc) {
            config.set("user", argv[++i]);
        }
        else if (arg == "--password" && i + 1 < argc) {
            config.set("password", argv[++i]);
        }
        else if (arg == "--database" && i + 1 < argc) {
            config.set("database", argv[++i]);
        }
        else if (arg == "--demo-only") {
            config.set("demo_mode", "true");
        }
        else if (arg == "--verbose") {
            config.set("verbose", "true");
        }
        else if (arg == "--quiet") {
            config.set("verbose", "false");
        }
        else {
            cerr << "Unknown argument: " << arg << endl;
            printUsage(argv[0]);
            return 1;
        }
    }
    
    bool verbose = config.getBool("verbose", true);
    bool demo_only = config.getBool("demo_mode", false);
    
    if (verbose) {
        cout << "\n╔════════════════════════════════════════════════════════════╗" << endl;
        cout << "║           CAMPUSFIX TICKET PRIORITY DISPATCHER              ║" << endl;
        cout << "║              v2.1 (Configurable & Portable)                 ║" << endl;
        cout << "╚════════════════════════════════════════════════════════════╝\n" << endl;
        config.printSettings();
    }

    // Extract database configuration
    string host = config.get("host", "localhost");
    int port = config.getInt("port", 33060);
    string user = config.get("user", "root");
    string password = config.get("password", "");
    string db_name = config.get("database", "campusfix_db");

    // Initialize the priority engine
    TicketPriorityEngine* engine = nullptr;
    vector<Ticket> tickets;

    // Attempt database connection (unless demo_only mode)
    if (!demo_only) {
        bool db_success = false;
        try {
            if (verbose) {
                cout << "Connecting to database at " << host << ":" << port << "..." << endl;
            }
            
            // Establish database session
            mysqlx::Session session = get_session(host, port, user, password);
            
            // Try to get the database - this may throw
            mysqlx::Schema database = get_database(&session, db_name);
            
            if (verbose) {
                cout << "✓ Database connection established.\n" << endl;
            }

            // Initialize the priority engine with database connection
            engine = new TicketPriorityEngine(&database);

            // Fetch open tickets from database
            tickets = get_open_tickets(database);

            if (tickets.empty()) {
                if (verbose) {
                    cout << "No open tickets found in database." << endl;
                }
            } else {
                // Enrich tickets with category information from database
                for (auto& ticket : tickets) {
                    ticket.category = get_ticket_category(database, ticket.ticket_id);
                }
                if (verbose) {
                    cout << endl;
                }
            }
            
            db_success = true;
        } catch (const runtime_error& e) {
            cerr << "\n⚠ " << e.what() << endl;
        } catch (const mysqlx::Error& e) {
            cerr << "\n⚠ Database Error: " << e.what() << endl;
        } catch (const exception& e) {
            cerr << "\n⚠ Error: " << e.what() << endl;
        } catch (...) {
            cerr << "\n⚠ Unknown database error" << endl;
        }
        
        // If database failed, use demo mode
        if (!db_success) {
            if (verbose) {
                cout << "Falling back to demo mode with sample data...\n" << endl;
            }
            engine = new TicketPriorityEngine();  // Demo mode
            tickets.clear();
        }
    } else {
        if (verbose) {
            cout << "Running in demo mode (no database connection)\n" << endl;
        }
        engine = new TicketPriorityEngine();  // Demo mode
    }

    // If in demo mode and no tickets, use sample data for demonstration
    if (tickets.empty() && demo_only) {
        if (verbose) {
            cout << "Creating sample tickets for demonstration...\n" << endl;
        }
        
        tickets = {
            Ticket(101, 8, 1, TicketCategory::PLUMBING, "Water leak near server rack"),
            Ticket(102, 5, 2, TicketCategory::HVAC, "HVAC system not cooling properly"),
            Ticket(103, 6, 3, TicketCategory::ELECTRICAL, "Lights out in east wing corridor"),
            Ticket(104, 9, 1, TicketCategory::IT, "Main network switch down"),
            Ticket(105, 2, 5, TicketCategory::FURNITURE, "Chair broken in reading area"),
            Ticket(106, 10, 4, TicketCategory::ELECTRICAL, "Fire alarm system malfunction")
        };
    }

    if (verbose && !tickets.empty()) {
        cout << "Processing " << tickets.size() << " tickets...\n" << endl;
    }

    // Process each ticket through the priority engine
    vector<Assignment> assignments;
    for (const auto& ticket : tickets) {
        Assignment assignment = engine->processTicket(ticket);
        assignments.push_back(assignment);
        if (verbose) {
            engine->printAssignment(assignment, ticket);
        }
    }

    // Display summary sorted by priority (highest first)
    if (verbose) {
        cout << "\n╔════════════════════════════════════════════════════════════╗" << endl;
        cout << "║                   DISPATCH SUMMARY (by Priority)            ║" << endl;
        cout << "╚════════════════════════════════════════════════════════════╝\n" << endl;
    }

    // Sort assignments by priority score (highest first)
    sort(assignments.begin(), assignments.end(), 
         [](const Assignment& a, const Assignment& b) {
             return a.priority_score > b.priority_score;
         });

    cout << "Rank | Ticket ID | Priority Score | Assigned Trade       | Category" << endl;
    cout << "-" << string(75, '-') << endl;

    for (size_t i = 0; i < assignments.size(); i++) {
        // Find the original ticket to get category
        TicketCategory category = TicketCategory::OTHER;
        for (const auto& t : tickets) {
            if (t.ticket_id == assignments[i].ticket_id) {
                category = t.category;
                break;
            }
        }

        printf("%4zu | %9d | %14d | %-20s | %s\n",
               i + 1,
               assignments[i].ticket_id,
               assignments[i].priority_score,
               engine->tradeToString(assignments[i].assigned_trade).c_str(),
               engine->categoryToString(category).c_str());
    }

    cout << "\n" << string(75, '=') << endl;
    if (tickets.empty()) {
        cout << "ℹ No tickets to process." << endl;
    } else {
        cout << "✓ Priority dispatch completed successfully!" << endl;
        cout << "All tickets have been prioritized and assigned to appropriate trades." << endl;
        if (!demo_only) {
            cout << "Updates have been written to the database." << endl;
        }
    }
    cout << endl;

    // Cleanup
    delete engine;
    return 0;
}
