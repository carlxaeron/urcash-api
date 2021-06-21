<?php

use App\Address;
use App\Role;
use App\Shop;
use App\User;
use App\VoucherAccount;
use App\Wallet;
use App\Http\Helper\Utils\GenerateRandomIntegers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        $qrCode = new GenerateRandomIntegers(1, 9, 12);
        $password = Hash::make('1234');

        $address = Address::create(array(
            'street' => 'street 1 user',
            'barangay' => 'barangay of user',
            'city' => 'city of user',
            'province' => 'province of user',
            'country' => 'Philippines'
        ));

        $user = User::create(array(
            'first_name' => 'Carl',
            'middle_name' => 'V',
            'last_name' => 'Manuel',
            'mobile_number' => '639953076751',
            'email' => 'carlxaeron09@gmail.com',
            'password' => $password,
            'address_id' => $address->id
        ));

        // Wallet::create(array(
        //     'qr_code' => $qrCode->generate(),
        //     'user_id' => (int) $user->id,
        //     'available_balance' => 0
        // ));

        $administrator_role = Role::where('slug', '=', 'administrator')->first();
        $user->roles()->attach($administrator_role); // Add role

        $address2 = Address::create(array(
            'street' => 'Some street',
            'barangay' => 'Barangay Hope and Dreams',
            'city' => 'Baguio',
            'province' => 'Benguet',
            'country' => 'Philippines'
        ));

        $user2 = User::create(array(
            'first_name' => 'Carl2',
            'middle_name' => 'V2',
            'last_name' => 'Manuel2',
            'birthdate' => '1995-04-09',
            'mobile_number' => '639301075863',
            'email' => 'carllouismanuel09@gmail.com',
            'password' => $password,
            'address_id' => $address2->id
        ));

        $shop_address = Address::create(array(
            'complete_address' => 'Kevin\'s address',
            'street' => 'Some street',
            'barangay' => 'Barangay Hope and Dreams',
            'city' => 'Baguio',
            'province' => 'Benguet',
            'country' => 'Philippines'
        ));

       $shop = Shop::create(array(
            'user_id' => $user2->id,
            'address_id' => $shop_address->id,
            'reg_bus_name' => 'Thrive Media sari-sari store',
            'dti' => '1234567',
            'bir_reg_cert' => '123456789ABCDE',
            'mayors_permit' => '1234'
        ));

        VoucherAccount::create(array(
            'shop_id' => $shop->id,
            'voucher_balance' => 0.00
        ));

        $customer_support_role = Role::where('slug', '=', 'customer-support')->first();
        $merchant_role = Role::where('slug', '=', 'merchant')->first();

        // Add roles
        $user2->roles()->attach($customer_support_role);
        $user2->roles()->attach($merchant_role);
    }
}
