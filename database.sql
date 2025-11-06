-- Court Case Management System Database
CREATE DATABASE IF NOT EXISTS court_case_management;
USE court_case_management;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cases table
CREATE TABLE IF NOT EXISTS cases (
  id INT AUTO_INCREMENT PRIMARY KEY,
  Types VARCHAR(50),
  case_no VARCHAR(50),
  province VARCHAR(100) DEFAULT NULL,
  district VARCHAR(100) DEFAULT NULL,
  filed_date DATE DEFAULT NULL,
  court VARCHAR(150) DEFAULT NULL,
  category_cause VARCHAR(150) DEFAULT NULL,
  name_and_address TEXT DEFAULT NULL,
  terminated TINYINT(1) DEFAULT 0,
  next_date DATE DEFAULT NULL,
  remarks TEXT DEFAULT NULL,
  last_date DATE DEFAULT NULL
);

-- Default users
INSERT INTO users (username, password, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator'),
('user', '12345', 'John Doe');

-- Sample case data
INSERT INTO cases (Types, province, district, case_no, filed_date, court, category_cause, name_and_address, terminated, next_date, remarks, last_date)
VALUES
('Filed by the CEA','Central','Kandy','123/2023','2023-01-15','Kandy Magistrate Court','Environmental Violation','John Doe, 123 Main St, Kandy',0,'2023-02-20','Initial hearing completed.','2023-01-15'),
('Filed by the CEA','Western','Colombo','456/2023','2023-02-10','Colombo District Court','Pollution Case','Jane Smith, 456 Elm St, Colombo',0,'2023-03-15','Awaiting evidence submission.','2023-02-10'),
('Filed by the CEA','Southern','Galle','789/2023','2023-03-05','Galle Magistrate Court','Illegal Dumping','Mike Johnson, 789 Pine St, Galle',0,'2023-04-10','Case under investigation.','2023-03-05');
