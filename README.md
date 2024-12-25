# Hanni's Clinic System

> **A PHP-MySQL application for managing patients, visits, and user authentication**  


**Developed by** Ashley John Baguio, Marc Nichol Riego, and Kurt Angelo Fabello
**Submitted to Prof. Eloihim Baculpo**  
> **Date: 12/25/2024**  


## Table of Contents

1. [Overview](#overview)  
2. [Requirements](#requirements)  
3. [How to Install & Configure XAMPP](#how-to-install--configure-xampp)  
4. [Project Setup (Hanni’s Clinic Files)](#project-setup-hannis-clinic-files)  
5. [Running the Application](#running-the-application)  
6. [Features Explained](#features-explained)  
7. [Common Troubleshooting](#common-troubleshooting)  
8. [FAQ](#faq)  
9. [Credits](#credits)  
10. [Final Thoughts](#final-thoughts)

---

## Overview

**Hanni's Clinic System** is a **web-based** application that handles:

- **User Authentication** (register, login, logout)  
- **Patient Management** (create, read, update, delete)  
- **Visit Management** for each patient (add visit, update visit, delete visit, view history)  
- **Searching & Pagination** of patient records  
- **Modern Interface** using **Bootstrap**, **FontAwesome**, and **SweetAlert2**  

This guide walks you through **everything** you need to do to get the system **up and running**, even if you’re a complete beginner with PHP or XAMPP.

---

## Requirements

1. **XAMPP** (or another Apache+PHP+MySQL environment). This guide assumes **XAMPP** on Windows.  
2. **PHP** version **7.4** or higher (XAMPP typically bundles a recent PHP).  
3. **A Web Browser** (Chrome, Firefox, Edge, etc.).  

---

## How to Install & Configure XAMPP

1. **Download XAMPP**  
   - Visit [https://www.apachefriends.org/](https://www.apachefriends.org/) and download XAMPP for Windows.  
   - **Run** the installer and follow the on-screen instructions.

2. **Open XAMPP Control Panel**  
   - After installation, find **XAMPP Control Panel** in your Start Menu or in `C:\xampp\xampp-control.exe`.  
   - Launch it.  

3. **Start Apache & MySQL**  
   - In the XAMPP Control Panel, click **Start** next to **Apache** (the web server) and **MySQL** (the database).  
   - Make sure both have **green** highlights or “Running” status.

---

## Project Setup (Hanni’s Clinic Files)

1. **Obtain the Project Files**  
   - Download or copy the **Hanni's Clinic System** folder (sometimes called `hanni_clinic` or `Hospital_System`) onto your computer.  

2. **Move the Folder into XAMPP’s `htdocs`**  
   - By default, **XAMPP** serves files from `C:\xampp\htdocs`.  
   - So place your entire project folder there. Example path:
     ```
     C:\xampp\htdocs\hanni_clinic\
     ```
   - When done, your folder might look like:
     ```
     C:\xampp\htdocs\hanni_clinic\...
       (assets, config, patients, public, templates, etc.)
     ```

3. **Create a MySQL Database**  
   - In your browser, go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin) to access phpMyAdmin.  
   - Click **New** on the left to create a new database.  
   - Name it something like `hanni_clinic_db` and click **Create**.

4. **Create the Tables**  
   - While in phpMyAdmin, select your `hanni_clinic_db` database.  
   - Click on the **SQL** tab, then paste the following (you can adjust if you have your own script):
     ```sql
     CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(50) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL
     );

     CREATE TABLE patients (
       id INT AUTO_INCREMENT PRIMARY KEY,
       firstname VARCHAR(100),
       lastname VARCHAR(100),
       age INT,
       gender VARCHAR(10),
       address VARCHAR(255),
       contact VARCHAR(20),
       admission_date DATE NOT NULL DEFAULT CURRENT_DATE
     );

     CREATE TABLE visits (
       id INT AUTO_INCREMENT PRIMARY KEY,
       patient_id INT NOT NULL,
       diagnosis TEXT NOT NULL,
       doctor_notes TEXT,
       visit_date DATE NOT NULL,
       FOREIGN KEY (patient_id) REFERENCES patients(id)
     );
     ```
   - Click **Go**.  
   - If successful, you’ll have **three** tables: `users`, `patients`, `visits`.

5. **Configure Database Credentials** (`connection.php`)  
   - Inside your project folder, open `config/connection.php` in a text editor.  
   - Update the `$servername`, `$username`, `$password`, and `$dbname` variables if needed. For most XAMPP setups:
     ```php
     <?php
     $servername = "localhost";
     $username   = "root";  // default XAMPP user
     $password   = "";      // XAMPP default is empty
     $dbname     = "hanni_clinic_db"; // the DB name you created

     $conn = new mysqli($servername, $username, $password, $dbname);
     if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
     }
     ?>
     ```
   - **Save** the file.

---

## Running the Application

1. **Ensure Apache & MySQL** are running in XAMPP Control Panel.  
2. **Open Your Browser** and navigate to:
http://localhost/hanni_clinic/public/login.php

- Adjust `hanni_clinic` if your folder name differs.

3. **Register a New Account** (if no users exist yet)  
- Click **Create an account**, enter a **username** and **password**.  
- If everything’s correct, you’ll see a success message.  

4. **Log In**  
- Enter your **username** and **password**.  
- If successful, you’ll be redirected to the **Patients** page (`patients/view.php`).

5. **Explore the Features**:

- **Add Patient**:  
  - From the main patients list, click **Add Patient**.  
  - Fill in first name, last name, etc.  
  - The patient record will appear in the table.  
- **View** patient details:  
  - Click the **View** button to see a quick summary in a pop-up modal.  
- **Visits**:  
  - Click the **Visits** button to see or add visits for a patient.  
  - You can **Add**, **Edit**, or **Delete** a visit.  
- **Search** patients:  
  - Type a name, address, or contact in the search bar to filter results.  
- **Pagination**:  
  - If there are more than 10 patients, you can navigate pages at the bottom.  
- **Update** a patient:  
  - Click **Update** to edit a patient’s info.  
- **Delete** a patient:  
  - Click **Delete** to remove a patient. A confirmation pop-up will appear (SweetAlert2).  

6. **Logout**  
- In the top-right nav (or however you’ve configured it), click **Logout** to end your session.  

---

## Features Explained

1. **User Authentication**  
- **Registration** creates a new record in the `users` table, **password-hashed** using `password_hash()`.  
- **Login** checks your username, then uses `password_verify()` to confirm.  
- **Logout** destroys the PHP session.

2. **Patients**  
- Each patient has `firstname`, `lastname`, `age`, `gender`, `address`, `contact`, and an `admission_date`.  
- The system ensures no duplicate records with the same name + contact if coded that way.  
- Searching is done with a simple `LIKE` query on first name, last name, address, or contact.

3. **Visits**  
- Tied to a `patient_id`.  
- Each visit stores `visit_date`, `diagnosis`, and `doctor_notes`.  
- AJAX calls (`add_visit.php`, `update_visit.php`, etc.) handle CRUD operations with pop-up modals for the user interface.

4. **Styling & UI**  
- **Bootstrap** for layout & components (cards, modals, tables).  
- **FontAwesome** for icons (e.g., user, trash, plus icon).  
- **SweetAlert2** for modern confirm dialogs & alerts.  
- **Custom CSS** for finishing touches (like gradient backgrounds for login/register pages).

---

## Common Troubleshooting

1. **404 Not Found**  
- Check your folder name in `htdocs`.  
- Ensure you typed `http://localhost/hanni_clinic/public/login.php` (or the correct folder name).  
- Confirm `logout.php` or other files have the correct **relative** path in the `<a>` or `<button>` link.

2. **Database Errors**  
- Make sure `connection.php` has the **correct** credentials.  
- Verify the table names are correct and exist in your database.

3. **Can’t Log In / Register**  
- Inspect the code in `login.php` or `register.php` to confirm the database queries.  
- Double-check the `users` table is properly set up with `username` as **UNIQUE**.

4. **Password Not Working**  
- The system uses `password_hash()` / `password_verify()`. Passwords are hashed, so you can’t manually read them in the DB.  
- If you forget, register a new user or manually reset the password field with a newly hashed value.

---

## FAQ

**1. Can I change the pagination size?**  
Yes, in `patients/view.php`, look for `$limit = 10;`. Adjust as needed.

**2. Where do I customize the homepage?**  
Inside `public/index.php`. You can add images, text, etc.

**3. How do I remove the login requirement?**  
Remove or comment out the `session_start()` checks and `header("Location: login.php");` lines. But that’s not recommended if you want security.

**4. Can I deploy this online?**  
Yes, upload the files to a **PHP**-enabled host, import your database, and update `connection.php` with the production DB info. Make sure `public/` is your public root, or adjust paths.

---

## Credits

Submitted to:
- **Prof. Eloihim Baculpo**  

Developers:  
- **Ashley John Baguio**  
- **Marc Nichol Riego**  
- **Kurt Angelo Fabello**

They created and tested this project to demonstrate a **basic clinic management system** using **PHP** and **MySQL** for the subject CMPSC 116 or Database Systems.

---

## Final Thoughts

You now have a complete **smol clinic management** web application that is:

- **Simple** enough for a local environment,  
- **Robust** enough to handle basic CRUD & authentication,  
- **Extendable** for appointments, advanced search, or analytics.

If you encounter any issues:

1. **Check** your folder paths & DB credentials,  
2. **Ensure** XAMPP is running,  
3. **Review** the code for any missing references or typos,  
4. Or contact one of the developers above (for real).

Enjoy using **Hanni's Clinic System**!

## License
MIT License

Copyright (c) 2024 Ashley John Baguio, Marc Nichol Riego, and Kurt Angelo Fabello.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Hanni's Clinic System"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
