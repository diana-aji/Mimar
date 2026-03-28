<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\City\CityController;
use App\Http\Controllers\Api\Home\HomeController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Subcategory\SubcategoryController;
use App\Http\Controllers\Api\BusinessActivityType\BusinessActivityTypeController;
use App\Http\Controllers\Api\BusinessAccount\BusinessAccountController;
use App\Http\Controllers\Api\Admin\BusinessAccountAdminController;
use App\Http\Controllers\Api\Admin\CityMaterialPriceController;
use App\Http\Controllers\Api\Admin\EstimationTypeController;
use App\Http\Controllers\Api\Admin\MaterialTypeController;
use App\Http\Controllers\Api\Service\ServiceController;
use App\Http\Controllers\Api\Admin\ServiceAdminController;
use App\Http\Controllers\Api\Chat\ChatController;
use App\Http\Controllers\Api\Estimation\EstimationController;
use App\Http\Controllers\Api\Favorite\FavoriteController;
use App\Http\Controllers\Api\Notification\DeviceTokenController;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Rating\RatingController;
use App\Http\Controllers\Api\Report\ServiceReportController;

Route::prefix('v1')->group(function () {

    Route::get('/health-check', function () {
        return response()->json([
            'success' => true,
            'message' => __('messages.success'),
            'data' => null,
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);

        Route::post('/otp/send', [AuthController::class, 'sendOtp']);
        Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);

        Route::post('/admin/login', [AuthController::class, 'adminLogin']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Home
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->get('/home', [HomeController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Cities
    |--------------------------------------------------------------------------
    */
    Route::prefix('cities')->group(function () {
        Route::middleware(['auth:sanctum', 'permission:view-cities'])
            ->get('/', [CityController::class, 'index']);

        Route::middleware(['auth:sanctum', 'permission:create-cities'])
            ->post('/', [CityController::class, 'store']);

        Route::middleware(['auth:sanctum', 'permission:view-cities'])
            ->get('/{city}', [CityController::class, 'show']);

        Route::middleware(['auth:sanctum', 'permission:edit-cities'])
            ->put('/{city}', [CityController::class, 'update']);

        Route::middleware(['auth:sanctum', 'permission:delete-cities'])
            ->delete('/{city}', [CityController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Business Activity Types
    |--------------------------------------------------------------------------
    */
    Route::prefix('business-activity-types')->group(function () {
        Route::middleware(['auth:sanctum', 'permission:view-business-activity-types'])
            ->get('/', [BusinessActivityTypeController::class, 'index']);

        Route::middleware(['auth:sanctum', 'permission:create-business-activity-types'])
            ->post('/', [BusinessActivityTypeController::class, 'store']);

        Route::middleware(['auth:sanctum', 'permission:view-business-activity-types'])
            ->get('/{businessActivityType}', [BusinessActivityTypeController::class, 'show']);

        Route::middleware(['auth:sanctum', 'permission:edit-business-activity-types'])
            ->put('/{businessActivityType}', [BusinessActivityTypeController::class, 'update']);

        Route::middleware(['auth:sanctum', 'permission:delete-business-activity-types'])
            ->delete('/{businessActivityType}', [BusinessActivityTypeController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */
    Route::prefix('categories')->group(function () {
        Route::middleware(['auth:sanctum', 'permission:view-categories'])
            ->get('/', [CategoryController::class, 'index']);

        Route::middleware(['auth:sanctum', 'permission:create-categories'])
            ->post('/', [CategoryController::class, 'store']);

        Route::middleware(['auth:sanctum', 'permission:view-categories'])
            ->get('/{category}', [CategoryController::class, 'show']);

        Route::middleware(['auth:sanctum', 'permission:edit-categories'])
            ->put('/{category}', [CategoryController::class, 'update']);

        Route::middleware(['auth:sanctum', 'permission:delete-categories'])
            ->delete('/{category}', [CategoryController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Subcategories
    |--------------------------------------------------------------------------
    */
    Route::prefix('subcategories')->group(function () {
        Route::middleware(['auth:sanctum', 'permission:view-subcategories'])
            ->get('/', [SubcategoryController::class, 'index']);

        Route::middleware(['auth:sanctum', 'permission:create-subcategories'])
            ->post('/', [SubcategoryController::class, 'store']);

        Route::middleware(['auth:sanctum', 'permission:view-subcategories'])
            ->get('/{subcategory}', [SubcategoryController::class, 'show']);

        Route::middleware(['auth:sanctum', 'permission:edit-subcategories'])
            ->put('/{subcategory}', [SubcategoryController::class, 'update']);

        Route::middleware(['auth:sanctum', 'permission:delete-subcategories'])
            ->delete('/{subcategory}', [SubcategoryController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Business Accounts - User
    |--------------------------------------------------------------------------
    */
    Route::prefix('business-accounts')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [BusinessAccountController::class, 'index']);
        Route::post('/', [BusinessAccountController::class, 'store']);
        Route::get('/{businessAccount}', [BusinessAccountController::class, 'show']);
        Route::put('/{businessAccount}', [BusinessAccountController::class, 'update']);
        Route::delete('/{businessAccount}', [BusinessAccountController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Business Accounts - Admin
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/business-accounts')->group(function () {
        Route::middleware(['auth:sanctum', 'permission:view-business-accounts'])
            ->get('/', [BusinessAccountAdminController::class, 'index']);

        Route::middleware(['auth:sanctum', 'permission:approve-business-accounts'])
            ->post('/{businessAccount}/approve', [BusinessAccountAdminController::class, 'approve']);

        Route::middleware(['auth:sanctum', 'permission:reject-business-accounts'])
            ->post('/{businessAccount}/reject', [BusinessAccountAdminController::class, 'reject']);
    });

    /*
    |--------------------------------------------------------------------------
    | Services - User
    |--------------------------------------------------------------------------
    */
    Route::prefix('business-accounts/{businessAccount}/services')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [ServiceController::class, 'index']);
        Route::post('/', [ServiceController::class, 'store']);
    });

    Route::prefix('services')->middleware(['auth:sanctum'])->group(function () {
        Route::put('/{service}', [ServiceController::class, 'update']);
        Route::delete('/{service}', [ServiceController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Services - Admin
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/services')->group(function () {
        Route::middleware(['auth:sanctum', 'permission:approve-services'])
            ->post('/{service}/approve', [ServiceAdminController::class, 'approve']);

        Route::middleware(['auth:sanctum', 'permission:reject-services'])
            ->post('/{service}/reject', [ServiceAdminController::class, 'reject']);
    });

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/orders/sent', [OrderController::class, 'sent']);
        Route::get('/orders/received', [OrderController::class, 'received']);

        Route::post('/services/{service}/orders', [OrderController::class, 'store']);

        Route::post('/orders/{order}/accept', [OrderController::class, 'accept']);
        Route::post('/orders/{order}/reject', [OrderController::class, 'reject']);
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    });

    /*
    |--------------------------------------------------------------------------
    | Ratings
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/orders/{order}/ratings', [RatingController::class, 'store']);
        Route::get('/services/{serviceId}/ratings', [RatingController::class, 'serviceRatings']);
    });

    /*
    |--------------------------------------------------------------------------
    | Favorites
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/services/{service}/favorite', [FavoriteController::class, 'store']);
        Route::delete('/services/{service}/favorite', [FavoriteController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/services/{service}/reports', [ServiceReportController::class, 'store']);
    });

    Route::prefix('admin/reports')->group(function () {
        Route::middleware(['auth:sanctum', 'permission:view-reports'])
            ->get('/', [ServiceReportController::class, 'index']);

        Route::middleware(['auth:sanctum', 'permission:resolve-reports'])
            ->post('/{serviceReport}/resolve', [ServiceReportController::class, 'resolve']);
    });

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread', [NotificationController::class, 'unread']);
        Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        Route::post('/device-tokens', [DeviceTokenController::class, 'store']);
        Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Chat
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->prefix('chat')->group(function () {
        Route::get('/conversations', [ChatController::class, 'conversations']);
        Route::post('/conversations', [ChatController::class, 'createConversation']);
        Route::get('/conversations/{conversation}/messages', [ChatController::class, 'messages']);
        Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']);
        Route::post('/conversations/{conversation}/read', [ChatController::class, 'markAsRead']);
    });

    /*
    |--------------------------------------------------------------------------
    | Estimations
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/estimations/calculate', [EstimationController::class, 'calculate']);
        Route::get('/estimations', [EstimationController::class, 'index']);
        Route::get('/estimations/{estimation}', [EstimationController::class, 'show']);
    });

    /*
    |--------------------------------------------------------------------------
    | Estimation Types
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/estimation-types')->group(function () {
        Route::middleware(['auth:sanctum', 'permission:view-estimation-types'])
            ->get('/', [EstimationTypeController::class, 'index']);

        Route::middleware(['auth:sanctum', 'permission:create-estimation-types'])
            ->post('/', [EstimationTypeController::class, 'store']);

        Route::middleware(['auth:sanctum', 'permission:view-estimation-types'])
            ->get('/{estimationType}', [EstimationTypeController::class, 'show']);

        Route::middleware(['auth:sanctum', 'permission:edit-estimation-types'])
            ->put('/{estimationType}', [EstimationTypeController::class, 'update']);

        Route::middleware(['auth:sanctum', 'permission:delete-estimation-types'])
            ->delete('/{estimationType}', [EstimationTypeController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Material Types
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/material-types')->group(function () {
        Route::middleware(['auth:sanctum', 'permission:view-material-types'])
            ->get('/', [MaterialTypeController::class, 'index']);

        Route::middleware(['auth:sanctum', 'permission:create-material-types'])
            ->post('/', [MaterialTypeController::class, 'store']);

        Route::middleware(['auth:sanctum', 'permission:view-material-types'])
            ->get('/{materialType}', [MaterialTypeController::class, 'show']);

        Route::middleware(['auth:sanctum', 'permission:edit-material-types'])
            ->put('/{materialType}', [MaterialTypeController::class, 'update']);

        Route::middleware(['auth:sanctum', 'permission:delete-material-types'])
            ->delete('/{materialType}', [MaterialTypeController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | City Material Prices
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin/city-material-prices')->group(function () {
        Route::middleware(['auth:sanctum', 'permission:view-city-material-prices'])
            ->get('/', [CityMaterialPriceController::class, 'index']);

        Route::middleware(['auth:sanctum', 'permission:create-city-material-prices'])
            ->post('/', [CityMaterialPriceController::class, 'store']);

        Route::middleware(['auth:sanctum', 'permission:view-city-material-prices'])
            ->get('/{cityMaterialPrice}', [CityMaterialPriceController::class, 'show']);

        Route::middleware(['auth:sanctum', 'permission:edit-city-material-prices'])
            ->put('/{cityMaterialPrice}', [CityMaterialPriceController::class, 'update']);

        Route::middleware(['auth:sanctum', 'permission:delete-city-material-prices'])
            ->delete('/{cityMaterialPrice}', [CityMaterialPriceController::class, 'destroy']);
    });
});