**🏢 CampusFix: Smart Facility Maintenance Management System (SFMMS)**
📖 Project Overview
CampusFix is an automated IT solution designed to optimize the prioritization of campus infrastructure repairs. It utilizes a Polyglot Microservices Architecture to decouple the user interface, logic processing, and system alerting.

**🏗️ System Architecture**
The system operates on three integrated layers:

Frontend & Middleware (Web): A "Midnight Navy" themed interface powered by PHP 8.2. Features a secure session-based Admin Dashboard and a public-facing ticket submission portal.

Core Engine (C++): A high-performance service executing a Weighted Priority Algorithm to rank tasks based on severity and location criticality.

"Watchdog" Microservice (Java): A background service that polls the MariaDB database every 30 seconds to detect "Critical" anomalies and trigger real-time alerts.

**🛠️ Tech Stack**
Hardware: Raspberry Pi 5 (8GB RAM)

OS: Raspberry Pi OS (Debian Bookworm)

Web Server: Apache2

Database: MariaDB (optimized with Unix Socket authentication)

Backend: PHP 8.2, C++17, Java 21

Frontend: HTML5, CSS3, Bootstrap 4.5

**👥 The Team**
Stanimir (Stan): Project Lead & System Architect (Backend & Security)

Philip: Frontend Lead (UI/UX Design & Branding)

Mohamad: Backend Engineer (C++ Priority Logic)

Kaleb: Java Engineer (Watchdog Notification Service)

**🚀 Deployment & Security (Edge Server**)
1. Unified Branding
The system utilizes a standardized Midnight Navy (#0b1f3a) color palette across all interfaces to maintain institutional branding. All headers feature a left-aligned CF CampusFix logo.

2. Database & Security Hardenings
Unix Socket Auth: PHP connects to MariaDB via the www-data system user, eliminating plain-text passwords in code for the local environment.

Session Guard: auth_check.php prevents unauthorized access to dashboard.php.

Credential Management: A secure credentials.txt is maintained in the project root for internal developer use.

3. Accessing the System
While on the campus network, the system can be accessed via the Pi's static IP:

Ticket Submission: http://192.168.1.141/ticket.html

Admin Dashboard: http://192.168.1.141/dashboard.php

