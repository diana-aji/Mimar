<?php

use App\Models\City;
use App\Models\EstimationType;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\RatingController;
use App\Http\Controllers\Web\ServiceController;
use App\Http\Controllers\Web\FavoriteController;
use App\Http\Controllers\Web\ServiceReportController;
use App\Http\Controllers\Web\BusinessAccountController;

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\Auth\OtpLoginController;
use App\Http\Controllers\Web\Auth\AdminLoginController;

use App\Http\Controllers\Web\Admin\CityAdminController;
use App\Http\Controllers\Web\Admin\UserAdminController;
use App\Http\Controllers\Web\Admin\RoleAdminController;
use App\Http\Controllers\Web\Admin\SliderAdminController;
use App\Http\Controllers\Web\Admin\ServiceReportAdminController;
use App\Http\Controllers\Web\Admin\BusinessAccountAdminController as WebAdminBusinessAccountController;
use App\Http\Controllers\Web\Admin\CategoryAdminController;
use App\Http\Controllers\Web\Admin\CityMaterialPriceAdminController;
use App\Http\Controllers\Web\Admin\DynamicFieldAdminController;
use App\Http\Controllers\Web\Admin\ServiceAdminController as WebAdminServiceController;
use App\Http\Controllers\Web\Admin\SubcategoryAdminController;
use App\Http\Controllers\Web\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Web\Admin\DashboardAdminController;
use App\Http\Controllers\Web\Admin\ExchangeRateAdminController;

use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\ChatController;
use App\Http\Controllers\Web\EstimationController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\ProfileController;

Route::get('/', function () {
    return view('public.welcome');
})->name('welcome');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }

    return redirect()->back();
})->name('lang.switch');

Route::get('/currency/{currency}', function ($currency) {
    if (in_array($currency, ['SYP', 'USD'])) {
        session(['display_currency' => $currency]);
    }

    return redirect()->back();
})->name('currency.switch');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [OtpLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [OtpLoginController::class, 'sendCode'])->name('otp.send');

    Route::get('/otp-verify', [OtpLoginController::class, 'showVerifyForm'])->name('otp.verify.form');
    Route::post('/otp-verify', [OtpLoginController::class, 'verifyCode'])->name('otp.verify');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

    Route::get('/dashboard', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/dashboard', [AdminLoginController::class, 'login'])->name('admin.login.submit');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    /*
    |--------------------------------------------------------------------------
    | Business Account
    |--------------------------------------------------------------------------
    */
    Route::get('/business-account', [BusinessAccountController::class, 'index'])->name('business-account.index');
    Route::get('/business-account/create', [BusinessAccountController::class, 'create'])->name('business-account.create');
    Route::post('/business-account', [BusinessAccountController::class, 'store'])->name('business-account.store');
    Route::put('/business-account/{businessAccount}', [BusinessAccountController::class, 'update'])->name('business-account.update');

    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    */
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

    Route::middleware('permission:create-services')->group(function () {
        Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    });

    Route::middleware('permission:edit-services')->group(function () {
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    });

    Route::middleware('permission:delete-services')->group(function () {
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    });

    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

    Route::post('/display-currency', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'display_currency' => ['required', 'in:SYP,USD'],
        ]);

        session(['display_currency' => $request->display_currency]);

        return back();
    })->name('display-currency.update');

    /*
    |--------------------------------------------------------------------------
    | Estimations
    |--------------------------------------------------------------------------
    */
    Route::get('/estimations/create', function () {
        return view('public.estimations.create', [
            'cities' => City::query()->orderBy('name_ar')->get(),
            'types' => EstimationType::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    })->name('estimations.create');

    Route::post('/estimations/calculate', [EstimationController::class, 'calculate'])->name('estimations.calculate');

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/services/{service}/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/{order}/accept', [OrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    /*
    |--------------------------------------------------------------------------
    | Ratings
    |--------------------------------------------------------------------------
    */
    Route::post('/orders/{order}/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::get('/services/{service}/ratings', [RatingController::class, 'index'])->name('ratings.index');

    /*
    |--------------------------------------------------------------------------
    | Favorites
    |--------------------------------------------------------------------------
    */
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/services/{service}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/services/{service}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::post('/services/{service}/reports', [ServiceReportController::class, 'store'])->name('reports.store');

    /*
    |--------------------------------------------------------------------------
    | Static Pages
    |--------------------------------------------------------------------------
    */
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/conversations', [ChatController::class, 'storeConversation'])->name('chat.conversations.store');
    Route::post('/chat/conversations/{conversation}/messages', [ChatController::class, 'storeMessage'])->name('chat.messages.store');
    Route::post('/services/{service}/chat', [ChatController::class, 'startFromService'])->name('chat.start-from-service');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role_or_permission:super-admin|admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */
        Route::middleware('permission:view-roles')->group(function () {
            Route::get('/roles', [RoleAdminController::class, 'index'])->name('admin.roles.index');
        });

        Route::middleware('permission:create-roles')->group(function () {
            Route::post('/roles', [RoleAdminController::class, 'store'])->name('admin.roles.store');
        });

        Route::middleware('permission:edit-roles')->group(function () {
            Route::put('/roles/{role}', [RoleAdminController::class, 'update'])->name('admin.roles.update');
        });

        Route::middleware('permission:delete-roles')->group(function () {
            Route::delete('/roles/{role}', [RoleAdminController::class, 'destroy'])->name('admin.roles.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | City Material Prices
        |--------------------------------------------------------------------------
        */
        Route::get('/city-material-prices', [CityMaterialPriceAdminController::class, 'index'])
            ->name('admin.city-material-prices.index');

        Route::post('/city-material-prices', [CityMaterialPriceAdminController::class, 'store'])
            ->name('admin.city-material-prices.store');

        Route::put('/city-material-prices/{cityMaterialPrice}', [CityMaterialPriceAdminController::class, 'update'])
            ->name('admin.city-material-prices.update');

        Route::delete('/city-material-prices/{cityMaterialPrice}', [CityMaterialPriceAdminController::class, 'destroy'])
            ->name('admin.city-material-prices.destroy');

        /*
        |--------------------------------------------------------------------------
        | Exchange Rates
        |--------------------------------------------------------------------------
        */
        Route::get('/exchange-rates', [ExchangeRateAdminController::class, 'index'])
            ->name('admin.exchange-rates.index');

        Route::post('/exchange-rates', [ExchangeRateAdminController::class, 'store'])
            ->name('admin.exchange-rates.store');

        Route::put('/exchange-rates/{exchangeRate}', [ExchangeRateAdminController::class, 'update'])
            ->name('admin.exchange-rates.update');

        Route::delete('/exchange-rates/{exchangeRate}', [ExchangeRateAdminController::class, 'destroy'])
            ->name('admin.exchange-rates.destroy');

        /*
        |--------------------------------------------------------------------------
        | Dynamic Fields
        |--------------------------------------------------------------------------
        */
        Route::middleware('permission:view-dynamic-fields')->group(function () {
            Route::get('/dynamic-fields', [DynamicFieldAdminController::class, 'index'])
                ->name('admin.dynamic-fields.index');
        });

        Route::middleware('permission:create-dynamic-fields')->group(function () {
            Route::post('/dynamic-fields', [DynamicFieldAdminController::class, 'store'])
                ->name('admin.dynamic-fields.store');
        });

        Route::middleware('permission:edit-dynamic-fields')->group(function () {
            Route::put('/dynamic-fields/{dynamicField}', [DynamicFieldAdminController::class, 'update'])
                ->name('admin.dynamic-fields.update');
        });

        Route::middleware('permission:delete-dynamic-fields')->group(function () {
            Route::delete('/dynamic-fields/{dynamicField}', [DynamicFieldAdminController::class, 'destroy'])
                ->name('admin.dynamic-fields.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        Route::middleware('permission:view-categories')->group(function () {
            Route::get('/categories', [CategoryAdminController::class, 'index'])->name('admin.categories.index');
        });

        Route::middleware('permission:create-categories')->group(function () {
            Route::post('/categories', [CategoryAdminController::class, 'store'])->name('admin.categories.store');
        });

        Route::middleware('permission:edit-categories')->group(function () {
            Route::put('/categories/{category}', [CategoryAdminController::class, 'update'])->name('admin.categories.update');
        });

        Route::middleware('permission:delete-categories')->group(function () {
            Route::delete('/categories/{category}', [CategoryAdminController::class, 'destroy'])->name('admin.categories.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Subcategories
        |--------------------------------------------------------------------------
        */
        Route::middleware('permission:view-subcategories')->group(function () {
            Route::get('/subcategories', [SubcategoryAdminController::class, 'index'])->name('admin.subcategories.index');
        });

        Route::middleware('permission:create-subcategories')->group(function () {
            Route::post('/subcategories', [SubcategoryAdminController::class, 'store'])->name('admin.subcategories.store');
        });

        Route::middleware('permission:edit-subcategories')->group(function () {
            Route::put('/subcategories/{subcategory}', [SubcategoryAdminController::class, 'update'])->name('admin.subcategories.update');
        });

        Route::middleware('permission:delete-subcategories')->group(function () {
            Route::delete('/subcategories/{subcategory}', [SubcategoryAdminController::class, 'destroy'])->name('admin.subcategories.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */
        Route::middleware('permission:view-users')->group(function () {
            Route::get('/users', [UserAdminController::class, 'index'])->name('admin.users.index');
        });

        Route::middleware('permission:create-users')->group(function () {
            Route::post('/users', [UserAdminController::class, 'store'])->name('admin.users.store');
        });

        Route::middleware('permission:edit-users')->group(function () {
            Route::put('/users/{user}', [UserAdminController::class, 'update'])->name('admin.users.update');
        });

        Route::middleware('permission:delete-users')->group(function () {
            Route::delete('/users/{user}', [UserAdminController::class, 'destroy'])->name('admin.users.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Cities
        |--------------------------------------------------------------------------
        */
        Route::middleware('permission:view-cities')->group(function () {
            Route::get('/cities', [CityAdminController::class, 'index'])->name('admin.cities.index');
        });

        Route::middleware('permission:create-cities')->group(function () {
            Route::post('/cities', [CityAdminController::class, 'store'])->name('admin.cities.store');
        });

        Route::middleware('permission:edit-cities')->group(function () {
            Route::put('/cities/{city}', [CityAdminController::class, 'update'])->name('admin.cities.update');
        });

        Route::middleware('permission:delete-cities')->group(function () {
            Route::delete('/cities/{city}', [CityAdminController::class, 'destroy'])->name('admin.cities.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Sliders
        |--------------------------------------------------------------------------
        */
        Route::middleware('permission:view-sliders')->group(function () {
            Route::get('/sliders', [SliderAdminController::class, 'index'])->name('admin.sliders.index');
        });

        Route::middleware('permission:create-sliders')->group(function () {
            Route::post('/sliders', [SliderAdminController::class, 'store'])->name('admin.sliders.store');
        });

        Route::middleware('permission:edit-sliders')->group(function () {
            Route::put('/sliders/{slider}', [SliderAdminController::class, 'update'])->name('admin.sliders.update');
        });

        Route::middleware('permission:delete-sliders')->group(function () {
            Route::delete('/sliders/{slider}', [SliderAdminController::class, 'destroy'])->name('admin.sliders.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Business Accounts Review
        |--------------------------------------------------------------------------
        */
        Route::middleware('permission:view-business-accounts')->group(function () {
            Route::get('/business-accounts', [WebAdminBusinessAccountController::class, 'index'])->name('admin.business-accounts.index');
        });

        Route::middleware('permission:approve-business-accounts')->group(function () {
            Route::post('/business-accounts/{businessAccount}/approve', [WebAdminBusinessAccountController::class, 'approve'])->name('admin.business-accounts.approve');
        });

        Route::middleware('permission:reject-business-accounts')->group(function () {
            Route::post('/business-accounts/{businessAccount}/reject', [WebAdminBusinessAccountController::class, 'reject'])->name('admin.business-accounts.reject');
        });

        /*
        |--------------------------------------------------------------------------
        | Services Review
        |--------------------------------------------------------------------------
        */
        Route::middleware('permission:view-services')->group(function () {
            Route::get('/services', [WebAdminServiceController::class, 'index'])->name('admin.services.index');
        });

        Route::middleware('permission:approve-services')->group(function () {
            Route::post('/services/{service}/approve', [WebAdminServiceController::class, 'approve'])->name('admin.services.approve');
        });

        Route::middleware('permission:reject-services')->group(function () {
            Route::post('/services/{service}/reject', [WebAdminServiceController::class, 'reject'])->name('admin.services.reject');
        });

        /*
        |--------------------------------------------------------------------------
        | Reports Review
        |--------------------------------------------------------------------------
        */
        Route::middleware('permission:view-reports')->group(function () {
            Route::get('/reports', [ServiceReportAdminController::class, 'index'])->name('admin.reports.index');
        });

        Route::middleware('permission:resolve-reports')->group(function () {
            Route::post('/reports/{serviceReport}/resolve', [ServiceReportAdminController::class, 'resolve'])->name('admin.reports.resolve');
        });

        /*
        |--------------------------------------------------------------------------
        | Orders Review
        |--------------------------------------------------------------------------
        */
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::post('/orders/{order}/accept', [AdminOrderController::class, 'accept'])->name('admin.orders.accept');
        Route::post('/orders/{order}/reject', [AdminOrderController::class, 'reject'])->name('admin.orders.reject');
        Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');
    });

Route::get('/categories-preview', function () {
    return view('public.categories.index');
})->name('categories.show.preview');