# 🎯 Quick Start Guide - Multi-Vendor E-commerce API

## Prerequisites
- ✅ Laravel Sanctum installed and configured
- ✅ Database migrated (personal_access_tokens table)
- ✅ API routes registered at `/api/v1/*`
- ✅ 63 endpoints ready

---

## 🚀 Getting Started in 5 Minutes

### Step 1: Start the Server
```bash
php artisan serve
```
Your API will be available at: `http://localhost:8000/api/v1/`

### Step 2: Register a Test User
**Using cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "test@example.com",
      "user_type": "customer"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz",
    "token_type": "Bearer"
  }
}
```

**Copy the token** - you'll need it for authenticated requests!

### Step 3: Test a Protected Endpoint
```bash
curl -X GET http://localhost:8000/api/v1/cart \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "items": [],
    "subtotal": 0,
    "items_count": 0
  }
}
```

### Step 4: Browse Products (No Auth Required)
```bash
curl -X GET http://localhost:8000/api/v1/products?per_page=5
```

---

## 📱 Postman Collection Setup

### Import These Endpoints

1. **Create New Collection:** "Multi-Vendor API"

2. **Set Variables:**
   - `base_url`: `http://localhost:8000/api/v1`
   - `token`: (leave empty, will be set after login)

3. **Add Requests:**

#### Auth Folder
- **POST** `{{base_url}}/register`
- **POST** `{{base_url}}/login`
- **POST** `{{base_url}}/logout`

#### Products Folder
- **GET** `{{base_url}}/products`
- **GET** `{{base_url}}/products/{slug}`
- **GET** `{{base_url}}/categories`

#### Cart Folder (Add Authorization: Bearer Token)
- **GET** `{{base_url}}/cart`
- **POST** `{{base_url}}/cart`
- **PUT** `{{base_url}}/cart/{id}`
- **DELETE** `{{base_url}}/cart/{id}`

---

## 🔐 Authentication Flow

### 1. Register
```http
POST /api/v1/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "securepass123",
  "password_confirmation": "securepass123",
  "user_type": "customer"
}
```

### 2. Login
```http
POST /api/v1/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "securepass123",
  "device_name": "iPhone 13"
}
```

### 3. Use Token in Subsequent Requests
```http
GET /api/v1/user/profile
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz
```

### 4. Logout
```http
POST /api/v1/logout
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz
```

---

## 🛒 Common Use Cases

### Use Case 1: Browse and Search Products

**Search products:**
```http
GET /api/v1/products?search=laptop&per_page=10
```

**Filter by category:**
```http
GET /api/v1/products?category=electronics
```

**Filter by price range:**
```http
GET /api/v1/products?min_price=100&max_price=500
```

**Multiple filters:**
```http
GET /api/v1/products?category=electronics&min_price=100&max_price=500&brands=1,2,3
```

### Use Case 2: Add Product to Cart

**Step 1: Add to cart**
```http
POST /api/v1/cart
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "product_id": 1,
  "variant_id": 5,
  "quantity": 2
}
```

**Step 2: View cart**
```http
GET /api/v1/cart
Authorization: Bearer YOUR_TOKEN
```

**Step 3: Update quantity**
```http
PUT /api/v1/cart/1
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "quantity": 3
}
```

### Use Case 3: Apply Coupon

```http
POST /api/v1/cart/coupon
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "code": "SAVE20"
}
```

### Use Case 4: Complete Purchase

**Step 1: Add delivery address**
```http
POST /api/v1/addresses
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "name": "John Doe",
  "phone": "+1234567890",
  "email": "john@example.com",
  "address_line_1": "123 Main St",
  "city": "New York",
  "state": "NY",
  "zip_code": "10001",
  "country": "USA",
  "is_default": true
}
```

**Step 2: Set billing info**
```http
POST /api/v1/checkout/billing-info
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "shipping_address_id": 1,
  "billing_address_id": 1,
  "shipping_method_id": 1
}
```

**Step 3: Get checkout summary**
```http
GET /api/v1/checkout/summary
Authorization: Bearer YOUR_TOKEN
```

**Step 4: Place order (COD)**
```http
POST /api/v1/payments/cod
Authorization: Bearer YOUR_TOKEN
```

**Step 5: View order details**
```http
GET /api/v1/orders/1
Authorization: Bearer YOUR_TOKEN
```

---

## 🎨 Response Handling

### Success Response Structure
All successful responses follow this format:
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { /* your data here */ }
}
```

### Error Response Structure
All error responses follow this format:
```json
{
  "success": false,
  "message": "Error description",
  "errors": { /* validation errors if any */ }
}
```

### Handling in Your App

**JavaScript/React:**
```javascript
fetch('http://localhost:8000/api/v1/products', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Products:', data.data);
  } else {
    console.error('Error:', data.message);
  }
});
```

**Swift/iOS:**
```swift
var request = URLRequest(url: URL(string: "http://localhost:8000/api/v1/products")!)
request.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")

URLSession.shared.dataTask(with: request) { data, response, error in
    // Handle response
}.resume()
```

**Kotlin/Android:**
```kotlin
val request = Request.Builder()
    .url("http://localhost:8000/api/v1/products")
    .header("Authorization", "Bearer $token")
    .build()

client.newCall(request).enqueue(object : Callback {
    override fun onResponse(call: Call, response: Response) {
        // Handle response
    }
})
```

---

## 🐛 Troubleshooting

### Issue: 401 Unauthorized
**Cause:** Missing or invalid token  
**Solution:** 
1. Check token is in Authorization header
2. Format: `Authorization: Bearer YOUR_TOKEN`
3. Ensure token hasn't expired
4. Try logging in again to get new token

### Issue: 422 Validation Error
**Cause:** Invalid request data  
**Solution:** Check the `errors` object in response for specific field errors

Example:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Issue: 404 Not Found
**Cause:** Invalid endpoint or resource doesn't exist  
**Solution:** 
1. Verify endpoint URL is correct
2. Check if resource ID exists
3. Ensure you're using `/api/v1/` prefix

### Issue: 500 Server Error
**Cause:** Server-side error  
**Solution:** 
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode in `.env`: `APP_DEBUG=true`
3. Review error message for details

---

## ⚡ Performance Tips

### 1. Use Pagination
Always specify `per_page` for list endpoints:
```http
GET /api/v1/products?per_page=20
```

### 2. Request Only Needed Data
Use filters to reduce response size:
```http
GET /api/v1/products?category=electronics
```

### 3. Cache Responses
Cache frequently accessed data in your app:
- Product listings
- Categories
- Home page data

### 4. Compress Requests
Enable gzip compression in your HTTP client

### 5. Batch Operations
Instead of multiple single requests, collect data and send in batches where possible

---

## 📊 Rate Limiting

Current limits (can be adjusted):
- **Authenticated:** 60 requests/minute
- **Guest:** 30 requests/minute

If you hit the limit, you'll receive:
```json
{
  "message": "Too Many Attempts.",
  "status": 429
}
```

**Solution:** Implement exponential backoff or request rate limit increase.

---

## 🔧 Environment Configuration

Ensure these are set in your `.env`:

```env
# API Configuration
APP_URL=http://localhost:8000

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1

# CORS (if needed for web apps)
CORS_ALLOWED_ORIGINS=http://localhost:3000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail (for password reset)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password

# Payment Gateways
PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_SECRET=your_secret

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret

RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret
```

---

## 📚 Additional Resources

- **Full API Documentation:** `API_DOCUMENTATION.md`
- **Implementation Details:** `API_IMPLEMENTATION.md`
- **Summary:** `API_SUMMARY.md`
- **API Routes:** `routes/api.php`

---

## 🎯 Quick Reference

### Base URL
```
http://localhost:8000/api/v1
```

### Headers Required
```http
Content-Type: application/json
Authorization: Bearer YOUR_TOKEN  # For protected routes
```

### Key Endpoints
| Purpose | Endpoint | Method | Auth |
|---------|----------|--------|------|
| Register | `/register` | POST | No |
| Login | `/login` | POST | No |
| Products | `/products` | GET | No |
| Cart | `/cart` | GET | Yes |
| Orders | `/orders` | GET | Yes |
| Profile | `/user/profile` | GET | Yes |

---

## ✅ Checklist for Going Live

- [ ] Set `APP_DEBUG=false` in production
- [ ] Configure real payment gateway credentials
- [ ] Set up SSL certificate (HTTPS)
- [ ] Configure proper CORS domains
- [ ] Set up rate limiting rules
- [ ] Enable API logging and monitoring
- [ ] Test all critical endpoints
- [ ] Set up automated backups
- [ ] Configure email for password resets
- [ ] Document API for third-party developers

---

## 🎉 You're Ready!

Your API is fully functional and ready to power your mobile applications. Start building amazing user experiences!

**Need help?** Refer to the comprehensive documentation files included in the project.

**Happy coding! 🚀**
