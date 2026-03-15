## IT15-L Backend

This document provides:

1. Detailed README.md with setup instructions
2. .env.example file with required environment variables
3. API documentation (endpoints and expected responses)

## Backend Setup

Run these commands in order:

```bash
cd laravel-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

After running, backend should be available at:

```text
http://127.0.0.1:8000
```

API base URL:

```text
http://127.0.0.1:8000/api
```

## .env.example Required Environment Variables

Make sure these required variables are present in `.env.example` and configured in `.env`:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=herrera_backend
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_TOKEN_EXPIRATION=120
FRONTEND_URL=http://localhost:5173
FORCE_HTTPS=false
REQUIRE_HTTPS_FOR_API=false
```

## API Documentation

Auth required for all endpoints except `/login`.

### Authentication

1. `POST /api/login`
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
	"token": "1|token...",
	"token_type": "Bearer"
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

2. `POST /api/logout`
- Success response (200):

```json
{
	"success": true,
	"message": "Logout successful.",
	"data": null
}
```

3. `GET /api/me`
- Success response (200):

```json
{
	"success": true,
	"message": "Authenticated user profile.",
	"data": { "id": 1, "email": "admin@example.com" }
}
```

### Dashboard and Reports

1. `GET /api/dashboard`
- Success response (200):

```json
{
	"stats": [],
	"enrollmentTrend": [],
	"programDistribution": [],
	"attendancePatterns": [],
	"capacityData": [],
	"recentEnrollments": [],
	"activities": []
}
```

2. `GET /api/dashboard/summary`
- Success response (200):

```json
{
	"success": true,
	"message": "Dashboard summary loaded successfully.",
	"data": {
		"students_enrolled": 120,
		"courses_offered": 30,
		"active_courses": 28,
		"today_attendance_rate": 95.2,
		"average_attendance_rate": 93.4,
		"events_this_month": 4
	}
}
```

3. `GET /api/dashboard/trends`
- Success response (200):

```json
{
	"success": true,
	"message": "Dashboard trends loaded successfully.",
	"data": {
		"enrollment_trend": [],
		"attendance_trend": [],
		"department_breakdown": []
	}
}
```

4. `GET /api/dashboard/calendar?from=2026-03-01&to=2026-03-31&day_type=event`
- Success response (200):

```json
{
	"success": true,
	"message": "Calendar data loaded successfully.",
	"data": [],
	"meta": { "from": "2026-03-01", "to": "2026-03-31" }
}
```

5. `GET /api/enrollments/pipeline`
- Success response (200):

```json
[
	{ "stage": "Applied", "count": 120 },
	{ "stage": "Document Check", "count": 90 },
	{ "stage": "For Interview", "count": 48 },
	{ "stage": "Approved", "count": 40 }
]
```

6. `GET /api/reports/cards`
- Success response (200):

```json
[
	{ "label": "Average Processing Time", "value": "1.8 days" },
	{ "label": "System Uptime", "value": "99.3%" },
	{ "label": "Retention Projection", "value": "88.2%" },
	{ "label": "At-Risk Students", "value": "6" }
]
```

### Enrollments

1. `GET /api/enrollments`
- Success response (200):

```json
{
	"success": true,
	"message": "Enrollments retrieved successfully.",
	"data": []
}
```

2. `GET /api/enrollments/status-summary`
- Success response (200):

```json
{
	"success": true,
	"message": "Enrollment status summary retrieved successfully.",
	"data": {
		"total_students": 100,
		"enrolled": 60,
		"pending": 20,
		"approved": 10,
		"for_review": 5,
		"probation": 2,
		"rejected": 2,
		"dropped": 1
	}
}
```

3. `POST /api/enrollments`
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

4. `DELETE /api/enrollments`
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

### Students (REST)

- `GET /api/students`
- `POST /api/students`
- `GET /api/students/{id}`
- `PUT /api/students/{id}`
- `DELETE /api/students/{id}`

Expected success format for create/update/show:

```json
{
	"success": true,
	"message": "Student created successfully.",
	"data": {}
}
```

### Courses (REST)

- `GET /api/courses`
- `POST /api/courses`
- `GET /api/courses/{id}`
- `PUT /api/courses/{id}`
- `DELETE /api/courses/{id}`

Expected success format for create/update/show:

```json
{
	"success": true,
	"message": "Course created successfully.",
	"data": {}
}
```

### School Days (REST)

- `GET /api/school-days`
- `POST /api/school-days`
- `GET /api/school-days/{id}`
- `PUT /api/school-days/{id}`
- `DELETE /api/school-days/{id}`

Expected success format:

```json
{
	"success": true,
	"message": "School day retrieved successfully.",
	"data": {}
}
```

### Announcements (REST)

- `GET /api/announcements`
- `POST /api/announcements`
- `GET /api/announcements/{id}`
- `PUT /api/announcements/{id}`
- `DELETE /api/announcements/{id}`

Expected success format:

```json
{
	"success": true,
	"message": "Announcement retrieved successfully.",
	"data": {}
}
```


