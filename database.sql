-- Court Case Management System Database
-- Import this file into phpMyAdmin or MySQL

CREATE DATABASE IF NOT EXISTS court_case_management;
USE court_case_management;

-- Users table for login
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cases table
CREATE TABLE IF NOT EXISTS cases (
    case_id VARCHAR(50) PRIMARY KEY,
    case_title VARCHAR(255) NOT NULL,
    case_type VARCHAR(100) NOT NULL,
    plaintiff_name VARCHAR(150) NOT NULL,
    defendant_name VARCHAR(150) NOT NULL,
    filing_date DATE NOT NULL,
    court_name VARCHAR(150) NOT NULL,
    judge_name VARCHAR(100),
    case_status VARCHAR(50) NOT NULL,
    case_description TEXT,
    next_hearing_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO users (username, password, full_name) VALUES
('admin', '12', 'System Administrator');
('user', '12345', 'CEA');

-- Insert sample cases
INSERT INTO cases (case_id, case_title, case_type, plaintiff_name, defendant_name, filing_date, court_name, judge_name, case_status, case_description, next_hearing_date) VALUES
('CIV-2024-001', 'Smith vs. Johnson Property Dispute', 'Civil', 'John Smith', 'Robert Johnson', '2024-01-15', 'District Court A', 'Hon. Judge Williams', 'Active', 'Property boundary dispute regarding land ownership.', '2024-11-15'),
('CRM-2024-002', 'State vs. Anderson', 'Criminal', 'State Prosecutor', 'Michael Anderson', '2024-02-20', 'Criminal Court B', 'Hon. Judge Davis', 'Under Trial', 'Criminal case involving theft charges.', '2024-11-20'),
('FAM-2024-003', 'Brown Divorce Case', 'Family', 'Sarah Brown', 'David Brown', '2024-03-10', 'Family Court C', 'Hon. Judge Martinez', 'Active', 'Divorce proceedings with custody disputes.', '2024-11-25'),
('CIV-2024-004', 'Tech Corp vs. StartUp Inc', 'Civil', 'Tech Corporation', 'StartUp Inc', '2024-04-05', 'Commercial Court D', 'Hon. Judge Thompson', 'Settled', 'Contract breach and damages claim.', NULL),
('CRM-2024-005', 'State vs. Wilson', 'Criminal', 'State Prosecutor', 'James Wilson', '2024-05-12', 'Criminal Court B', 'Hon. Judge Garcia', 'Active', 'Fraud investigation case.', '2024-12-01');
