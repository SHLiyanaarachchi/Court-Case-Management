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
   ALTER TABLE cases
  ADD COLUMN case_no VARCHAR(50) ,
  ADD COLUMN province VARCHAR(100) DEFAULT NULL,
  ADD COLUMN district VARCHAR(100) DEFAULT NULL,
  ADD COLUMN filed_date DATE DEFAULT NULL,
  ADD COLUMN court VARCHAR(150) DEFAULT NULL,
  ADD COLUMN category_cause VARCHAR(150) DEFAULT NULL,
  ADD COLUMN name_and_address TEXT DEFAULT NULL,
  ADD COLUMN terminated TINYINT(1) DEFAULT 0,
  ADD COLUMN next_date DATE DEFAULT NULL,
  ADD COLUMN remarks TEXT DEFAULT NULL,
  ADD COLUMN last_date DATE DEFAULT NULL;
);

UPDATE cases
SET
  case_no = case_id,
  filed_date = filing_date,
  court = court_name,
  category_cause = case_type,
  next_date = next_hearing_date,
  remarks = case_description,
  last_date = DATE(updated_at);

  UPDATE cases
SET name_and_address = CONCAT(
    COALESCE(plaintiff_name, ''), 
    CASE WHEN plaintiff_name IS NOT NULL AND defendant_name IS NOT NULL THEN '  /  ' ELSE '' END,
    COALESCE(defendant_name, '')
);

UPDATE cases
SET terminated = CASE
    WHEN LOWER(TRIM(case_status)) IN ('settled','dismissed','withdrawn','closed','terminated','completed') THEN 1
    ELSE 0
END;

SELECT case_id, case_no, filed_date, court, category_cause, name_and_address, terminated, next_date, remarks, last_date
FROM cases
ORDER BY filed_date DESC
LIMIT 50;

ALTER TABLE cases
  CHANGE COLUMN filing_date filed_date DATE;


-- Insert default admin user (username: admin, password: admin123)
INSERT INTO users (username, password, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator');

-- Insert sample cases
INSERT INTO cases (case_id, District,  Case_No.,  Filed_Date , Court, Category_Cause_of_Action, Name_Address, Terminated, Next_Date ,  Remarks , Last_Date ) VALUES
(1, 'District A', 'C-1001', '2023-01-15', 'Court 1', 'Civil', 'John Doe / Jane Smith', 0, '2023-02-20', 'Initial hearing scheduled.', '2023-01-15'),
(2, 'District B', 'C-1002', '2023-02-10', 'Court 2', 'Criminal', 'Alice Johnson / Bob Brown', 0, '2023-03-15', 'Awaiting evidence submission.', '2023-02-10');

