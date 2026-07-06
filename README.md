# KiddieLearn

> A web-based **Preschool Learning Management System** developed as a capstone project to support preschool education through interactive learning and effective communication between teachers and parents.

---

## 📖 Project Overview

KiddieLearn is a full-stack web application developed to support preschool education through an interactive learning management system.

The platform enables teachers to create and manage educational content, upload worksheets, monitor student progress, and communicate with parents. Parents can access their child's learning materials, track academic progress, and stay connected with teachers through a dedicated dashboard.

The application focuses on providing a responsive, child-friendly interface that encourages engaging learning experiences while simplifying classroom management.

---

## Project Highlights

- Teacher Dashboard
- Parent Dashboard
- Role-Based Authentication
- Interactive Learning Modules
- Worksheet Management
- Student Progress Tracking
- Messaging System
- Responsive Child-Friendly Interface

---

<h2>Screenshot Gallery</h2>

<p align="center">
  <img src="screenshots/Home%20Page.png" width="45%">
  <img src="screenshots/Login%20Page.png" width="45%">
</p>

<p align="center">
  <img src="screenshots/Teacher%20Dashboard%20Page.png" width="45%">
  <img src="screenshots/Parent%20Dashboard%20Page.png" width="45%">
</p>

<p align="center">
  <img src="screenshots/Registration%20Page.png" width="45%">
  <img src="screenshots/Manage%20lesson%20Page.png" width="45%">
</p>

<p align="center">
  <img src="screenshots/Child%20Progress%20Page.png" width="45%">
  <img src="screenshots/Child%20Worksheet%20Page.png" width="45%">
</p>

<p align="center">
  <img src="screenshots/Upload%20Activity%20Page.png" width="45%">
  <img src="screenshots/Chat%20System.png" width="45%">
</p>

---

### Technologies Used

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

---

## Key Features

| Feature | Description |
|---------|-------------|
| Role-Based Authentication | Separate access for Teachers and Parents |
| Teacher Dashboard | Manage lessons, worksheets, activities, and students |
| Parent Dashboard | Monitor child progress and access learning materials |
| Learning Modules | Alphabet, Numbers, Colors, and Shapes |
| Worksheet Management | Upload and distribute worksheets |
| Student Progress | Track learning progress and grades |
| Messaging | Communication between teachers and parents |

---

## Project Information

| Category | Details |
|----------|---------|
| Project Type | Capstone Project |
| Application | Preschool Learning Management System |
| Development | Full-Stack Web Application |
| Frontend | HTML5, CSS3, Bootstrap, JavaScript |
| Backend | PHP |
| Database | MySQL |
| Authentication | Role-Based Login |
| Status | Completed |

---

# 🚀 Installation

## Prerequisites

Before running the project, make sure you have:

- XAMPP (Apache & MySQL)
- MySQL
- Git (optional)

---

## Clone the Repository

```bash
git clone https://github.com/nicoamante9/kiddielearn.git
```

Move the project into your XAMPP `htdocs` directory if necessary.

---

## Import the Database

1. Start **Apache** and **MySQL** using XAMPP.
2. Open **phpMyAdmin**.
3. Create a database named:

```text
kiddielearn
```

4. Import:

```text
database/kiddielearn.sql
```

---

## Configure Database Connection

Open:

```text
db.php
```

Update the database settings if needed:

```php
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "kiddielearn";
```

---

## Run the Project

Open your browser and navigate to:

```text
http://localhost/kiddielearn
```

---

## Default Environment

| Software | Version |
|----------|---------|
| PHP |  |
| MySQL |  |
| Apache | XAMPP |
| Browser | Google Chrome (Recommended) |

---

## Project Structure

```text
kiddielearn/
├── assets/
├── css/
├── database/
│   └── kiddielearn.sql
├── img/
├── js/
├── lib/
├── screenshots/
├── teacher/
├── parent/
├── README.md
├── db.php
└── index.php
```

---

## My Contributions

Although KiddieLearn was developed as a capstone project, I independently designed and developed the application, including:

- Full-stack web development using PHP and MySQL
- Database design and implementation
- Role-based authentication system
- Teacher and Parent dashboards
- CRUD operations
- Learning modules
- Student progress tracking
- Messaging functionality
- Responsive user interface using Bootstrap

---

## Future Improvements

- Email notifications
- Admin dashboard
- Attendance monitoring
- Mobile application
- Learning analytics
- Online assessments
- Cloud deployment

---

## 📄 License

This project is intended for educational and portfolio purposes.