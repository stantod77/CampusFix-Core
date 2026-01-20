# 🏢 Smart Campus Facility Maintenance Management System

![Status](https://img.shields.io/badge/Status-Development-orange)
![Version](https://img.shields.io/badge/Version-1.0.0-blue)
![License](https://img.shields.io/badge/License-MIT-green)

The **Smart Facility Maintenance Management System (SFMMS)** is an automated IT solution designed to optimize the prioritization of campus infrastructure repairs. Unlike standard first-come-first-served ticketing systems, SFMMS utilizes a **C++ Logic Engine** to calculate "Urgency Scores" in real-time, ensuring critical assets (Server Rooms, Labs) are serviced before low-priority areas.

---

## 🏗️ Architecture
The system operates on a decoupled 3-tier architecture:
1.  **Frontend (Web):** HTML/Bootstrap interface for students/staff to submit maintenance requests.
2.  **Middleware (Integration):** Python scripts that handle data ingestion, sanitization, and database communication.
3.  **Backend (The Engine):** A compiled C++ service that performs the **Weighted Priority Algorithm** (Severity × Criticality).

---

## 🛠️ Tech Stack
* **Database:** MySQL 8.0
* **Logic Engine:** C++ (std::17)
* **Integration:** Python 3.10
* **Frontend:** HTML5, CSS3, JavaScript (No Frameworks)

---

## 🚀 Getting Started (For Developers)

### 1. Prerequisites
* VS Code (Recommended)
* MySQL Server running locally
* GCC Compiler (MinGW or Linux)
* Python 3.x

### 2. Database Setup
Before running the code, you must initialize the database:
1.  Open your MySQL Workbench/Terminal.
2.  Run the script located at: `database/schema_v1.sql`
3.  Verify that the `campusfix_db` database was created.

### 3. Build the Engine (Mohamad)
```bash
cd backend
g++ -o engine main.cpp
./engine
