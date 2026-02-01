# 🏢 Smart Facility Maintenance Management System (SFMMS)

![Status](https://img.shields.io/badge/Status-Development-orange)
![Version](https://img.shields.io/badge/Version-1.0.0-blue)
![License](https://img.shields.io/badge/License-MIT-green)

**Project Overview**
The **Smart Facility Maintenance Management System (SFMMS)** is an automated IT solution designed to optimize the prioritization of campus infrastructure repairs. Unlike standard ticketing systems, it utilizes a **Polyglot Microservices Architecture** to decouple the user interface, logic processing, and system alerting into separate, specialized components.

---

## 🏗️ System Architecture
The system operates on four integrated layers:
1.  **Frontend (Web):** HTML/Bootstrap interface for ticket submission and administration.
2.  **Middleware (Integration):** Python scripts handling API routing and data sanitization.
3.  **Core Engine (C++):** A high-performance background service that executes the **Weighted Priority Algorithm** (Severity × Criticality) to rank maintenance tasks.
4.  **Notification Microservice (Java):** A standalone "Observer" service that continuously monitors the database for high-priority anomalies (e.g., Fire, Flooding) and triggers real-time system alerts.

---

## 🛠️ Tech Stack
* **Database:** MySQL 8.0
* **Core Logic:** C++ (std::17)
* **Alerting Service:** Java (JDK 17, JDBC)
* **Integration:** Python 3.10
* **Frontend:** HTML5, CSS3, JavaScript

---

## 👥 The Team
* **Stanimir (Stan):** Project Manager & System Architect
* **Mohamad:** Backend Engineer (C++ Logic)
* **Philip:** Frontend Developer (Web Interface)
* **Kaleb:** Microservice Developer (Java Notifications)

---

## 🚀 Getting Started (For Developers)

### 1. Prerequisites
* VS Code (Recommended)
* MySQL Server running locally
* GCC Compiler (MinGW or Linux)
* Java JDK 17 or higher
* MySQL Connector/J (JDBC Driver)
* Python 3.x

### 2. Database Setup
Before running any code, you must initialize the database:
1.  Open your MySQL Workbench or Terminal.
2.  Run the script located at: `database/schema_v1.sql`
3.  Verify that the `campusfix_db` database was created.

### 3. Build the Logic Engine (Mohamad)
Compile and run the C++ priority engine:
```bash
cd backend
g++ -o engine main.cpp
./engine
