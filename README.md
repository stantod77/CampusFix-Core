# 🏢 CampusFix: Smart Facility Maintenance Management System (SFMMS)

![Status](https://img.shields.io/badge/Status-Development-orange)
![Version](https://img.shields.io/badge/Version-1.0.0-blue)
![License](https://img.shields.io/badge/License-MIT-green)

## 📖 Project Overview
**CampusFix** is an automated IT solution designed to optimize the prioritization of campus infrastructure repairs. Unlike standard ticketing systems, it utilizes a **Polyglot Microservices Architecture** to decouple the user interface, logic processing, and system alerting into separate, specialized components.



---

## 🏗️ System Architecture
The system operates on three integrated layers:
1.  **Frontend & Middleware (Web):** HTML5/Bootstrap interface powered by **PHP 8.2** for secure login, ticket submission, and database connectivity.
2.  **Core Engine (C++):** A high-performance background service that executes the **Weighted Priority Algorithm** (Severity × Criticality) to automatically rank maintenance tasks.
3.  **"Watchdog" Microservice (Java):** A standalone background service that continuously monitors the database for critical anomalies (e.g., Gas Leaks, Fire) and triggers real-time system alerts via the console.

---

## 🛠️ Tech Stack
* **Web Server:** Apache (XAMPP)
* **Database:** MySQL 8.0
* **Backend Logic:** PHP 8.2
* **Priority Engine:** C++ (std::17)
* **Notification Service:** Java 21 (JDBC)
* **Frontend:** HTML5, CSS3, Bootstrap 5

---

## 👥 The Team
* **Stanimir (Stan):** Project Lead & System Architect
* **Mohamad:** Backend Engineer (C++ Priority Logic)
* **Philip:** Frontend Lead (Web Interface & UI)
* **Kaleb:** Java Engineer (Watchdog Notification Service)

---

## 🚀 Getting Started (For Developers)

### 1. Prerequisites
* **XAMPP** (Apache & MySQL)
* **VS Code** (Recommended)
* **GCC Compiler** (MinGW or Linux)
* **Java JDK 17** or higher
* **MySQL Connector/J** (JDBC Driver)

### 2. Database Setup
Before running any code, you must initialize the database:
1.  Open **phpMyAdmin** or MySQL Workbench.
2.  Import the script located at: `database/seed_data.sql`
3.  Verify that the `campusfix_db` database is created and populated with test users.

### 3. Run the Web Application (Stan & Philip)
1.  Clone this repo into your XAMPP `htdocs` folder:
    ```bash
    C:\xampp\htdocs\CampusFix-Core
    ```
2.  Start **Apache** and **MySQL** in the XAMPP Control Panel.
3.  Open your browser and navigate to: `http://localhost/CampusFix-Core/`

### 4. Run the Watchdog Service (Kaleb)
The Java service monitors the database independently of the web server.
1.  Navigate to the source folder:
    ```bash
    cd src
    ```
2.  Create a `config.properties` file in the `src/` folder with your DB credentials:
    ```properties
    db.host=localhost
    db.port=3306
    db.name=campusfix_db
    db.user=root
    db.password=
    ```
3.  Compile and run the Watchdog:
    ```bash
    javac CampusFixWatchdog.java
    java -cp ".;mysql-connector-j-8.0.33.jar" CampusFixWatchdog
    ```

### 5. Build the Logic Engine (Mohamad)
Compile and run the C++ priority engine:
```bash
cd backend-cpp
g++ -o engine main.cpp
./engine
## 🍓 Production Edge Deployment (Raspberry Pi 5)
For the final capstone demonstration, the system has been migrated from XAMPP to a dedicated Linux Edge Server.

### 1. Edge Environment Specs
* **Hardware:** Raspberry Pi 5 (8GB)
* **OS:** Debian (Raspberry Pi OS)
* **Access:** `http://campusfix.local` (Local DNS via mDNS)

### 2. Security Hardenings
* **Database Access:** Switched from `root` to a restricted `campus_user` for all PHP transactions.
* **Environment Config:** Database credentials are managed via `db_connect.php` at the server root.
* **Permissions:** Web directory ownership assigned to `www-data` to prevent unauthorized file execution.

### 3. Deployment Command
To sync latest changes to the edge server:
```bash
git pull origin main
sudo chown -R www-data:www-data /var/www/html/
