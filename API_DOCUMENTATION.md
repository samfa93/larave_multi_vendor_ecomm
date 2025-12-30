# Multi-Vendor E-commerce API Documentation

## Base URL
```
http://your-domain.com/api/v1
```

## Authentication
All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {your-token-here}
```

---

## Authentication Endpoints

### Register
**POST** `/register`

**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "user_type": "customer"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": { ... },
    "token": "1|abcdef...",
    "token_type": "Bearer"
  }
}
```

### Login
**POST** `/login`

**Body:**
```json
{
  "email": "john@example.com",
  "password": "password123",
  "device_name": "mobile-app"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { ... },
    "token": "2|ghijkl...",
    "token_type": "Bearer"
  }
}
```

### Logout
**POST** `/logout` 🔒

**Response:**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

---

## Product Endpoints

### List Products
**GET** `/products`

**Query Parameters:**
- `category` - Category slug
- `min_price` - Minimum price
- `max_price` - Maximum price
- `brands` - Brand IDs (comma-separated or array)
- `tags` - Tag IDs (comma-separated or array)
- `search` - Search term
- `store_id` - Store ID
- `per_page` - Items per page (default: 20)

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

### Get Product Details
**GET** `/products/{slug}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Product Name",
    "slug": "product-name",
    "description": "...",
    "price": 99.99,
    "special_price": 79.99,
    "final_price": 79.99,
    "discount_percentage": 20,
    "stock": 100,
    "images": [ ... ],
    "store": { ... },
    "variants": [ ... ]
  }
}
```

### Get Featured Products
**GET** `/featured-products?limit=10`

### Get Flash Sale Products
**GET** `/flash-sales`

### Get Brands
**GET** `/brands?limit=20`

---

## Category Endpoints

### List Categories
**GET** `/categories`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics",
      "icon": "...",
      "children": [ ... ]
    }
  ]
}
```

### Get Category Products
**GET** `/categories/{slug}/products`

---

## Cart Endpoints (Protected)

### Get Cart
**GET** `/cart` 🔒

**Response:**
```json
{
  "success": true,
  "data": {
    "items": [ ... ],
    "subtotal": 199.98,
    "items_count": 2
  }
}
```

### Add to Cart
**POST** `/cart` 🔒

**Body:**
```json
{
  "product_id": 1,
  "variant_id": 5,
  "quantity": 2
}
```

### Update Cart Item
**PUT** `/cart/{id}` 🔒

**Body:**
```json
{
  "quantity": 3
}
```

### Remove from Cart
**DELETE** `/cart/{id}` 🔒

### Clear Cart
**DELETE** `/cart` 🔒

---

## Coupon Endpoints (Protected)

### Apply Coupon
**POST** `/cart/coupon` 🔒

**Body:**
```json
{
  "code": "SAVE20"
}
```

### Remove Coupon
**DELETE** `/cart/coupon` 🔒

---

## Wishlist Endpoints (Protected)

### Get Wishlist
**GET** `/wishlist` 🔒

### Add to Wishlist
**POST** `/wishlist` 🔒

**Body:**
```json
{
  "product_id": 1
}
```

### Remove from Wishlist
**DELETE** `/wishlist/{id}` 🔒

---

## Address Endpoints (Protected)

### List Addresses
**GET** `/addresses` 🔒

### Create Address
**POST** `/addresses` 🔒

**Body:**
```json
{
  "name": "John Doe",
  "phone": "+1234567890",
  "email": "john@example.com",
  "address_line_1": "123 Main St",
  "address_line_2": "Apt 4B",
  "city": "New York",
  "state": "NY",
  "zip_code": "10001",
  "country": "USA",
  "is_default": true
}
```

### Update Address
**PUT** `/addresses/{id}` 🔒

### Delete Address
**DELETE** `/addresses/{id}` 🔒

---

## Checkout & Order Endpoints (Protected)

### Get Shipping Methods
**GET** `/shipping-methods/{addressId}` 🔒

### Set Billing Info
**POST** `/checkout/billing-info` 🔒

**Body:**
```json
{
  "shipping_address_id": 1,
  "billing_address_id": 1,
  "shipping_method_id": 1
}
```

### Get Checkout Summary
**GET** `/checkout/summary` 🔒

**Response:**
```json
{
  "success": true,
  "data": {
    "subtotal": 199.98,
    "shipping_charge": 10.00,
    "discount": 20.00,
    "total": 189.98,
    "items_count": 2,
    "coupon": { ... }
  }
}
```

---

## Payment Endpoints (Protected)

### Create PayPal Payment
**POST** `/payments/paypal/create` 🔒

**Response:**
```json
{
  "success": true,
  "data": {
    "order_id": "paypal-order-id",
    "approval_url": "https://paypal.com/checkout/..."
  }
}
```

### Create Stripe Payment
**POST** `/payments/stripe/create` 🔒

**Response:**
```json
{
  "success": true,
  "data": {
    "session_id": "stripe-session-id",
    "checkout_url": "https://checkout.stripe.com/..."
  }
}
```

### Create Razorpay Payment
**POST** `/payments/razorpay/create` 🔒

**Response:**
```json
{
  "success": true,
  "data": {
    "order_id": "razorpay-order-id",
    "amount": 189.98,
    "currency": "USD",
    "key": "razorpay-key"
  }
}
```

### Cash on Delivery
**POST** `/payments/cod` 🔒

**Response:**
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "transaction_id": "COD-1234567890-1",
    "message": "Order placed successfully"
  }
}
```

---

## Order Endpoints (Protected)

### List Orders
**GET** `/orders` 🔒

### Get Order Details
**GET** `/orders/{id}` 🔒

### Track Order
**GET** `/orders/{id}/track` 🔒

---

## User Profile Endpoints (Protected)

### Get Profile
**GET** `/user/profile` 🔒

### Update Profile
**PUT** `/user/profile` 🔒

**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com"
}
```

### Update Password
**PUT** `/user/password` 🔒

**Body:**
```json
{
  "current_password": "oldpassword",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

### Update Avatar
**POST** `/user/avatar` 🔒

**Body (multipart/form-data):**
```
avatar: [image file]
```

---

## Vendor Endpoints

### List Vendors
**GET** `/vendors`

### Get Vendor Details
**GET** `/vendors/{id}`

### Get Vendor Products
**GET** `/vendors/{id}/products`

---

## Vendor Dashboard Endpoints (Protected - Vendor Role)

### Get Dashboard Stats
**GET** `/vendor/dashboard` 🔒

### Get Vendor Profile
**GET** `/vendor/profile` 🔒

### Update Vendor Profile
**PUT** `/vendor/profile` 🔒

### List Vendor Products
**GET** `/vendor/products` 🔒

### Get Vendor Product
**GET** `/vendor/products/{id}` 🔒

### Delete Product
**DELETE** `/vendor/products/{id}` 🔒

### List Vendor Orders
**GET** `/vendor/orders` 🔒

### Get Vendor Order
**GET** `/vendor/orders/{id}` 🔒

### Update Order Status
**PUT** `/vendor/orders/{id}` 🔒

**Body:**
```json
{
  "order_status": "processing"
}
```

---

## Product Review Endpoints

### Get Product Reviews
**GET** `/products/{id}/reviews`

### Submit Review
**POST** `/products/{id}/reviews` 🔒

**Body:**
```json
{
  "rating": 5,
  "comment": "Great product!"
}
```

---

## Miscellaneous Endpoints

### Get Home Page Data
**GET** `/home`

### Submit Contact Form
**POST** `/contact`

**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "subject": "Question",
  "message": "I have a question..."
}
```

### Subscribe to Newsletter
**POST** `/newsletter/subscribe`

**Body:**
```json
{
  "email": "john@example.com"
}
```

---

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error"
}
```

---

## Testing the API

### Using cURL
```bash
# Login
curl -X POST http://your-domain.com/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Get Products (with token)
curl -X GET http://your-domain.com/api/v1/products \
  -H "Authorization: Bearer your-token-here"
```

### Using Postman
1. Import the API endpoints
2. Set Authorization Type to "Bearer Token"
3. Add your token in the Token field
4. Send requests

---

## Rate Limiting
- **Authenticated users:** 60 requests per minute
- **Guest users:** 30 requests per minute

## API Versioning
Current version: `v1`

Future versions will be available at `/api/v2`, etc.
