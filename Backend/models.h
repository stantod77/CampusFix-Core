/*______________________________________________________________________________
____    Written by: Mohamad Al Kary                                         ____
____    Date Written: 01/28/2026                                            ____
____    Title: Data Models for Tickets and Buildings                        ____
______________________________________________________________________________*/
#pragma once

#include <string>

using namespace std;

// Enum for ticket categories
enum class TicketCategory {
    HVAC,
    PLUMBING,
    ELECTRICAL,
    IT,
    FURNITURE,
    OTHER
};

// Enum for assigned trades
enum class Trade {
    HVAC_TECH,
    PLUMBER,
    ELECTRICIAN,
    SYSADMIN,
    GENERAL_MAINTENANCE,
    UNASSIGNED
};

// Building structure with criticality score
struct Building {
    int building_id;
    string name;
    int criticality_score;  // Range: 1-10
    
    Building() : building_id(0), name(""), criticality_score(0) {}
    Building(int id, string n, int score) 
        : building_id(id), name(n), criticality_score(score) {}
};

// Ticket structure containing severity and building reference
struct Ticket {
    int ticket_id;
    int severity_level;     // Range: 1-10
    int building_id;
    TicketCategory category;
    string description;
    
    Ticket() : ticket_id(0), severity_level(0), building_id(0), 
               category(TicketCategory::OTHER), description("") {}
    
    Ticket(int id, int severity, int bid, TicketCategory cat, string desc)
        : ticket_id(id), severity_level(severity), building_id(bid),
          category(cat), description(desc) {}
};

// Assignment result structure
struct Assignment {
    int ticket_id;
    int priority_score;
    Trade assigned_trade;
    
    Assignment() : ticket_id(0), priority_score(0), 
                   assigned_trade(Trade::UNASSIGNED) {}
    
    Assignment(int id, int score, Trade trade)
        : ticket_id(id), priority_score(score), assigned_trade(trade) {}
};
