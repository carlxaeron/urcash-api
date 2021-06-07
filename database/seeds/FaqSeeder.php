<?php

use Illuminate\Database\Seeder;
use App\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Faq::create([
            'category' => 'Uncategorized',
            'question' => 'Test question',
            'answer' => 'Test answer'
        ]);

        Faq::create([
            'category' => 'Change Info Request',
            'question' => 'I cannot change my mobile number and email. What is happening?',
            'answer' =>
                'For requests to change their phone number or email address, verification and submission of documents
                will be required. These additional verifications are necessary to avoid fraudulent transactions.'
        ]);

        Faq::create([
            'category' => 'Login',
            'question' => 'I didn\'t receive the code. Where is it?',
            'answer' =>
                'Please wait for the code to be sent to your mobile number. It may take up to a minute for you to
                receive the code through your mobile number.'
        ]);

        Faq::create([
            'category' => 'Payment Methods',
            'question' => 'Are there other available payment methods for me to cash-in/cash-out my earnings?',
            'answer' =>
                'No. Right now, Dragonpay is the supported payment method. Other payment methods will be added soon.'
        ]);

        Faq::create([
            'category' => 'Registration',
            'question' => 'I am using my mobile number. Why can\'t I sign up?',
            'answer' =>
                'The mobile number has already been used. If you own the mobile number, you may use the Forget Password
                feature to get access to your account.'
        ]);
    }
}
