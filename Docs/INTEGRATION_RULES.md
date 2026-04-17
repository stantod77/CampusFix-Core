# 🔗 System Integration Rules & API Contract

**Project:** Smart Campus Facility Maintenance Management System (SFMMS)  
**Version:** 1.0  
**Status:** Draft (Develop Branch)

---

## 1. Architecture Pattern: Shared Database
Since our subsystems (C++, Java, PHP) operate in different environments, we use the **MySQL Database** as our central communication hub. We do **not** use REST APIs.

### The "Handshake" Workflow
1.  **Web Dashboard (PHP):** Creates a Ticket → Saves to DB (Status: `open`).
2.  **Logic Engine (C++):** Polls DB for `open` tickets → Calculates Priority → Updates DB (Status: `assigned`).
3.  **Notification Service (Java):** Polls DB for high-priority events → Sends Alert → Logs action.

---

## 2. Data Contracts (The Dictionary)
All subsystems must adhere to these value mappings to prevent data corruption.

### A. Severity Levels (Input)
*Used by PHP (Input) and C++ (Calculation).*
| Value (Int) | Meaning | Description |
| :--- | :--- | :--- |
| **1** | **High** | Immediate safety hazard (e.g., Fire, Leak). |
| **2** | **Medium** | Functional failure (e.g., AC broken, Internet down). |
| **3** | **Low** | Cosmetic or minor issue (e.g., Flickering light). |

### B. Status Workflow
| Status String | Owner | Description |
| :--- | :--- | :--- |
| `'open'` | **PHP** | Newly created. Needs scoring. |
| `'assigned'` | **C++** | Scored by the Engine. Ready for Technician. |
| `'resolved'` | **Technician** | Work completed. |
| `'closed'` | **Admin** | Verified and archived. |

### C. Priority Score (Output)
*Calculated by C++.*
* **Range:** 0 to 100
* **Critical Threshold:** > 80 (Triggers Java Alarm)

---

## 3. Subsystem Responsibilities

### 🟢 Web Interface (PHP/HTML)
* **Access:** Read/Write
* **Role:** The "Input Gate."
* **Rule:** MUST set `severity_level` as an Integer (1, 2, or 3), NOT a string ("High").

### 🔵 Logic Engine (C++)
* **Access:** Read/Write
* **Role:** The "Brain."
* **Polling Interval:** Every **5 Seconds**.
* **Query Logic:**
    ```sql
    SELECT * FROM tickets WHERE status = 'open';
    -- After calculation:
    UPDATE tickets SET priority_score = X, status = 'assigned' WHERE id = Y;
    ```

### 🔴 Notification Service (Java)
* **Access:** Read-Only (mostly)
* **Role:** The "Watchdog."
* **Polling Interval:** Every **30 Seconds**.
* **Trigger Condition:**
    ```sql
    SELECT * FROM tickets WHERE priority_score > 80;
    ```

---

## 4. Connection Standards
* **Development:** `jdbc:mysql://localhost:3306/campusfix_db`
* **Credentials:** Do **NOT** hardcode passwords in the repo. Use environment variables or a local config file ignored by Git.