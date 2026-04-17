/*______________________________________________________________________________
____    Written by: Mohamad Al Kary                                         ____
____    Date Written: 01/28/2026                                            ____
____    Title: Ticket Priority Engine - Header                              ____
______________________________________________________________________________*/
#pragma once

#include "models.h"
#include <map>
#include </opt/homebrew/include/mysqlx/xdevapi.h>

using namespace std;

class TicketPriorityEngine {
private:
    // Building criticality lookup database
    map<int, Building> building_database;
    
    // Optional database connection for live data
    mysqlx::Schema* db_schema;
    
public:
    // Constructor
    TicketPriorityEngine();
    TicketPriorityEngine(mysqlx::Schema* database);
    
    // Initialize building database with sample data (fallback mode)
    void initializeBuildingDatabase();
    
    // Calculate priority score: Severity × Criticality
    int calculatePriorityScore(const Ticket& ticket);
    
    // Map ticket category to assigned trade
    Trade assignTrade(TicketCategory category);
    
    // Main dispatcher function - processes a ticket and returns assignment
    Assignment processTicket(const Ticket& ticket);
    
    // Get building criticality score by ID (uses DB if available, else cache)
    int getBuildingCriticality(int building_id);
    
    // Helper function to print assignment details
    void printAssignment(const Assignment& assignment, const Ticket& ticket);
    
    // Helper to convert enum to string
    string tradeToString(Trade trade);
    string categoryToString(TicketCategory category);
};
