# School Attendance Management System (ZKTeco Integrated)

[![Laravel Version](https://img.shields.io/badge/Laravel-v12.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-v8.3.x-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

An enterprise-grade, high-performance School Attendance Management System with native BIOMETRIC integration for ZKTeco devices. Built with **Laravel 12**, it provides real-time synchronization, advanced reporting, and a dynamic administrative control panel.

---

## 🌟 Key Features

### 🏢 Core Management

- **User & Role Management**: Multi-role support (Admin, Teacher, Staff, Student) with deep RBAC via Spatie.
- **Academic Structure**: Intelligent management of Classes, Sections, and Timetables.
- **Bulk Operations**: Secure batch deletion and processing for large datasets.

### 🧬 ZKTeco Biometric Sync

- **Automated Data Pull**: Seamless background synchronization with ZKTeco devices.
- **Manual Sync Engine**: Trigger real-time data fetch from the admin dashboard.
- **Sync Logging**: Comprehensive logs for tracking successful and failed device communication.

### 📊 Attendance & Smart Reporting

- **Automated Processing**: Smart calculation of Late, Missing Punch, and Half-day status.
- **Dynamic Dashboard**: Informative, SaaS-inspired overview with Chart.js visualization.
- **Advanced Exporting**: Stream-based Excel and CSV exports for Daily, Monthly, and Individual summaries.

### 🎨 Dynamic Branding System

- **Administrator Settings**: Change Site Title, Logo, Favicon, and Footer text directly from the UI.
- **Assets Management**: Automated public folder management for branding assets.
- **Modern UI**: Dark/Light mode support using AdminLTE 3.2, SweetAlert2 modals, and Inter typography.

---

## 🛠 Technology Stack

- **Backend**: Laravel 12 (PHP 8.3+)
- **Database**: MySQL / MariaDB
- **Frontend**: AdminLTE 3.2, Bootstrap 4, Chart.js
- **Icons & Modals**: FontAwesome 5/6, SweetAlert2
- **Permissions**: Spatie Laravel-Permission (v7.2+)
- **Integration**: Python Bridge for ZKTeco device communication

---

## 🚀 Installation Guide

Ensure you have **Composer**, **NPM/Bun**, and **MySQL** installed before proceeding.

### 1. Clone the Repository

```bash
git clone https://github.com/saifulislam07/school_attendance_management_stystem_zkteco.git
cd school_attendance_management_stystem_zkteco
```

### 2. Install Dependencies

```bash
composer install
npm install && npm run build
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

_Update `.env` with your database and ZKTeco bridge credentials._

### 4. Database Setup & Seeding

```bash
php artisan migrate --seed
```

_This will create the tables and populate the system with professional sample data._

### 5. Start the Application

```bash
php artisan serve
```

---

## 🔑 Administrative Access

Use the following default credentials for initial setup:

| Role            | Email             | Password   |
| :-------------- | :---------------- | :--------- |
| **Super Admin** | `admin@admin.com` | `password` |

---

## 🛡 Security & Best Practices

- **Data Integrity**: Foreign key constraints and manual purge logic for bulk operations.
- **UI Safety**: CSRF protection, mass-assignment guards, and SweetAlert2 confirmation dialogs.
- **Clean Architecture**: Repository-lite controller pattern with Blade components.

---

## 👨‍💻 Developer & Attribution

- **Developer**: Saiful Islam
- **Email**: [saiful.rana@gmail.com](mailto:saiful.rana@gmail.com)
- **Repository**: [GitHub Link](https://github.com/saifulislam07/school_attendance_management_stystem_zkteco)

---

> [!IMPORTANT]
> ### 🛡️ Biometric Synchronization Bridge (Private Feature)
> The **ZKTeco Synchronization Bridge (`sync_attendance.py`)** is not included in this public repository for security and commercial reasons. This script is required to communicate with physical ZKTeco devices and push data to the Laravel database.
>
> 📞 **How to get the Bridge?**
> If you need the synchronization bridge or professional setup assistance, please contact the developer via:
> - **WhatsApp**: [+8801916665832](https://wa.me/8801916665832)
> - **Email**: [saiful.rana@gmail.com](mailto:saiful.rana@gmail.com)
> - **LinkedIn**: [Saiful Islam](https://linkedin.com/in/saifulislam07)
> - **Cost**: Personal & Commercial licenses are available upon request.

---

> "Crafted with heart for modern educational institutions."
