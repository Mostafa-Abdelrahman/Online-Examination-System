# Online Examination System

## Table of Contents
- [Overview](#overview)
- [Screenshots](#screenshots)
- [Features](#features)
- [System Architecture](#system-architecture)
- [Backend Structure](#backend-structure)
- [Frontend Structure](#frontend-structure)
- [Database Schema](#database-schema)
- [Setup Instructions](#setup-instructions)
- [API Overview](#api-overview)
- [Security Considerations](#security-considerations)
- [Contributing](#contributing)
- [License](#license)

---

## Overview

**Online Examination System** is a full-stack educational management platform designed for universities and schools. It supports online exam creation, management, grading, and analytics for students, doctors (instructors), and administrators. The system is built with a modern React frontend and a robust Laravel backend API.

---

## Screenshots

Below are key screens of the system, organized by user role and workflow. Each section includes a detailed flow and a reference to the main content visible on the screen, based on the alt text for each image.

---

### Authentication & Account Management

#### Login Page
<img src="screens/Screenshot 2025-07-08 093127.png" width="400" alt="Login Page" />
**Flow & Content:**
- Users (students, doctors, admins) enter their credentials to access the system.
- Options for password reset and registration are available.
**Screen Content Reference:**
- Login form (email, password)
- Login button
- Forgot password link
- Register link
- System branding/logo

#### Register Page
<img src="screens/Screenshot 2025-07-08 093816.png" width="400" alt="register " />
**Flow & Content:**
- New users create an account by providing required details.
- Role selection (student, doctor, admin) may be present.
**Screen Content Reference:**
- Registration form (name, email, password, role, etc.)
- Register button
- Link to login

#### Forgot Password Page
<img src="screens/Screenshot 2025-07-08 093826.png" width="400" alt="forget my password " />
**Flow & Content:**
- Users request a password reset by entering their email.
- Link to return to login is provided.
**Screen Content Reference:**
- Email input field
- Submit/reset button
- Link to login

---

### Student Experience

#### Student Dashboard
<img src="screens/Screenshot 2025-07-08 093442.png" width="400" alt="student dashboard" />
**Flow & Content:**
- Students see an overview of their courses, upcoming exams, and notifications.
**Screen Content Reference:**
- List of enrolled courses
- Upcoming exams
- Notifications panel

#### Available Exams
<img src="screens/Screenshot 2025-07-08 093458.png" width="400" alt="available exams from student dashboard" />
**Flow & Content:**
- Students view and start available exams.
**Screen Content Reference:**
- List of available exams (exam name, date, status)
- Start exam button

#### Exam Result
<img src="screens/Screenshot 2025-07-08 093533.png" width="400" alt="exam result from student dashboard" />
**Flow & Content:**
- Students view their results and feedback for completed exams.
**Screen Content Reference:**
- Exam result summary (score, grade, feedback)
- List of past results

#### Student Settings
<img src="screens/Screenshot 2025-07-08 093621.png" width="400" alt="student settings " />
**Flow & Content:**
- Students manage their account and notification preferences.
**Screen Content Reference:**
- Settings form (profile, password, notifications)
- Save/cancel buttons

#### Student Schedule
<img src="screens/Screenshot 2025-07-08 093517.png" width="400" alt="schualde pages from student dahsboard" />
**Flow & Content:**
- Students view their exam and class schedule in a calendar format.
**Screen Content Reference:**
- Calendar view
- List of scheduled exams/events
- Add/view event button

---

### Doctor (Instructor) Experience

#### Doctor Dashboard
<img src="screens/Screenshot 2025-07-08 093201.png" width="400" alt="doctor Dashboard" />
**Flow & Content:**
- Doctors see an overview of their assigned courses, quick actions, and analytics.
**Screen Content Reference:**
- List of assigned courses
- Quick actions (add question, add exam, grading, reports, settings)
- Analytics widgets

#### Add Question
<img src="screens/Screenshot 2025-07-08 093218.png" width="400" alt="add question from docotr Dashboard" />
**Flow & Content:**
- Doctors add new questions to the question bank for use in exams.
**Screen Content Reference:**
- Add question form (question text, type, options)
- Save/cancel buttons
- List of existing questions

#### Add Exam
<img src="screens/Screenshot 2025-07-08 093243.png" width="400" alt="add exam from doctor Dashboard" />
**Flow & Content:**
- Doctors create and schedule new exams for their courses.
**Screen Content Reference:**
- Add exam form (exam name, course, date, duration)
- Save/cancel buttons
- List of existing exams

#### Grading Management
<img src="screens/Screenshot 2025-07-08 093349.png" width="400" alt="grading  Management from docotr dashboard" />
**Flow & Content:**
- Doctors review and assign grades to student submissions.
**Screen Content Reference:**
- List of student submissions
- Grading form (score, feedback)
- Save/submit grade button

#### Reports & Analysis
<img src="screens/Screenshot 2025-07-08 093404.png" width="400" alt="report and analysis  from docotr dashboard" />
**Flow & Content:**
- Doctors view analytics and reports on exam and student performance.
**Screen Content Reference:**
- Exam statistics (average score, pass rate)
- Performance charts/graphs
- Export/report buttons

#### Doctor Settings
<img src="screens/Screenshot 2025-07-08 093418.png" width="400" alt="settings  from docotr dashboard" />
**Flow & Content:**
- Doctors configure their account and course preferences.
**Screen Content Reference:**
- Settings form (profile, notification, preferences)
- Save/cancel buttons

---

### Admin Experience

#### Admin Dashboard Details
<img src="screens/Screenshot 2025-07-08 093649.png" width="400" alt="admin dashboard Details" />
**Flow & Content:**
- Admins view system statistics and access management tools.
**Screen Content Reference:**
- System statistics cards
- Management panels (users, courses, majors)

#### Major List
<img src="screens/Screenshot 2025-07-08 093702.png" width="400" alt="major  List in admin dahsboard" />
**Flow & Content:**
- Admins manage the list of academic majors.
**Screen Content Reference:**
- List of majors (name, code, status)
- Add/edit/delete major buttons

#### Student List
<img src="screens/Screenshot 2025-07-08 093724.png" width="400" alt="student List from admin dashboard" />
**Flow & Content:**
- Admins manage the list of students in the system.
**Screen Content Reference:**
- List of students (name, email, status)
- Add/edit/delete student buttons

#### System Statistics
<img src="screens/Screenshot 2025-07-08 093736.png" width="400" alt="system staticss from admin dashfaboard" />
**Flow & Content:**
- Admins view overall system statistics and analytics.
**Screen Content Reference:**
- System statistics cards (users, courses, exams)
- Analytics charts

---

## Features

### Authentication & Authorization
- Secure JWT-based authentication (login, registration, password reset)
- Role-based access control (Admin, Doctor, Student)
- Session management and user profile

### Student Portal
- View and enroll in available courses
- View upcoming and past exams
- Take online exams (MCQ, written, programming, essay)
- Auto-save and timed exam sessions
- View grades, results, and feedback
- Access course materials and announcements
- Receive notifications (exam reminders, grades, announcements)

### Doctor (Instructor) Portal
- Manage assigned courses
- Create and manage question banks (MCQ, written, etc.)
- Create, schedule, and publish exams
- Grade student submissions (auto/manual)
- View student performance analytics
- Post course materials and announcements
- Manage exam schedules and availability

### Admin Portal
- Full user management (CRUD for students, doctors, admins)
- Course and major management
- Assign doctors to courses and enroll students
- System statistics and health monitoring
- Manage system-wide settings and reports

### General System
- Real-time notifications and dashboard analytics
- Secure file uploads for avatars and materials
- Audit trails and activity logs
- Responsive, modern UI/UX

---

## System Architecture

### High-Level Diagram

```
+-----------+    HTTP/REST    +-----------+    Database     +-----------+
| Frontend  | <=============> |  Backend  | <=============> | Database  |
|  (React)  |      API        | (Laravel) |     Queries     |  (MySQL)  |
+-----------+                 +-----------+                 +-----------+
```

- **Frontend**: React + TypeScript (Vite), communicates via REST API
- **Backend**: Laravel 10.x, RESTful API, JWT authentication, role-based access
- **Database**: MySQL (or PostgreSQL), normalized schema with audit and security

### User Roles
- **Student**: Take exams, view grades, enroll in courses
- **Doctor**: Manage courses, create exams/questions, grade
- **Admin**: Manage users, courses, system settings

---

## Backend Structure

- **Framework**: Laravel 10.x (PHP)
- **API**: RESTful, stateless, JWT authentication (Laravel Sanctum)
- **Key Modules**:
  - `AuthController`: Registration, login, password reset, profile
  - `StudentController`: Course enrollment, exam taking, results
  - `DoctorController`: Course/exam/question management, grading
  - `AdminController`: System stats, user/course/major management
  - `CourseController`, `ExamController`, `QuestionController`, `ScheduleController`, etc.
- **Services**: Notification, file storage, analytics
- **Middleware**: Role-based access, authentication, CORS
- **Testing**: PHPUnit feature and unit tests

---

## Frontend Structure

- **Framework**: React + TypeScript (Vite)
- **Key Directories**:
  - `src/components/`: UI components (auth, admin, doctor, student, exams, courses, etc.)
  - `src/pages/`: Page-level views for each role
  - `src/services/`: API service layer (auth, course, exam, etc.)
  - `src/contexts/`: Global state (e.g., AuthContext)
  - `src/hooks/`: Custom React hooks
  - `src/types/`: TypeScript type definitions
- **Styling**: Tailwind CSS, custom styles
- **State Management**: React Context, hooks
- **Routing**: React Router

---

## Database Schema

### Entity Relationship Diagram (ERD)

```
+-----------+       +-----------+       +-----------+
| PROFILES  |<----->| COURSES   |<----->| QUESTIONS |
+-----------+       +-----------+       +-----------+
| id (PK)   |       | id (PK)   |       | id (PK)   |
| name      |       | name      |       | text      |
| email     |       | code      |       | type      |
| role      |       | ...       |       | ...       |
+-----------+       +-----------+       +-----------+
      |                   |                   |
      v                   v                   v
+-----------+       +-----------+       +-----------+
|DOCTOR_CRS |       |STUDENT_CRS|       |  CHOICES  |
+-----------+       +-----------+       +-----------+
| doctor_id |       | student_id|       | question_id|
| course_id |       | course_id |       | text      |
+-----------+       +-----------+       |is_correct |
                                         +-----------+
```

### Main Tables
- **profiles**: All users (admin, doctor, student)
- **courses**: Course catalog
- **doctor_courses**: Doctor-course assignments
- **student_courses**: Student enrollments
- **questions**: Question bank
- **choices**: MCQ options
- **exams**: Exam definitions
- **exam_questions**: Questions in each exam
- **student_exams**: Student exam sessions
- **student_exam_answers**: Answers per question
- **grades**: Final grades and feedback

### See [`learn-exam-arena/docs/database-schema.md`](learn-exam-arena/docs/database-schema.md) for full schema and SQL.

---

## Setup Instructions

### Prerequisites
- Node.js (v18+)
- PHP (8.1+)
- Composer
- MySQL or PostgreSQL
- [Optional] Docker

### Backend (Laravel API)
1. `cd education-management-api`
2. `cp .env.example .env` and configure DB credentials
3. `composer install`
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. `php artisan serve` (or use Valet/Homestead/Docker)

### Frontend (React)
1. `cd learn-exam-arena`
2. `npm install`
3. `npm run dev`

### Access
- Frontend: [http://localhost:5173](http://localhost:5173)
- Backend API: [http://localhost:8000/api](http://localhost:8000/api)

---

## API Overview

See [`learn-exam-arena/docs/backend-system-design.md`](learn-exam-arena/docs/backend-system-design.md) for full API details.

### Authentication
- `POST /api/login` — Login
- `POST /api/register` — Register
- `POST /api/logout` — Logout
- `GET /api/user` — Current user info

### Student
- `GET /api/courses` — My courses
- `GET /api/exams/available` — Available exams
- `POST /api/exams/{id}/start` — Start exam
- `POST /api/exams/{id}/submit` — Submit exam
- `GET /api/results` — My results

### Doctor
- `GET /api/doctor/courses` — My courses
- `GET /api/doctor/questions` — My questions
- `POST /api/doctor/exams` — Create exam
- `GET /api/exams/{id}/submissions` — Student submissions
- `POST /api/answers/{id}/grade` — Grade answer

### Admin
- `GET /api/admin/stats` — System stats
- `GET /api/admin/users` — User management
- `GET /api/admin/courses` — Course management

---

## Security Considerations
- JWT tokens with expiration
- Role-based access control (RBAC)
- Secure password hashing (bcrypt)
- Input validation and sanitization
- SQL injection and XSS protection
- Audit trails and activity logs
- Rate limiting and CSRF protection

---

## Contributing

Contributions are welcome! Please fork the repo and submit a pull request. For major changes, open an issue first to discuss what you would like to change.

---

## License

This project is licensed under the MIT License.
