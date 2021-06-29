<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User
Route::post('login', 'Api\UserController@login');
Route::post('mpin','Api\UserController@mpinLogin');
Route::post('/email/send-reset-link','Api\UserController@sendResetPasswordLinkEmail');
Route::post('/email/reset-password', 'Api\UserController@resetPasswordWithToken');
Route::post('register-user', 'Api\UserController@register');
Route::get('users', 'Api\UserController@index');
Route::post('resend-otp', 'Api\UserController@resendOtp');
Route::post('reset-password', 'Api\UserController@resetPassword');
Route::post('validate-otp', 'Api\UserController@validateOtp');
Route::post('request_review_account', 'Api\UserController@requestReviewUnlockAccount');

// Admin
Route::post('/register/admin', 'Api\UserController@registerAdmin');
Route::post('/update/admin/{id}', 'Api\UserController@updateAdmin');
Route::get('/admins', 'Api\UserController@getAdmins');

// Admin log serializers
Route::get('/admin/logs', 'Api\AdminLogController@index');
Route::get('/admin/logs/{id}', 'Api\AdminLogController@show');

// Mail Routes
Route::post('/email/verification', 'Api\EmailController@verifyEmail');
Route::post('/email/verification/resend', 'Api\EmailController@resendVerification');

// Dragonpay postback
Route::get('post-back', 'Api\WalletController@postBack');

// API that requires TOKEN
Route::middleware('auth:api')->group(function () {
    Route::get('/support_tickets/user/status/{status}', 'Api\SupportTicketController@showByAuthUserAndStatus');
    Route::get('/verification_requests/user/status/{status}', 'Api\VerificationRequestController@showByAuthUserAndStatus');
    // User
    Route::post('user', 'Api\UserController@getUserByMobileNumber');
    Route::post('set-mpin', 'Api\UserController@setMpin');
    Route::post('update-user/{id}', 'Api\UserController@update');
    Route::post('logout', 'Api\UserController@logout')->name('logout');
    Route::post('update/user/{id}/profile/picture', 'Api\UserController@updateProfilePicture');
    // Merchant Voucher Transaction
    Route::get('/voucher/account/{id}/transaction', 'Api\TransactionController@vouncherTransactions');
    // Voucher Order
    Route::get('/voucher-order/{id}', 'Api\VoucherOrderController@show');
    Route::post('/voucher-order/create', 'Api\VoucherOrderController@create');
    Route::post('/voucher-order/upload/proof-of-payment', 'Api\VoucherOrderController@uploadProofOfPayment');
    Route::get('/voucher-order/{id}/no-proof-of-payment', 'Api\VoucherOrderController@noProofOfPayment');

    // Cashout
    Route::post('/ewallet/cashout', 'Api\CashoutController@ewalletCashout');
    Route::post('/bank/cashout', 'Api\CashoutController@bankCashout');
    // Pay
    Route::post('/pay', 'Api\VoucherAccountController@pay');
    // Pay Transaction
    Route::get('/pay/transaction/{id}', 'Api\TransactionController@payTransaction');
    // Shop
    Route::get('/find/shops/{id}', 'Api\ShopController@findShop');
});

// Cancel Order
Route::post('/voucher-order/{id}/cancel', 'Api\VoucherOrderController@cancelOrder');

// Voucher
Route::get('vouchers', 'Api\VoucherController@index');

// Address serializers
Route::get('/addresses', 'Api\AddressController@index');
Route::get('/addresses/shops', 'Api\AddressController@showByShopAddress');
Route::get('/addresses/barangay', 'Api\AddressController@showByBarangay');
Route::get('/addresses/city', 'Api\AddressController@showByCity');
Route::get('/addresses/province', 'Api\AddressController@showByProvince');
Route::get('/addresses/{id}', 'Api\AddressController@show');

// Announcement serializers
Route::get('/announcements', 'Api\AnnouncementController@index');
Route::get('/announcements/{id}', 'Api\AnnouncementController@show');

// Bank serializers
Route::get('/banks', 'Api\BankController@index');
Route::post('/banks/search', 'Api\BankController@searchBanks');
Route::get('/banks/{id}', 'Api\BankController@show');

// EWallet serializers
Route::get('/e-wallets', 'Api\EWalletController@index');
Route::post('/e-wallets/search', 'Api\EWalletController@searchEWallets');
Route::get('/e-wallets/{id}', 'Api\EWalletController@show');

// Faq serializers
Route::get('/faqs', 'Api\FaqController@index');
Route::post('/faqs/search', 'Api\FaqController@searchFaqs');
Route::get('/faqs/{id}', 'Api\FaqController@show');
Route::get('/faqs/category/{category}', 'Api\FaqController@showFaqsByCategory');

// Notification type serializers
Route::get('/notification_types', 'Api\NotificationTypeController@index');
Route::get('/notification_types/{id}', 'Api\NotificationTypeController@show');
Route::post('/notification_types/{id}/update', 'Api\NotificationTypeController@update');
Route::post('/notification_types/{id}/delete', 'Api\NotificationTypeController@delete');
Route::post('/notification_types/create', 'Api\NotificationTypeController@create');

// Notification serializers
Route::get('/notifications', 'Api\NotificationController@index');
Route::get('/notifications/{id}', 'Api\NotificationController@show');
Route::post('/notifications/{id}/update', 'Api\NotificationController@update');
Route::post('/notifications/{id}/delete', 'Api\NotificationController@delete');
Route::post('/notifications/create', 'Api\NotificationController@create');

// Payment method serializers
Route::get('/payment-methods', 'Api\PaymentMethodController@index');
Route::get('/payment-method/{id}', 'Api\PaymentMethodController@show');

// Price serializers
Route::get('/prices', 'Api\PriceController@index');
Route::get('/prices/{id}', 'Api\PriceController@show');
Route::post('/prices/{id}/update', 'Api\PriceController@update');
Route::post('/prices/{id}/delete', 'Api\PriceController@delete');
Route::post('/prices/create', 'Api\PriceController@create');

// Product serializers
Route::get('/products', 'Api\ProductController@index');
Route::get('/products/names', 'Api\ProductController@showByName');
Route::get('/products/manufacturers', 'Api\ProductController@showByManufacturer');
Route::get('/products/unverified', 'Api\ProductController@showUnverifiedProducts');
Route::get('/products/{id}', 'Api\ProductController@show');
Route::post('/products/checkout', 'Api\ProductController@checkoutProducts');
Route::post('/products/search/query', 'Api\ProductController@searchProducts');
Route::post('/products/search/ean', 'Api\ProductController@searchProductByEan');
Route::post('/products/search/manufacturer', 'Api\ProductController@searchProductsByManufacturer');
Route::post('/products/{id}/update', 'Api\ProductController@update');
Route::post('/products/{id}/delete', 'Api\ProductController@delete');
Route::post('/products/create', 'Api\ProductController@create');

// Purchase serializers -- AKA Transactions
Route::get('/transactions/shop/{id}', 'Api\PurchaseController@showTransactionsByShop');

// Shop serializers
Route::get('/shops', 'Api\ShopController@index');
Route::get('/shops/merchant_names', 'Api\ShopController@showByRegBusName');
Route::get('/shops/unverified', 'Api\ShopController@showUnverifiedMerchants');
Route::get('/shops/{id}', 'Api\ShopController@show');
Route::get('/shops/{id}/products/unverified', 'Api\ShopController@showUnverifiedProductsByShop');
Route::post('/shops/merchant_verification', 'Api\ShopController@merchantVerification');
Route::post('/shops/{id}/update', 'Api\ShopController@update');
Route::post('/shops/create', 'Api\ShopController@create');

// Support ticket serializers
Route::get('/support_tickets/{id}', 'Api\SupportTicketController@show');
Route::get('/support_tickets/reference_number/{reference_number}', 'Api\SupportTicketController@showSupportTicketByReferenceNumber');
Route::get('/support_tickets/mobile_number/{mobile_number}', 'Api\SupportTicketController@showByMobileNumber');
Route::post('/support_tickets/create', 'Api\SupportTicketController@create');

// Ticket serializers
Route::get('/tickets', 'Api\TicketController@index');
Route::get('/ticket/{id}', 'Api\TicketController@show');
Route::post('/ticket/{id}/update', 'Api\TicketController@update');
Route::post('/ticket/{id}/delete', 'Api\TicketController@delete');
Route::post('/ticket/create', 'Api\TicketController@create');

// Verification request serializers
Route::get('/verification_requests/{id}', 'Api\VerificationRequestController@show');
Route::post('/verification_requests/create', 'Api\VerificationRequestController@create');

Route::group(['middleware' => ['role:administrator,customer-support']], function() {
    Route::get('/support_tickets', 'Api\SupportTicketController@index');
    Route::get('/support_tickets/type/account_lock/', 'Api\SupportTicketController@showAllAccountLockRequestReview');
    Route::get('/support_tickets/user/{id}', 'Api\SupportTicketController@showByCustomerSupportUserId');
    Route::get('/support_tickets/email/{email}', 'Api\SupportTicketController@showByEmail');
    Route::get('/support_tickets/issue/{issue}', 'Api\SupportTicketController@showByIssue');
    Route::get('/support_tickets/priority/{priority}', 'Api\SupportTicketController@showByPriority');
    Route::get('/support_tickets/status/{status}', 'Api\SupportTicketController@showByIsResolved');
    Route::post('/support_tickets/compose', 'Api\SupportTicketController@compose');
    Route::post('/support_tickets/{id}/update', 'Api\SupportTicketController@update');
    Route::post('/support_tickets/{id}/delete', 'Api\SupportTicketController@delete');

    Route::get('/verification_requests', 'Api\VerificationRequestController@index');
    Route::get('/verification_requests/user/{id}', 'Api\VerificationRequestController@showByUserId');
    Route::get('/verification_requests/type/{type}', 'Api\VerificationRequestController@showByType');
    Route::get('/verification_requests/document/{document}', 'Api\VerificationRequestController@showByDocument');
    Route::get('/verification_requests/status/{status}', 'Api\VerificationRequestController@showByStatus');
    Route::get('/verification_requests/{id}/get_upload_path', 'Api\VerificationRequestController@showImagePath');
    Route::post('/verification_requests/{id}/update', 'Api\VerificationRequestController@update');
    Route::post('/verification_requests/{id}/delete', 'Api\VerificationRequestController@delete');
});

Route::group(['middleware' => 'role:administrator'], function() {
    Route::post('/announcements/{id}/update', 'Api\AnnouncementController@update');
    Route::post('/announcements/{id}/delete', 'Api\AnnouncementController@delete');
    Route::post('/announcements/create', 'Api\AnnouncementController@create');

    Route::post('/faqs/{id}/update', 'Api\FaqController@update');
    Route::post('/faqs/{id}/delete', 'Api\FaqController@delete');
    Route::post('/faqs/create', 'Api\FaqController@create');

    // Permission serializers
    Route::get('/permissions', 'Api\PermissionController@index');
    Route::get('/permissions/{id}', 'Api\PermissionController@show');
    Route::get('/permissions/users/{id}', 'Api\PermissionController@showPermissionsByUserId');
    Route::post('/permissions/{id}/update', 'Api\PermissionController@update');
    Route::post('/permissions/{id}/delete', 'Api\PermissionController@delete');
    Route::post('/permissions/create', 'Api\PermissionController@create');

    // Role serializers
    Route::get('/roles', 'Api\RoleController@index');
    Route::get('/roles/{id}', 'Api\RoleController@show');
    Route::get('/roles/users/{id}', 'Api\RoleController@showRolesByUserId');
    Route::post('/roles/users/{id}/add', 'Api\RoleController@addRoleToUserId');
    Route::post('/roles/users/{id}/delete', 'Api\RoleController@removeRoleOfUserId');
    Route::post('/roles/{id}/update', 'Api\RoleController@update');
    Route::post('/roles/{id}/delete', 'Api\RoleController@delete');
    Route::post('/roles/create', 'Api\RoleController@create');

    // Purchase serializers -- AKA Transactions
    Route::get('/transactions', 'Api\PurchaseController@index');
    Route::get('/transactions/today', 'Api\PurchaseController@showTransactionsToday');
    Route::get('/transactions/top5/cities', 'Api\PurchaseController@showTransactionsTop5Cities');
    Route::get('/transactions/top5/manufacturers', 'Api\PurchaseController@showTransactionsTop5Manufacturers');
    Route::get('/transactions/top5/merchants', 'Api\PurchaseController@showTransactionsTop5Merchants');
    Route::get('/transactions/top5/products', 'Api\PurchaseController@showTransactionsTop5Products');
    Route::get('/transactions/latest/{value}', 'Api\PurchaseController@showLatestTransactions');
    Route::get('/transactions/{id}', 'Api\PurchaseController@show');
    Route::post('/sales/manufacturers', 'Api\PurchaseController@salesByManufacturer');
    Route::post('/sales/merchants', 'Api\PurchaseController@salesByMerchant');
    Route::post('/sales/products', 'Api\PurchaseController@salesByProduct');
    Route::post('/sales/barangay', 'Api\PurchaseController@salesByBarangay');
    Route::post('/sales/city', 'Api\PurchaseController@salesByCity');
    Route::post('/sales/province', 'Api\PurchaseController@salesByProvince');

    Route::post('/shops/{id}/delete', 'Api\ShopController@delete');

    // Voucher serializers
    Route::get('/voucher/{id}', 'Api\VoucherController@show');
    Route::post('/voucher/{id}/update', 'Api\VoucherController@update');
    Route::post('/voucher/{id}/delete', 'Api\VoucherController@delete');
    Route::post('/voucher/create', 'Api\VoucherController@create');

    // Payment method
    Route::post('/payment_methods/{id}/update', 'Api\PaymentMethodController@update');
    Route::post('/payment_methods/create', 'Api\PaymentMethodController@create');
});

//// temporary
// Voucher Order Verify/Reject
Route::post('/voucher/{id}/verify', 'Api\VoucherOrderController@verify');
Route::post('/voucher/{id}/reject', 'Api\VoucherOrderController@reject');

// Voucher orders
Route::post('/voucher/orders', 'Api\VoucherOrderController@orders');

// Voucher orders To Verify
Route::post('/voucher/orders/to-verify', 'Api\VoucherOrderController@toVerify');

// Voucher orders To History
Route::post('/voucher/orders/history', 'Api\VoucherOrderController@history');

// Voucher orders count Pending Request
Route::get('/voucher/orders/pending/request', 'Api\VoucherOrderController@countPendingRequest');

// Voucher orders sum unpaid
Route::get('/voucher/orders/unpaid', 'Api\VoucherOrderController@unpaid');

// Voucher orders in purchase
Route::get('/voucher/orders/fees/collected', 'Api\VoucherOrderController@feesCollected');

// Voucher orders voucher sold
Route::get('/voucher/orders/sold', 'Api\VoucherOrderController@voucherSold');

// Cashout history
Route::get('/cashouts', 'Api\CashoutController@cashouts');

// Cashout Pending
Route::get('/cashouts/pending/request', 'Api\CashoutController@pendingCashouts');

// Cashout Count Pending
Route::get('/cashouts/count/pending', 'Api\CashoutController@countPendingRequest');

// Cashout Count successful payments
Route::get('/cashouts/count/successful/payments', 'Api\CashoutController@countSuccessfulPayment');

// Cashout sum successful payments
Route::get('/cashouts/sum/successful/payments', 'Api\CashoutController@sumOfSuccessfulCashouts');

// Cashout list payment successful
Route::get('/cashouts/payments/successful', 'Api\CashoutController@approvedCashouts');

// Cashout fees collected
Route::get('/cashouts/fees/collected', 'Api\CashoutController@feesTotalCollected');

// Cashout counts today's transaction
Route::get('/cashouts/count/transaction', 'Api\CashoutController@countCashoutTransactionOfTheDay');

// Cashout fees collected today's transaction
Route::get('/cashouts/fees/collected/today', 'Api\CashoutController@feesCollectedToday');

// Cashout approve/reject
Route::post('/cashout/{id}/approve','Api\CashoutController@approve');
Route::post('/cashout/{id}/reject','Api\CashoutController@reject');

// Dragonpay payout processor
Route::get('/processors', 'Api\PayoutProcessorController@index');
Route::get('/processor/{id}', 'Api\PayoutProcessorController@show');
Route::post('/processor/{id}/update', 'Api\PayoutProcessorController@update');
Route::post('/processor/{id}/delete', 'Api\PayoutProcessorController@delete');
Route::post('/processor/create', 'Api\PayoutProcessorController@create');

// B2B
Route::prefix('/v1')->group(function () {
    // // Test
    // Route::get('mail', function(){
    //     return (new App\Notifications\CheckoutProducts(User::with('address')->find(1), PurchaseItem::with(['product.owner'])->where('batch_code','9357eb58-6b83-4c2b-ae03-e3653f5dceb4')->get()))->toMail('carlxaeron09@gmail.com');
    // });

    // Settings
    Route::get('settings', function(){
        return response()->json([
            'message' => 'Fetched Settings',
            'error' => false,
            'statusCode' => 200,
            'results' => [
                'app_type'=>config('UCC.type'),
                'orders'=>[
                    'status'=>config('purchase_statuses.status.v1'),
                    'purchase_status'=>config('purchase_statuses.purchase_status.v1'),
                    'payment_method'=>config('purchase_statuses.payment_method.v1'),
                ]
            ]
        ], 200);
    });

    // Login
    Route::post('login', [App\Http\Controllers\Api\UserController::class, 'loginV1']);
    Route::post('login-with-red', [App\Http\Controllers\Api\UserController::class, 'loginV1WithRed']);

    // Validate OTP on email
    Route::post('validate-otp', 'Api\UserController@validateOtpV1');

    // Register
    Route::post('register', [App\Http\Controllers\Api\UserController::class, 'registerV1']);

    // Products
    Route::get('/products', 'Api\ProductController@indexV1');
    Route::get('/products/related', 'Api\ProductController@getRelatedProducts');

    // Product
    Route::get('/product/{id}', 'Api\ProductController@show');

    // Category
    Route::get('category', [App\Http\Controllers\Api\CategoryController::class, 'index']);
    
    // Requires TOKEN
    Route::middleware('auth:api')->group(function () {

        // User - Product
        Route::get('/products/me', 'Api\ProductController@myProducts');
        // User -
        Route::get('/user/me', 'Api\UserController@getUserInfo');
        Route::post('/user/me/address', 'Api\UserController@updateUserAddress');
        // User - Purchases
        Route::get('/order/me', 'Api\UserController@getUserPurchases');

        // Order
        Route::put('order','Api\OrderController@update');

        // Checkout
        Route::post('/checkout', [App\Http\Controllers\Api\ProductController::class, 'checkoutProductsV1']);
        
        // Product Image
        Route::delete('/product/image/{id}', 'Api\ProductImageController@destroy');
        
        // Admin/Merchant Role
        Route::middleware(['role:administrator,merchant',function($response, $next){
            if(Auth::user()->merchant_level === 0) abort(403);
            return $next($response);
        }])->group(function () {
            // Product
            Route::put('/product/{id}', 'Api\ProductController@updateV1');
            Route::delete('/product/{id}', 'Api\ProductController@deleteV1');
            Route::post('/product', 'Api\ProductController@createV1');
            // Order
            Route::get('orders/me','Api\OrderController@getAllUserOrders');
        });

        // Admin Role
        Route::middleware(['role:administrator'])->group(function () {
            // Categories
            Route::post('category', [App\Http\Controllers\Api\CategoryController::class, 'store']); 
            Route::put('category/{id}', [App\Http\Controllers\Api\CategoryController::class, 'update']);
            Route::delete('category/{id}', [App\Http\Controllers\Api\CategoryController::class, 'destroy']);
            // Order
            Route::get('orders','Api\OrderController@getAllOrders');
            // Users
            Route::get('users','Api\UserController@indexV1');
        }); 
    });
});