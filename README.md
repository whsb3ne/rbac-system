# Role-Based Access Control (RBAC) System

A secure and lightweight Role-Based Access Control (RBAC) system built using PHP and MySQL.  
This system is designed for small organizations, clubs, institutions, and teams that need structured authentication, authorization, and user management.

## Features

### 1. User Authentication
- Secure registration and login  
- Password hashing using PHP's `password_hash()`  
- Login validation using prepared statements  
- Forgot password and reset password functionality  

### 2. Role-Based Access Control
- Three predefined roles:
  - **Admin**
  - **Editor**
  - **Viewer**
- Access control enforced on all protected pages  
- Only Admins can manage users and roles  

### 3. Activity & Audit Logging
- Login history tracking (IP address, time, user reference)
- Audit logs for sensitive operations (uploads, deletions, edits)
- Viewable by Admin only

### 4. Admin Dashboard
- View all users  
- Promote/demote user roles  
- Delete or deactivate accounts  
- Review login logs  
- View uploaded files  

### 5. File Uploads
- Secure file upload system  
- Upload logs and access control  
- Ability to review or delete files (Admin only)

### 6. System Setup
- Automatic database setup via `setup.php`  
- SQL schema included under `/sql/schema.sql`

---

## Technology Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8+ |
| Frontend | HTML5, CSS, JavaScript |
| Database | MySQL |
| Server | Apache (XAMPP recommended) |

---

## Installation Guide

### 1. Clone the repository
git clone https://github.com/whsb3ne/rbac-system.git

### 2. Move project into a server directory
Example for XAMPP:
C:\xampp\htdocs\rbac-systemC:\xampp\htdocs\rbac-system

### 3. Create the database
Create a database:
rbac_system

Import the SQL file from: sql/schema.sql

### 4. Configure database connection
Edit: includes/db.php

Set: 
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rbac_system";

### 5. Run the system
http://localhost/rbac-system/

---

## Default Roles
- **Admin** – Full control  
- **Editor** – Upload/edit content  
- **Viewer** – View-only access  

---

## Security Features
- Password hashing  
- SQL injection protection  
- Session-based access control  
- Activity logging  

---

## Project Structure
rbac-system/
│── index.php
│── login.php
│── register.php
│── dashboard.php
│── manage_users.php
│── upload.php
│── view_uploads.php
│── reset_password.php
│── logout.php
│── uploads/
│── css/
│── includes/
│ ├── db.php
│ ├── header.php
│ ├── footer.php
│ ├── audit.php
│ ├── functions.php
│── sql/
│ ├── schema.sql

---

## Author
**Benedict Kevin Karaya (whsb3ne)**  
Chuka University  
Diploma in Computer Science






