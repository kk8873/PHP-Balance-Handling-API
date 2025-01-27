# PHP-Balance-Handling-API

A simple PHP-based API for handling group balances, expenses, and payments. This API demonstrates managing group members, recording expenses, and calculating balances in a group setting.

---

## Features

- **Group Management**: Fetch members of a specific group.
- **Expense Tracking**: Record expenses paid by group members.
- **Balance Calculation**: Calculate balances for each member of a group.
- **Payment Recording**: Record payments between group members.
- **Flexible APIs**: Easily extendable endpoints for managing groups, expenses, and balances.

---

## Prerequisites

- PHP >= 7.4
- Composer
- Web server (e.g., Apache or Nginx)

---

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/PHP-Balance-Handling-API.git
   cd PHP-Balance-Handling-API
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Start the server:
   ```bash
   php -S localhost:8080
   ```

   The API will be accessible at `http://localhost:8080`.

---

## Endpoints

### 1. Get Group Members
**Endpoint**: `GET /group/{group_id}/members`  
**Description**: Retrieve all members of a specific group.  
**Response**:
```json
[
    {"id": 1, "name": "Aliya"},
    {"id": 2, "name": "Buhan"},
    {"id": 3, "name": "Cheeti"}
]
```

### 2. Get Group Balances
**Endpoint**: `GET /group/{group_id}/balances`  
**Description**: Calculate balances for all members in a specific group.  
**Response**:
```json
[
    {"member_id": 1, "member_name": "Aliya", "balance": -10.0},
    {"member_id": 2, "member_name": "Buhan", "balance": 5.0},
    {"member_id": 3, "member_name": "Cheeti", "balance": 5.0}
]
```

### 3. Record a Payment
**Endpoint**: `POST /payment`  
**Description**: Record a payment between two members.  
**Request Body**:
```json
{
    "from_member": 1,
    "to_member": 2,
    "amount": 50,
    "group_id": 1
}
```
**Response**:
```json
{"message": "Payment recorded successfully."}
```

### 4. Record an Expense
**Endpoint**: `POST /expense`  
**Description**: Record an expense made by a member for the group.  
**Request Body**:
```json
{
    "group_id": 1,
    "paid_by": 1,
    "amount": 100
}
```
**Response**:
```json
{"message": "Expense recorded successfully."}
```

### 5. Update Group Balances
**Endpoint**: `POST /group/{group_id}/balances`  
**Description**: Update balances for a group manually.  
**Request Body**:
```json
{
    "balances": [
        {"member_id": 1, "balance": 20.0},
        {"member_id": 2, "balance": -10.0}
    ]
}
```
**Response**:
```json
{"message": "Balances updated successfully."}
```

### 6. Update Due Payments
**Endpoint**: `POST /group/{group_id}/due_payments`  
**Description**: Update due payments for a group.  
**Request Body**:
```json
{
    "payments": [
        {"from_member": 1, "to_member": 2, "amount": 30}
    ]
}
```
**Response**:
```json
{"message": "Due payments updated successfully."}
```

---

## License

This project is licensed under the MIT License. See the `LICENSE` file for details.
