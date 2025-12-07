<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Currency Settings
            [
                'key' => 'currency_usd_to_bdt_rate',
                'value' => '110',
                'type' => 'number',
                'group' => 'currency',
                'label' => 'USD to BDT Conversion Rate',
                'description' => 'Exchange rate from US Dollar to Bangladeshi Taka',
            ],
            [
                'key' => 'currency_default',
                'value' => 'USD',
                'type' => 'string',
                'group' => 'currency',
                'label' => 'Default Currency',
                'description' => 'Default currency for the system',
            ],
            [
                'key' => 'currency_enabled',
                'value' => 'USD,BDT',
                'type' => 'string',
                'group' => 'currency',
                'label' => 'Enabled Currencies',
                'description' => 'Comma-separated list of enabled currencies',
            ],
            
            // Payment Settings
            [
                'key' => 'payment_stripe_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Enable Stripe Payments',
                'description' => 'Enable or disable Stripe payment gateway',
            ],
            [
                'key' => 'payment_bkash_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Enable bKash Payments',
                'description' => 'Enable or disable bKash payment gateway',
            ],
            [
                'key' => 'payment_nagad_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Enable Nagad Payments',
                'description' => 'Enable or disable Nagad payment gateway',
            ],
            [
                'key' => 'payment_rocket_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Enable Rocket Payments',
                'description' => 'Enable or disable Rocket payment gateway',
            ],
            
            // Payment Instructions
            [
                'key' => 'payment_bkash_number',
                'value' => '01712345678',
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'bKash Number',
                'description' => 'bKash merchant/personal number for receiving payments',
            ],
            [
                'key' => 'payment_bkash_type',
                'value' => 'Personal',
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'bKash Account Type',
                'description' => 'Personal or Merchant account',
            ],
            [
                'key' => 'payment_bkash_instructions',
                'value' => "1. Go to your bKash app\n2. Select 'Send Money'\n3. Enter the number: 01712345678\n4. Enter the amount\n5. Enter your PIN and confirm\n6. Save the transaction ID",
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'bKash Payment Instructions',
                'description' => 'Step-by-step instructions for bKash payment',
            ],
            [
                'key' => 'payment_nagad_number',
                'value' => '01812345678',
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'Nagad Number',
                'description' => 'Nagad merchant/personal number for receiving payments',
            ],
            [
                'key' => 'payment_nagad_type',
                'value' => 'Personal',
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'Nagad Account Type',
                'description' => 'Personal or Merchant account',
            ],
            [
                'key' => 'payment_nagad_instructions',
                'value' => "1. Open Nagad app\n2. Tap 'Send Money'\n3. Enter number: 01812345678\n4. Enter amount and proceed\n5. Confirm with your PIN\n6. Note down the transaction ID",
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'Nagad Payment Instructions',
                'description' => 'Step-by-step instructions for Nagad payment',
            ],
            [
                'key' => 'payment_rocket_number',
                'value' => '019123456789',
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'Rocket Number',
                'description' => 'Rocket account number for receiving payments',
            ],
            [
                'key' => 'payment_rocket_instructions',
                'value' => "1. Dial *322# from your mobile\n2. Select 'Send Money'\n3. Enter Rocket number: 019123456789\n4. Enter the amount\n5. Enter your PIN\n6. Save the transaction reference",
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'Rocket Payment Instructions',
                'description' => 'Step-by-step instructions for Rocket payment',
            ],
            [
                'key' => 'payment_bank_name',
                'value' => 'Dutch Bangla Bank Limited',
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'Bank Name',
                'description' => 'Name of the bank for bank transfers',
            ],
            [
                'key' => 'payment_bank_account_name',
                'value' => 'Your Company Name',
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'Bank Account Name',
                'description' => 'Account holder name',
            ],
            [
                'key' => 'payment_bank_account_number',
                'value' => '1234567890123',
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'Bank Account Number',
                'description' => 'Bank account number for receiving payments',
            ],
            [
                'key' => 'payment_bank_branch',
                'value' => 'Gulshan Branch, Dhaka',
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'Bank Branch',
                'description' => 'Branch name and location',
            ],
            [
                'key' => 'payment_bank_routing',
                'value' => '090123456',
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'Routing Number',
                'description' => 'Bank routing number (if applicable)',
            ],
            [
                'key' => 'payment_bank_instructions',
                'value' => "1. Visit your bank or use online banking\n2. Select 'Fund Transfer' or 'Pay'\n3. Bank: Dutch Bangla Bank Limited\n4. Account: 1234567890123\n5. Name: Your Company Name\n6. Enter amount and complete transfer\n7. Keep the transaction receipt",
                'type' => 'string',
                'group' => 'payment_instructions',
                'label' => 'Bank Transfer Instructions',
                'description' => 'Step-by-step instructions for bank transfer',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
