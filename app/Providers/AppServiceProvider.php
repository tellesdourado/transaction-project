<?php

namespace App\Providers;

use App\Repositories\Gateway\Interfaces\ITransactionRepository;
use App\Repositories\Gateway\Interfaces\IWalletRepository;
use App\Repositories\Gateway\TransactionRepository;
use App\Repositories\Gateway\WalletRepository;
use App\Repositories\Notification\Interfaces\IFailedNotificationRepository;
use App\Repositories\Notification\FailedNotificationRepository;
use App\Repositories\Users\Interfaces\IUserProfileRepository;
use App\Repositories\Users\Interfaces\IUserRepository;
use App\Repositories\Users\Interfaces\IUserTypeRepository;
use App\Repositories\Users\UserProfileRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserTypeRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IFailedNotificationRepository::class, FailedNotificationRepository::class);
        $this->app->bind(IUserTypeRepository::class, UserTypeRepository::class);
        $this->app->bind(IUserProfileRepository::class, UserProfileRepository::class);
        $this->app->bind(ITransactionRepository::class, TransactionRepository::class);
        $this->app->bind(IWalletRepository::class, WalletRepository::class);
    }
}
