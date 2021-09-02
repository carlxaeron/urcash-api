<?php

namespace App\Providers;

use App\Observers\ProductObserver;
use App\Observers\PurchaseItemObserver;
use App\Product;
use App\PurchaseItem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Interfaces\UserInterface',
            'App\Repositories\UserRepository'
        );
        $this->app->bind(
            'App\Interfaces\AddressInterface',
            'App\Repositories\AddressRepository'
        );
        $this->app->bind(
            'App\Interfaces\AdminLogInterface',
            'App\Repositories\AdminLogRepository'
        );
        $this->app->bind(
            'App\Interfaces\AnnouncementInterface',
            'App\Repositories\AnnouncementRepository'
        );
        $this->app->bind(
            'App\Interfaces\BankInterface',
            'App\Repositories\BankRepository'
        );
        $this->app->bind(
            'App\Interfaces\CashoutInterface',
            'App\Repositories\CashoutRepository'
        );
        $this->app->bind(
            'App\Interfaces\EWalletInterface',
            'App\Repositories\EWalletRepository'
        );
        $this->app->bind(
            'App\Interfaces\FaqInterface',
            'App\Repositories\FaqRepository'
        );
        $this->app->bind(
            'App\Interfaces\NotificationInterface',
            'App\Repositories\NotificationRepository'
        );
        $this->app->bind(
            'App\Interfaces\NotificationTypeInterface',
            'App\Repositories\NotificationTypeRepository'
        );
        $this->app->bind(
            'App\Interfaces\PaymentMethodInterface',
            'App\Repositories\PaymentMethodRepository'
        );
        $this->app->bind(
            'App\Interfaces\PayoutProcessorInterface',
            'App\Repositories\PayoutProcessorRepository'
        );
        $this->app->bind(
            'App\Interfaces\PermissionInterface',
            'App\Repositories\PermissionRepository'
        );
        $this->app->bind(
            'App\Interfaces\PriceInterface',
            'App\Repositories\PriceRepository'
        );
        $this->app->bind(
            'App\Interfaces\ProductInterface',
            'App\Repositories\ProductRepository'
        );
        $this->app->bind(
            'App\Interfaces\PurchaseInterface',
            'App\Repositories\PurchaseRepository'
        );
        $this->app->bind(
            'App\Interfaces\RoleInterface',
            'App\Repositories\RoleRepository'
        );
        $this->app->bind(
            'App\Interfaces\ShopInterface',
            'App\Repositories\ShopRepository'
        );
        $this->app->bind(
            'App\Interfaces\SupportTicketInterface',
            'App\Repositories\SupportTicketRepository'
        );
        $this->app->bind(
            'App\Interfaces\TicketInterface',
            'App\Repositories\TicketRepository'
        );
        $this->app->bind(
            'App\Interfaces\TicketPurchaseInterface',
            'App\Repositories\TicketPurchaseRepository'
        );
        $this->app->bind(
            'App\Interfaces\VerificationRequestInterface',
            'App\Repositories\VerificationRequestRepository'
        );
        $this->app->bind(
            'App\Interfaces\VoucherInterface',
            'App\Repositories\VoucherRepository'
        );
        $this->app->bind(
            'App\Interfaces\VoucherAccountInterface',
            'App\Repositories\VoucherAccountRepository'
        );
        $this->app->bind(
            'App\Interfaces\VoucherAccountTransactionInterface',
            'App\Repositories\VoucherAccountTransactionRepository'
        );
        $this->app->bind(
            'App\Interfaces\VoucherOrderInterface',
            'App\Repositories\VoucherOrderRepository'
        );
        $this->app->bind(
            'App\Interfaces\WalletInterface',
            'App\Repositories\WalletRepository'
        );
        $this->app->bind(
            'App\Interfaces\WalletTransactionInterface',
            'App\Repositories\WalletTransactionRepository'
        );
        $this->app->bind(
            'App\Interfaces\CategoryInterface',
            'App\Repositories\CategoryRepository'
        );
        $this->app->bind(
            'App\Interfaces\ProductImageInterface',
            'App\Repositories\ProductImageRepository'
        );
        $this->app->bind(
            'App\Interfaces\OrderInterface',
            'App\Repositories\OrderRepository'
        );
        $this->app->bind(
            'App\Interfaces\PaymentInterface',
            'App\Repositories\PaymentRepository'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        PurchaseItem::observe(PurchaseItemObserver::class);
        Product::observe(ProductObserver::class);
    }
}
