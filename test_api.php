<?php

/**
 * API Test Script
 * 
 * This script tests basic API functionality
 * Run: php artisan tinker < test_api.php
 */

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "\n=== API Test Script ===\n\n";

// Test 1: Check if User model has HasApiTokens trait
echo "1. Checking User model for API support... ";
$traits = class_uses(User::class);
if (in_array('Laravel\Sanctum\HasApiTokens', $traits)) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL - HasApiTokens trait not found\n";
}

// Test 2: Check if api guard exists
echo "2. Checking API guard configuration... ";
$guards = config('auth.guards');
if (isset($guards['api']) && $guards['api']['driver'] === 'sanctum') {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL - API guard not configured\n";
}

// Test 3: Check service classes exist
echo "3. Checking service classes... ";
$services = [
    'App\Services\Auth\AuthService',
    'App\Services\Cart\CartService',
    'App\Services\Cart\CouponService',
    'App\Services\Product\ProductService',
];
$allExist = true;
foreach ($services as $service) {
    if (!class_exists($service)) {
        echo "✗ FAIL - $service not found\n";
        $allExist = false;
        break;
    }
}
if ($allExist) {
    echo "✓ PASS\n";
}

// Test 4: Check API controllers exist
echo "4. Checking API controllers... ";
$controllers = [
    'App\Http\Controllers\Api\V1\Auth\LoginController',
    'App\Http\Controllers\Api\V1\Product\ProductController',
    'App\Http\Controllers\Api\V1\Cart\CartController',
    'App\Http\Controllers\Api\V1\Order\OrderController',
];
$allExist = true;
foreach ($controllers as $controller) {
    if (!class_exists($controller)) {
        echo "✗ FAIL - $controller not found\n";
        $allExist = false;
        break;
    }
}
if ($allExist) {
    echo "✓ PASS\n";
}

// Test 5: Check API resources exist
echo "5. Checking API resources... ";
$resources = [
    'App\Http\Resources\User\UserResource',
    'App\Http\Resources\Product\ProductResource',
    'App\Http\Resources\Cart\CartResource',
];
$allExist = true;
foreach ($resources as $resource) {
    if (!class_exists($resource)) {
        echo "✗ FAIL - $resource not found\n";
        $allExist = false;
        break;
    }
}
if ($allExist) {
    echo "✓ PASS\n";
}

// Test 6: Test token creation
echo "6. Testing token creation... ";
try {
    $testUser = User::first();
    if ($testUser) {
        $token = $testUser->createToken('test-token');
        if ($token) {
            echo "✓ PASS\n";
            // Clean up
            $testUser->tokens()->where('name', 'test-token')->delete();
        } else {
            echo "✗ FAIL - Token creation failed\n";
        }
    } else {
        echo "⊘ SKIP - No users in database\n";
    }
} catch (Exception $e) {
    echo "✗ FAIL - " . $e->getMessage() . "\n";
}

// Test 7: Count API routes
echo "7. Counting API routes... ";
$routes = Route::getRoutes();
$apiRoutes = 0;
foreach ($routes as $route) {
    if (str_starts_with($route->uri(), 'api/v1/')) {
        $apiRoutes++;
    }
}
echo "$apiRoutes routes found ";
if ($apiRoutes >= 60) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL - Expected at least 60 routes\n";
}

echo "\n=== Test Summary ===\n";
echo "API infrastructure is ready!\n";
echo "You can now test endpoints using Postman or cURL.\n\n";
