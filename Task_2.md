# 🚀 Smart School Attendance & Management System (Advanced Prompt)

## 📌 Project Overview

Build a production-ready, scalable Smart School Attendance & Management System using Laravel (backend + admin panel) and Python (device sync) integrated with ZKTeco K60 biometric devices.

The system must support:

- Manual data sync (admin-controlled)
- Multi-device support
- Class-wise and role-wise attendance rules
- Advanced attendance calculation (Late, Half-day, Early Leave, Missing Punch)
- Smart reporting and notification system
- Future SaaS scalability

---

## 🧠 Core System Goals

- No need to keep PC always ON
- Admin-triggered data sync
- Accurate attendance with multi-punch handling
- Flexible timetable (class + role + day ভিত্তিক)
- Fully automated attendance status calculation
- Scalable architecture for multiple schools (future SaaS)

---

## 🧩 Modules Overview

### 1. User Management

Roles:

- Admin
- Operator
- Teacher
- Student

Fields:

- name
- role
- device_user_id (unique)
- class_id (nullable)
- section_id (nullable)
- phone

---

### 2. Academic Structure

#### Classes

- Nursery, Class 1, Class 2...

#### Sections

- A, B, C

#### Shifts

- Morning / Day

---

### 3. Device Management

Support multiple devices.

Table: devices

- id
- name
- ip_address
- port (default 4370)
- location (Gate, Office)
- status (active/inactive)

---

## 🕒 Advanced Timetable System

Table: time_tables

Fields:

- role (student/teacher)
- class_id (nullable)
- day (Sunday–Saturday)
- in_time
- late_time
- out_time
- grace_time (minutes)
- half_day_time
- overtime_start (for teachers)

---

### Rules:

- Student → class ভিত্তিক timing
- Teacher → global timing
- Day-wise configuration required

---

## 📅 Attendance System

Table: attendances

Fields:

- user_id
- date
- check_in (first punch)
- check_out (last punch)
- status
- early_leave (boolean)
- working_hours

---

## 🧠 Attendance Logic (Advanced)

### Multi-Punch Handling:

- First punch = check_in
- Last punch = check_out
- Ignore middle punches

---

### Status Types:

- Present
- Late
- Absent
- Half Day
- Early Leave
- Missing Punch

---

### Calculation Rules:

1. Get timetable by:
   - role
   - class_id (if student)
   - day

2. Apply Grace Time:
   - late যদি check_in > (late_time + grace_time)

3. Half Day:
   - যদি check_in > half_day_time

4. Early Leave:
   - যদি check_out < out_time

5. Missing Punch:
   - only check_in, no check_out

---

## 🔄 Sync System

### Manual Sync (Primary)

- Admin clicks "Sync Device Data"

### Process:

1. Python script connects to device
2. Fetch logs
3. Filter নতুন logs (based on last_sync_time)
4. Send to Laravel API

---

### Sync নিরাপত্তা:

- Unique key: (device_user_id + timestamp)
- Prevent duplicate entry
- Retry failed sync

---

### Sync লগ:

Table: sync_logs

- last_sync_time
- total_records
- errors

---

## 🐍 Python Sync Requirements

- Use zk library
- Multi-device loop support
- Error handling (connection fail, timeout)
- লগ সংরক্ষণ

---

## 🌐 Laravel API

Endpoint:
POST /api/attendance-sync

Responsibilities:

- Validate user
- Save raw logs
- Process attendance
- Avoid duplicates

---

## 📊 Reporting System (Must Have)

### Reports:

#### Daily Report

- Present / Late / Absent

#### Monthly Report

- Total working days
- Present days
- Late count
- Absent count

#### Individual রিপোর্ট

- Student/Teacher history

#### Export:

- Excel
- PDF

---

## 📱 Notification System

### Trigger:

- Absent → Guardian SMS
- Late → Alert

### মাধ্যম:

- SMS Gateway (Bangladesh)
- Email

---

## 📆 Holiday Management

Table: holidays

- date
- title

Rule:

- Holiday দিনে absent count হবে না

---

## 📝 Leave Management

- Student/Teacher leave apply
- Admin approve

Rule:

- Approved leave = not absent

---

## 🔐 Role & Permission System

Roles:

- Admin (full access)
- Operator (attendance only)
- Teacher (view access)

Use permission middleware

---

## 📊 Dashboard (Smart)

Show:

- Today উপস্থিত
- Late
- Absent
- Device status
- Last sync time

---

## ⚠️ Edge Case Handling

- Device time mismatch → normalize with server time
- Multiple punches → handled
- Missing checkout → mark
- Invalid time (midnight punch) → ignore
- Offline device → alert

---

## 💾 Backup System

- Daily automatic DB backup

---

## 🔌 Hardware Considerations

- UPS for device
- Stable LAN connection

---

## 🚀 Future Expansion (SaaS Ready)

- Multi-school support
- Subscription system
- Mobile app
- Cloud sync

---

## 🧱 Tech Stack

- Laravel 10/11/12
- MySQL
- Bootstrap / jQuery
- Python (zk library)
- REST API

---

## 🎯 Final Outcome

A smart, scalable, and automated attendance system that:

- Works without always-on PC
- Handles real-world school complexity
- Provides accurate reporting
- Ready for SaaS expansion

---

student info import by excl or csv
if device has problem then menual attendacne add system
