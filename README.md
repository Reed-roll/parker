# Parker - Parking Management Microservice

A RESTful microservice for managing parking operations, built with CodeIgniter 4.

**Base URL:** `https://parker.queenifyofficial.site/`

## Features

- 🎫 Parking ticket management (start/end sessions)
- 💳 Payment processing with webhook support
- 🧾 Digital receipt generation
- 👤 User authentication and management
- 🅿️ Parking spot availability tracking

## API Documentation

All API endpoints are prefixed with `/api`

### Authentication

#### Register
```http
POST /api/auth/register
```

#### Login
```http
POST /api/auth/login
```

#### Get Current User
```http
GET /api/auth/me
```

### Parking Tickets

#### Start Parking Session
```http
POST /api/tickets/start
```
Creates a new parking ticket when a vehicle enters.

#### End Parking Session
```http
POST /api/tickets/{ticket_id}/end
```
Ends an active parking session and calculates charges.

#### Get Ticket Details
```http
GET /api/tickets/{ticket_id}
```
Retrieves information about a specific parking ticket.

### Payments

#### Pay for Ticket
```http
POST /api/tickets/{ticket_id}/pay
```
Processes payment for a parking ticket.

#### Payment Webhook
```http
POST /api/payments/webhook
```
Webhook endpoint for payment gateway callbacks.

#### Get Receipt
```http
GET /api/payments/{payment_id}/receipt
```
Retrieves a payment receipt.

### Users

#### Get User Tickets
```http
GET /api/users/{user_id}/tickets
```
Retrieves all parking tickets for a specific user.

### Parking Spots

#### List Parking Spots
```http
GET /api/parking-spots
```
Returns available parking spots and their status.

#### Update Parking Spot
```http
PUT /api/parking-spots/{spot_id}
```
Updates parking spot information (availability, status, etc.).
