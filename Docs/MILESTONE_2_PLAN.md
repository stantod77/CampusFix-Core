# Milestone 2: Functional Beta - Task Assignments
**Project:** CampusFix
**Milestone Target:** March 13, 2026

## 1. Stanimir Todorov (Project Lead & Backend Architect)
**Primary Responsibility:** System Architecture & Data Security.
* **Database Integration:** Develop `insert_ticket.php` to securely capture student requests into the MySQL database.
* **Security:** Implement `password_hash()` verification to replace current test data.
* **Project Management:** Maintain the Gantt chart and lead weekly code reviews to ensure Phase 2 delivery.

## 2. Philip (Frontend Lead)
**Primary Responsibility:** User Interface (UI) & Experience (UX).
* **Login Interface:** Finalize `login.html` styling using Bootstrap 5 to match the institutional branding.
* **Dashboard View:** Create the "Active Tickets" table view where students can see their submission status.
* **Form Validation:** Implement JavaScript checks to ensure "Email" and "Description" fields are valid before submission.

## 3. Mohamad (Backend Engineer - C++)
**Primary Responsibility:** Core Priority Logic.
* **Algorithm Refinement:** Enhance the C++ keyword detection (e.g., "Smoke", "Leak", "Spark") to assign accurate priority scores (1-10).
* **CLI Integration:** Compile the C++ engine into an executable (.exe) that can accept command-line arguments from the PHP server.
* **Unit Testing:** Verify the sorting algorithm works correctly with batch data files.

## 4. Kaleb Yang (Backend Java Engineer)
**Primary Responsibility:** Java Notification Microservice ("The Watchdog").
* **Database Connection:** Implement JDBC connectivity to the MySQL `campusfix_db` to read ticket data independent of the PHP web server.
* **The "Watchdog" Loop:** Create a background service (while-loop) that polls the database every 30 seconds to detect new entries.
* **Alert Logic:** Program the triggers to identify "Critical" events (Priority Score > 80) and simulate an immediate alert output for security staff.