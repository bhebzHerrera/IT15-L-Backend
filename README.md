# IT15-L Enrollment System (Backend)

Laravel 12 REST API for student enrollment, courses, dashboard analytics, and authentication.

## Project Overview

This backend provides:

- Authentication using Laravel Sanctum
- Student, Course, Enrollment, and School Day APIs
- Dashboard analytics and reporting endpoints
- Database seeders for demo data

## Tech Stack

- PHP 8.2+
- Laravel 12
- Laravel Sanctum
- MySQL (recommended) or PostgreSQL
- Composer

## Prerequisites

Install the following before setup:

- PHP 8.2 or newer
- Composer 2+
- MySQL 8+ (or PostgreSQL)

## Quick Start

Run these commands from this backend folder:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Backend will run at:

```text
http://127.0.0.1:8000
```

## Environment Configuration

`.env.example` is included and contains required variable names.

Important variables:

- `APP_URL`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `FORCE_HTTPS`
- `REQUIRE_HTTPS_FOR_API`
- `SANCTUM_TOKEN_EXPIRATION`

## API Authentication

Default seeded admin account:

- Email: `admin@example.com`
- Password: `admin12345`

Login endpoint:

```text
POST /api/login
```

Use the returned bearer token for protected endpoints.

## API Endpoint Groups

All routes are registered in `routes/api.php`.

- Authentication: `/login`, `/logout`, `/me`
- Dashboard and reports: `/dashboard`, `/dashboard/summary`, `/dashboard/trends`, `/dashboard/calendar`, `/enrollments/pipeline`, `/reports/cards`
- Students (REST): `/students`
- Courses (REST): `/courses`
- Enrollments: `/enrollments`
- School Days (REST): `/school-days`

## API Documentation (Endpoints and Expected Responses)

Base URL:

```text
http://127.0.0.1:8000/api
```

### Authentication

1. POST /login
- Auth required: No
- Purpose: Authenticate user and issue Sanctum token.
- Request body:

```json
{
	"email": "admin@example.com",
	"password": "admin12345",
	"device_name": "frontend-client"
}
```

- Success response (200):

```json
{
	"success": true,
	"message": "Login successful.",
	"user": { "id": 1, "email": "admin@example.com" },
	"token": "1|plain-text-token",
	"token_type": "Bearer",
	"data": {
		"user": { "id": 1, "email": "admin@example.com" },
		"token": "1|plain-text-token",
		"token_type": "Bearer"
	}
}
```

- Error response (401):

```json
{
	"success": false,
	"message": "Invalid credentials.",
	"errors": {
		"email": ["The provided credentials are incorrect."]
	}
}
```

2. POST /logout
- Auth required: Yes (Bearer token)
- Purpose: Revoke current access token.
- Success response (200):

```json
{
	"success": true,
	"message": "Logout successful.",
	"data": null
}
```

3. GET /me
- Auth required: Yes (Bearer token)
- Purpose: Return authenticated user profile.
- Success response (200):

```json
{
	"success": true,
	"message": "Authenticated user profile.",
	"data": { "id": 1, "email": "admin@example.com" }
}
```

### Dashboard and Reports

All endpoints in this section require Bearer token authentication.

1. GET /dashboard
- Purpose: Frontend dashboard payload (cards, trends, distributions, activities).
- Success response (200):

```json
{
	"stats": [{ "label": "Students Enrolled", "value": 120, "trend": "Current academic year" }],
	"enrollmentTrend": [{ "month": "Jan", "enrollees": 20 }],
	"programDistribution": [{ "name": "Computer Science", "value": 45 }],
	"attendancePatterns": [{ "day": "Mar 10", "attendance": 94.5 }],
	"capacityData": [{ "code": "CS101", "slots": 50, "enrolled": 34 }],
	"recentEnrollments": [{ "id": 1, "student": "Juan Dela Cruz", "program": "Computer Science", "year": 2, "status": "enrolled" }],
	"activities": [{ "id": 1, "title": "Department Meeting", "description": "Event", "time": "Mar 15, 2026" }]
}
```

2. GET /dashboard/summary
- Purpose: Summary metrics for dashboard cards.
- Success response (200):

```json
{
	"success": true,
	"message": "Dashboard summary loaded successfully.",
	"data": {
		"students_enrolled": 120,
		"courses_offered": 30,
		"active_courses": 27,
		"today_attendance_rate": 95.2,
		"average_attendance_rate": 93.75,
		"events_this_month": 4
	}
}
```

3. GET /dashboard/trends
- Purpose: Enrollment, attendance, and department trend breakdown.
- Success response (200):

```json
{
	"success": true,
	"message": "Dashboard trends loaded successfully.",
	"data": {
		"enrollment_trend": [{ "month": "2026-03", "total": 20 }],
		"attendance_trend": [{ "month": "2026-03", "average_attendance": 93.2 }],
		"department_breakdown": [{ "department": "Computer Science", "total": 45 }]
	}
}
```

4. GET /dashboard/calendar
- Purpose: Calendar entries with optional filters.
- Query params: from, to, day_type (class_day | holiday | event)
- Success response (200):

```json
{
	"success": true,
	"message": "Calendar data loaded successfully.",
	"data": [{ "id": 1, "date": "2026-03-15", "day_type": "event" }],
	"meta": { "from": "2026-03-01", "to": "2026-03-31" }
}
```

5. GET /enrollments/pipeline
- Purpose: Enrollment pipeline stages for charts.
- Success response (200):

```json
[
	{ "stage": "Applied", "count": 120 },
	{ "stage": "Document Check", "count": 90 },
	{ "stage": "For Interview", "count": 48 },
	{ "stage": "Approved", "count": 85 }
]
```

6. GET /reports/cards
- Purpose: KPI cards for reporting panel.
- Success response (200):

```json
[
	{ "label": "Average Processing Time", "value": "1.8 days" },
	{ "label": "System Uptime", "value": "99.4%" },
	{ "label": "Retention Projection", "value": "92.1%" },
	{ "label": "At-Risk Students", "value": "6" }
]
```

### Students

All endpoints in this section require Bearer token authentication.

1. GET /students
- Purpose: List students with optional filtering, sorting, and pagination.
- Query params: search, department, status, gender, year_level, sort_by, sort_dir, per_page, with_meta
- Success response (200, default without metadata):

```json
[
	{ "id": 1, "name": "Juan Dela Cruz", "program": "Computer Science", "year": 2, "status": "enrolled" }
]
```

2. GET /students?with_meta=1
- Purpose: Return full records with pagination metadata.
- Success response (200):

```json
{
	"success": true,
	"message": "Students retrieved successfully.",
	"data": [{ "id": 1, "student_number": "S000001" }],
	"meta": { "current_page": 1, "per_page": 15, "total": 120, "last_page": 8 }
}
```

3. POST /students
- Purpose: Create a new student.
- Success response (201):

```json
{
	"success": true,
	"message": "Student created successfully.",
	"data": { "id": 121, "student_number": "S000121" }
}
```

4. GET /students/{id}
- Purpose: Get one student with enrolled courses.

5. PUT/PATCH /students/{id}
- Purpose: Update student.

6. DELETE /students/{id}
- Purpose: Delete student.

### Courses

All endpoints in this section require Bearer token authentication.

1. GET /courses
- Purpose: List courses.
- Query params: student_id, search, department, is_active, sort_by, sort_dir, per_page, with_meta
- Note: If student_id is provided, only courses matching the selected student's department are returned.
- Success response (200, default without metadata):

```json
[
	{ "code": "CS101", "title": "Intro to Computing", "slots": 50, "enrolled": 35 }
]
```

2. GET /courses?with_meta=1
- Success response (200):

```json
{
	"success": true,
	"message": "Courses retrieved successfully.",
	"data": [{ "id": 1, "course_code": "CS101" }],
	"meta": { "current_page": 1, "per_page": 15, "total": 30, "last_page": 2 }
}
```

3. POST /courses
- Purpose: Create a new course.
- Success response (201):

```json
{
	"success": true,
	"message": "Course created successfully.",
	"data": { "id": 31, "course_code": "CS205" }
}
```

4. GET /courses/{id}
- Purpose: Get one course with enrolled students.

5. PUT/PATCH /courses/{id}
- Purpose: Update course.

6. DELETE /courses/{id}
- Purpose: Delete course.

### Enrollments

All endpoints in this section require Bearer token authentication.

1. GET /enrollments
- Purpose: List students with their enrolled courses.
- Success response (200):

```json
{
	"success": true,
	"message": "Enrollments retrieved successfully.",
	"data": [{ "id": 1, "student_number": "S000001", "courses": [{ "id": 1, "course_code": "CS101" }] }]
}
```

2. POST /enrollments
- Purpose: Enroll a student in a course.
- Request body:

```json
{
	"student_id": 1,
	"course_id": 2
}
```

- Success response (201):

```json
{
	"success": true,
	"message": "Enrollment created successfully.",
	"data": { "student_id": 1, "course_id": 2 }
}
```

- Common error responses:
	- 422 for department mismatch or capacity reached
	- 409 for duplicate enrollment

3. DELETE /enrollments
- Purpose: Remove an enrollment.
- Request body:

```json
{
	"student_id": 1,
	"course_id": 2
}
```

- Success response (200):

```json
{
	"success": true,
	"message": "Enrollment removed successfully.",
	"data": null
}
```

### School Days

All endpoints in this section require Bearer token authentication.

1. GET /school-days
- Purpose: List school day records with optional date and type filters.

2. POST /school-days
- Purpose: Create school day or event record.

3. GET /school-days/{id}
- Purpose: View one school day record.

4. PUT/PATCH /school-days/{id}
- Purpose: Update school day record.

5. DELETE /school-days/{id}
- Purpose: Delete school day record.

General success response format (for most CRUD endpoints):

```json
{
	"success": true,
	"message": "Operation completed successfully.",
	"data": {}
}
```

General validation error format (422):

```json
{
	"message": "The given data was invalid.",
	"errors": {
		"field_name": ["Validation message"]
	}
}
```

## Useful Commands

```bash
php artisan test
php artisan db:seed
php artisan route:list
```


