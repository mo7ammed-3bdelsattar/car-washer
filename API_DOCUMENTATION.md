# Car Washer API Documentation (v1)

**Base URL**: `http://localhost:8000/api/v1`  
**Postman Collection File**: [`car_washer_api.postman_collection.json`](./car_washer_api.postman_collection.json) *(Import this file directly into Postman)*

---

## 1. Authentication Endpoints

### 1.1 Register
- **URL**: `POST /register`
- **Headers**:
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Body**:
```json
{
    "name": "Mohammed Ali",
    "email": "customer@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "type": 3,
    "fcm": "sample_fcm_token_string"
}
```
> **User Types (`type`)**: `1 = Admin`, `2 = Manager`, `3 = Customer (Default)`

- **Success Response (`201 Created`)**:
```json
{
    "success": true,
    "status": 201,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Mohammed Ali",
            "email": "customer@example.com",
            "type": 3,
            "type_label": "Customer",
            "is_active": true,
            "created_at": "2026-09-13T19:12:00.000000Z",
            "updated_at": "2026-09-13T19:12:00.000000Z"
        },
        "token": "1|2Y7..."
    }
}
```

---

### 1.2 Login
- **URL**: `POST /login`
- **Headers**:
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Body**:
```json
{
    "email": "customer@example.com",
    "password": "password123",
    "fcm": "sample_fcm_token_string"
}
```
- **Success Response (`200 OK`)**:
```json
{
    "success": true,
    "status": 200,
    "message": "Login Successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Mohammed Ali",
            "email": "customer@example.com",
            "type": 3,
            "type_label": "Customer",
            "is_active": true,
            "created_at": "2026-09-13T19:12:00.000000Z",
            "updated_at": "2026-09-13T19:12:00.000000Z"
        },
        "token": "2|3Z8..."
    }
}
```

---

### 1.3 Logout
- **URL**: `POST /logout`
- **Authentication**: `Bearer <token>`
- **Headers**:
  - `Accept: application/json`
- **Success Response (`200 OK`)**:
```json
{
    "success": true,
    "status": 200,
    "message": "Logged Out Successfully",
    "data": []
}
```

---

## 2. Profile Endpoints
All profile endpoints require `Authorization: Bearer <token>`.

### 2.1 Get Profile
- **URL**: `GET /profile`
- **Headers**:
  - `Accept: application/json`
- **Success Response (`200 OK`)**:
```json
{
    "success": true,
    "status": 200,
    "message": "Success",
    "data": {
        "id": 1,
        "name": "Mohammed Ali",
        "email": "customer@example.com",
        "type": 3,
        "type_label": "Customer",
        "is_active": true,
        "created_at": "2026-09-13T19:12:00.000000Z",
        "updated_at": "2026-09-13T19:12:00.000000Z"
    }
}
```

---

### 2.2 Update Profile
- **URL**: `PUT /profile`
- **Headers**:
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Body**:
```json
{
    "name": "Mohammed Ali Updated",
    "email": "customer_updated@example.com",
    "fcm_token": "new_fcm_token_sample"
}
```
- **Success Response (`200 OK`)**:
```json
{
    "success": true,
    "status": 200,
    "message": "Profile updated successfully",
    "data": {
        "id": 1,
        "name": "Mohammed Ali Updated",
        "email": "customer_updated@example.com",
        "type": 3,
        "type_label": "Customer",
        "is_active": true,
        "created_at": "2026-09-13T19:12:00.000000Z",
        "updated_at": "2026-09-13T19:15:00.000000Z"
    }
}
```

---

### 2.3 Change Password
- **URL**: `POST /profile/change-password`
- **Headers**:
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Body**:
```json
{
    "current_password": "password123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```
- **Success Response (`200 OK`)**:
```json
{
    "success": true,
    "status": 200,
    "message": "Password changed successfully",
    "data": []
}
```

---

### 2.4 Delete Profile (Soft Delete)
- **URL**: `DELETE /profile`
- **Headers**:
  - `Accept: application/json`
- **Success Response (`200 OK`)**:
```json
{
    "success": true,
    "status": 200,
    "message": "Account deleted successfully",
    "data": []
}
```

---

## 3. Standard Response Format

### Success Response
```json
{
    "success": true,
    "status": 200,
    "message": "Success",
    "data": {}
}
```

### Validation Error Response (`422 Unprocessable Content`)
```json
{
    "success": false,
    "status": 422,
    "message": "Validation failed",
    "errors": {
        "email": [
            "The email field is required."
        ]
    }
}
```
