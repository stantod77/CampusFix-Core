-- 1. Create Users (Admin, Tech, Student)
INSERT INTO users (full_name, email, role, password_hash) VALUES 
('Stan Administrator', 'admin@campusfix.com', 'admin', 'hashed_secret_123'),
('Kalob Technician', 'tech@campusfix.com', 'technician', 'hashed_secret_456'),
('Student John', 'john@student.valencia.edu', 'student', 'hashed_secret_789');

-- 2. Create Buildings (Locations)
INSERT INTO buildings (name, criticality_score) VALUES 
('Server Room A', 10),      -- High Criticality (If AC breaks, servers melt)
('Science Lab 101', 8),     -- Medium-High
('Student Dorm B', 4),      -- Medium-Low
('Cafeteria Hall', 2);      -- Low

-- 3. Create Assets (The things that break)
INSERT INTO assets (name, category) VALUES 
('Main Server AC Unit', 'hvac'),
('Lab Safety Shower', 'plumbing'),
('Dorm Hallway Light', 'electrical'),
('Projector 4K', 'it');

-- 4. Create Tickets (The Test Cases)
-- Ticket 1: CRITICAL (Server Room AC broken)
INSERT INTO tickets (user_id, asset_id, building_id, description, priority_score, status) 
VALUES (1, 1, 1, 'Server room temperature rising! AC stopped.', 95, 'open');

-- Ticket 2: MEDIUM (Light flickering)
INSERT INTO tickets (user_id, asset_id, building_id, description, priority_score, status) 
VALUES (3, 3, 3, 'Light in hallway is flickering constantly.', 40, 'open');