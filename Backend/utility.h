/*______________________________________________________________________________
____    Written by: Mohamad Al Kary                                         ____
____    Date Written: 01/28/2026                                            ____
____    Title: Utility function headers                                     ____
______________________________________________________________________________*/
#pragma once

#include <string>
#include <vector>
#include </opt/homebrew/include/mysqlx/xdevapi.h>
#include "models.h"

using namespace std;

mysqlx::Session get_session(string host, int protocol, string user, string password);
mysqlx::Schema get_database(mysqlx::Session* session, string database_name);

// Database query functions
int get_building_criticality(mysqlx::Schema& database, int building_id);
vector<Ticket> get_open_tickets(mysqlx::Schema& database);
TicketCategory get_ticket_category(mysqlx::Schema& database, int ticket_id);
bool update_ticket_priority(mysqlx::Schema& database, int ticket_id, int priority_score, Trade assigned_trade);
string get_building_name(mysqlx::Schema& database, int building_id);