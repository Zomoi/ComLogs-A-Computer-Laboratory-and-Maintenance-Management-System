<p align="center">
  <img src="—Pngtree—illustration of pixel notebook_4757613.png" width="100" alt="ComLogs Logo">
</p>

<h1 align="center">ComLogs</h1>

<p align="center">
  <em>
    <code>🖥️ A Computer Laboratory and Maintenance Management System 🖥️</code>
  </em>
</p>

<p align="center">
  <b>NT-3104 – Web Systems and Technologies, System Integration, System Architecture</b><br>
  Batangas State University – Alangilan Campus<br>
  <a href="https://github.com/BjorkRico">Rico, B-jork M.</a> — Full Stack 
</p>

<hr>

## 🔍 Table of Contents
- [📖 Project Overview](#-project-overview)
- [⚙️ Notable Features](#️-notable-features)
- [🌐 Technologies Used](#-technologies-used)
- [🚀 Instructions to Run Locally](#-instructions-to-run-locally)
- [💮 Acknowledgments](#-acknowledgments)

<hr>

## 📖 Project Overview

**ComLogs** is a **web-based management system** designed to streamline the monitoring, documentation, and maintenance of school computer laboratories at **Batangas State University**.  

The system centralizes critical data—such as computer inventory, peripheral devices, maintenance logs, and scheduled tasks—into a single, intuitive interface. It empowers **Administrators** to manage user accounts and oversee operations, while **Technicians** can update device statuses, log repairs, and plan preventive maintenance.

### ✅ Key Objectives:
- Maintain an accurate, real-time inventory of computers and peripherals  
- Reduce device downtime through organized maintenance tracking  
- Improve coordination between Admin and Technicians  
- Ensure accountability with PC-specific logs and scheduled tasks  
- Provide a secure, role-based access system

### 👥 Beneficiaries:
- **Admin**: Full system control, user management, and oversight  
- **Technicians**: Device monitoring, log updates, and maintenance execution

<hr>

## ⚙️ Notable Features

Aligned with the official project proposal (IT-314), ComLogs implements all 8 required modules:

1. **🔐 Login System**  
   Secure authentication for Admin and Technician roles. Credentials are hashed, and sessions protect all actions.

2. **📊 Dashboard**  
   Real-time overview of lab status: total computers, active units, under maintenance, and offline devices.

3. **🖨️ Device Function**  
   Manages **peripherals** (printers, projectors, routers) with support for **device dependencies** (e.g., a printer tied to PC-01).

4. **💻 Computers**  
   Full CRUD operations for computers: view/edit IP, MAC, location, and status. All data stored in MySQL.

5. **📝 Maintenance Logs (PC-Specific)**  
   Detailed history of issues, repairs, and technician assignments—linked to individual computers.

6. **📅 Maintenance Schedule**  
   Plan future tasks for **computers or peripherals**, assign technicians, and view upcoming activities in a clean table (calendar-style).

7. **👥 User Management**  
   Admin can **add technician accounts** with email and password. Edit name, email, and status. (Admin accounts are protected.)

8. **🚪 Logout System**  
   Secure session termination to prevent unauthorized access.

<hr>

## 🌐 Technologies Used

- **Frontend**: HTML5, CSS3 (with custom gradients & responsive design), JavaScript (tab navigation)
- **Backend**: PHP 8.2 (procedural, with PDO for security)
- **Database**: MySQL (relational schema with foreign keys and cascading deletes)
- **Server**: Apache (via XAMPP)
- **Security**: `password_hash()`, `htmlspecialchars()`, prepared statements, session-based auth
- **Design Philosophy**: Clean, light-blue gradient UI with rounded cards, soft shadows, and intuitive workflows

<hr>

## 🚀 Instructions to Run Locally

### Prerequisites
- XAMPP (or any Apache + MySQL stack)
- Web browser (Chrome, Edge, Firefox)

### Steps
1. **Clone or download** this repository into your `htdocs` folder:
   ```bash
   git clone https://github.com/your-username/ComLogs.git
2. Start Apache and MySQL in XAMPP Control Panel.
3. Import the database:
Open phpMyAdmin (http://localhost/phpmyadmin)
Create a new database named comlogs_db
(Optional) The system auto-inserts sample data on first load.
4. Visit on your prefered browser : http://localhost/ComLogs/
5. Log in with:
Email: admin@batstate-u.edu.ph
Password: admin123

## 💮 Acknowledgments
💻 My laptop – For surviving countless PHP errors and XAMPP restarts
👨‍💻 Team ComLogs – Andrei, Hannah, Jenrick — thank you for the collaboration 
🐾 Ulap, Tala, Ulan, Sinag, and Nyebe – My fur babies who kept me sane during late-night coding 
