<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            'PermissionSeeder',
            'RoleSeeder',
            'UserSeeder',
            'FaqSeeder',
            'PayoutProcessorSeeder',
            'VoucherSeeder',
            'NotificationTypeSeeder',
            'PaymentMethodSeeder',
            'BanksSeeder',
            'EWalletSeeder'
        ]);

        $this->command->info('Tables seeded successfully!');
    }
}
