-- CAMPUSFIX DATABASE SCHEMA V1.0
-- Author: Stanimir (Stan)
-- Description: Core tables for Users, Assets, and Priority Ticketing

CREATE DATABASE IF NOT EXISTS campusfix_db;
USE campusfix_db;

-- 1. USERS (Students, Staff, Admins)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'technician', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. LOCATIONS (Buildings)
-- Criticality Score (1-10) is used for Mohamad's Algorithm
CREATE TABLE buildings (
    building_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    criticality_score INT NOT NULL CHECK (criticality_score BETWEEN 1 AND 10)
);

-- 3. ASSETS (The things that break)
CREATE TABLE assets (
    asset_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- e.g., "HVAC Unit 1"
    category ENUM('hvac', 'plumbing', 'electrical', 'it', 'furniture') NOT NULL,
    building_id INT,
    FOREIGN KEY (building_id) REFERENCES buildings(building_id) ON DELETE SET NULL
);

-- 4. TICKETS (The core work)
CREATE TABLE tickets (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    asset_id INT, -- Optional (User might just report a room, not a specific asset)
    description TEXT NOT NULL,
    status ENUM('open', 'assigned', 'in_progress', 'resolved') DEFAULT 'open',
    severity_level INT NOT NULL CHECK (severity_level BETWEEN 1 AND 10),
    priority_score INT DEFAULT 0, -- Calculated by C++ Engine later
    assigned_tech_id INT, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (asset_id) REFERENCES assets(asset_id),
    FOREIGN KEY (assigned_tech_id) REFERENCES users(user_id)
);

-- DUMMY DATA (So we can test immediately)
INSERT INTO buildings (name, criticality_score) VALUES 
('Server Room Alpha', 10), ('Student Center', 5), ('Dormitory B', 2);

INSERT INTO assets (name, category, building_id) VALUES 
('Main Server Rack', 'it', 1), ('Lobby AC Unit', 'hvac', 2);
