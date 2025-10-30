# Court Case Management System

A comprehensive web-based court case management system built with PHP, HTML, CSS, and MySQL for XAMPP local environment.

## Features

- **Secure Login System** - User authentication with session management
- **Case Search** - Search cases by ID with dropdown selection
- **View Case Details** - Display complete case information
- **Add New Cases** - Create new court cases with validation
- **Update Cases** - Edit existing case information
- **Delete Cases** - Remove cases with confirmation
- **Modern UI** - Beautiful design with background images and animations

## Installation Instructions

### Prerequisites
- XAMPP installed on your system
- Web browser (Chrome, Firefox, Edge, etc.)

### Setup Steps

1. **Install XAMPP**
   - Download and install XAMPP from https://www.apachefriends.org/
   - Install to default location (C:\xampp on Windows)

2. **Copy Project Files**
   - Copy all project files to: `C:\xampp\htdocs\court_case_management\`
   - Your folder structure should look like:
     ```
     C:\xampp\htdocs\court_case_management\
     ├── index.php
     ├── dashboard.php
     ├── add_case.php
     ├── edit_case.php
     ├── delete_case.php
     ├── logout.php
     ├── config.php
     └── database.sql
     ```

3. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start **Apache** server
   - Start **MySQL** database

4. **Create Database**
   - Open your web browser
   - Go to: `http://localhost/phpmyadmin`
   - Click on "Import" tab
   - Click "Choose File" and select `database.sql`
   - Click "Go" button at the bottom
   - Database `court_case_management` will be created with sample data

5. **Access the Application**
   - Open your browser
   - Go to: `http://localhost/court_case_management/`
   - You will see the login page

6. **Login**
   - **Username:** admin
   - **Password:** admin123

## Default Login Credentials

- **Username:** admin
- **Password:** admin123

## Database Configuration

The default database configuration in `config.php` is:
- **Host:** localhost
- **Username:** root
- **Password:** (empty)
- **Database:** court_case_management

If your XAMPP MySQL has a different configuration, edit `config.php` accordingly.

## Sample Cases

The database comes with 5 sample cases:
1. CIV-2024-001 - Smith vs. Johnson Property Dispute
2. CRM-2024-002 - State vs. Anderson
3. FAM-2024-003 - Brown Divorce Case
4. CIV-2024-004 - Tech Corp vs. StartUp Inc
5. CRM-2024-005 - State vs. Wilson

## Usage Guide

### Search a Case
1. Login to the system
2. Select a case ID from the dropdown on the dashboard
3. Click "Search Case" button
4. View complete case details

### Add New Case
1. Click "Add New Case" button on dashboard
2. Fill in all required fields (marked with *)
3. Click "Save Case" button
4. Case will be added to the database

### Edit Case
1. Search for a case
2. Click "Edit" button on case details
3. Update the required fields
4. Click "Update Case" button

### Delete Case
1. Search for a case
2. Click "Delete" button on case details
3. Confirm the deletion
4. Case will be permanently removed

## Case Types Available
- Civil
- Criminal
- Family
- Commercial
- Constitutional

## Case Status Options
- Active
- Under Trial
- Settled
- Closed
- Pending

## Troubleshooting

### Cannot connect to database
- Make sure MySQL is running in XAMPP
- Check database credentials in `config.php`
- Verify database name is correct

### Cannot access the application
- Make sure Apache is running in XAMPP
- Check if files are in correct folder: `htdocs\court_case_management\`
- Try: `http://localhost/court_case_management/index.php`

### Blank page or errors
- Check Apache error logs in XAMPP
- Make sure PHP is enabled
- Verify all files are uploaded correctly

## Security Notes

- Change the default admin password after first login
- Use strong passwords for production environment
- Never expose database credentials
- Regular backup of database is recommended

## Technologies Used

- **Frontend:** HTML5, CSS3
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Server:** Apache (via XAMPP)

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Microsoft Edge
- Safari

## Support

For issues or questions, please check:
1. XAMPP is running properly
2. Database is imported correctly
3. Files are in correct location
4. PHP and MySQL versions are compatible

---

**Developed for Court Case Management**
**Version 1.0**
