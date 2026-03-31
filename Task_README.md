# 🎯 School Attendance Management System (ZKTeco K60 + Laravel + Python Sync)

## 📌 Project Overview

Build a complete school attendance system using Laravel (backend + admin panel) and Python (device sync) for a biometric device (ZKTeco K60). The system must support manual data sync (admin-triggered), class-wise timetable, role-based attendance rules, and automatic late/absent calculation.

---

## 🧠 Core Requirements

### 1. Device Integration

- Device: ZKTeco K60
- Protocol: TCP/IP (port 4370)
- Data should NOT require PC to be always ON
- Admin will manually trigger data sync from the panel
- Python script will pull attendance logs from the device and send to Laravel API

---

### 2. User Roles

- Admin
- Teacher
- Student

---

### 3. Class System

- Classes like: Nursery, Class 1, Class 2, etc.
- Each student must belong to a class
- Teachers do NOT require class mapping

---

## 🕒 Timetable System (Critical Feature)

### Requirements:

- Separate timing for:
  - Students (class-wise)
  - Teachers (global)

- Day-wise schedule (Sunday–Saturday)
- Fields:
  - in_time
  - late_time
  - out_time

### Example:

- Nursery: 08:00 – 11:00
- Class 1: 08:00 – 13:00
- Teacher: 07:45 – 13:30

---

## 🗄️ Database Design

### users

- id
- name
- role (admin/teacher/student)
- device_user_id (unique)
- class_id (nullable)
- phone

### classes

- id
- name

### time_tables

- id
- role (student/teacher)
- class_id (nullable)
- day (Sunday–Saturday)
- in_time
- late_time
- out_time

### attendances

- id
- user_id
- date
- check_in
- check_out
- status (Present, Late, Absent)
- early_leave (boolean)

### sync_logs

- id
- last_sync_time

---

## 🔄 Attendance Sync Flow

### Manual Sync Process:

1. Admin clicks "Sync Device Data"
2. Python script runs
3. Script connects to device
4. Fetches attendance logs
5. Filters new logs (based on last_sync_time)
6. Sends data to Laravel API

---

## 🐍 Python Sync Script Requirements

- Use zk library
- Connect via IP (e.g., 192.168.1.201)
- Fetch attendance logs
- Format:
  - device_user_id
  - timestamp

- Send via POST request to Laravel API endpoint

---

## 🌐 Laravel API

### Endpoint:

POST /api/attendance-sync

### দায়িত্ব:

- Match device_user_id with user
- Create or update attendance record
- Set check_in and check_out
- Trigger status calculation

---

## 🧮 Attendance Calculation Logic

### Steps:

1. Identify user role
2. If student → get class_id
3. Detect day (Sunday, Monday...)
4. Fetch matching timetable

### Rules:

- If no check_in → Absent
- If check_in > late_time → Late
- Else → Present
- If check_out < out_time → early_leave = true

---

## 🖥️ Admin Panel Features

### Dashboard:

- Today উপস্থিত
- Late count
- Absent count

### Attendance Management:

- Filter by date, class, role
- View logs

### Timetable Management:

- Add/Edit/Delete timetable
- Role ভিত্তিক + Class ভিত্তিক

### Class Management:

- Add/Edit classes

### User Management:

- ছাত্র/শিক্ষক CRUD

### Sync Button:

- Manual trigger
- Show success/fail message

---

## ⚙️ Automation (Optional)

- Windows Task Scheduler → run sync on PC startup

---

## 📊 Reports

- Daily রিপোর্ট
- Monthly রিপোর্ট
- Export: Excel / PDF

---

## 🔐 Security

- API authentication (token-based)
- Validate device_user_id
- Prevent duplicate entries (user_id + date unique)

---

## 🚀 Optional Advanced Features

- SMS alert for absent students
- Guardian notification
- Mobile responsive UI
- Role-based login सिस्टम

---

## 🧱 Tech Stack

- Laravel 12
- MySQL
- Bootstrap / jQuery (Admin Panel)
- Python (zk library)
- REST API

---

## 🎯 Final Goal

- Fully automated attendance system
- No need to keep PC always ON
- Accurate class-wise and role-wise attendance tracking
- Admin-controlled sync system

---

## 📌 Deliverables

- Full Laravel project (MVC)
- Migration + Seeder
- Python sync script
- Admin panel UI
- API integration
- Documentation

---

🐍 Python script full optimized version
🎨 Admin panel UI design - adminlte
