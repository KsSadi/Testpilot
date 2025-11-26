<?php

namespace App\Modules\User\Services\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $gateway;
    protected $config;

    public function __construct()
    {
        $this->gateway = config('services.sms.gateway', 'sslwireless');
        $this->config = config("services.sms.gateways.{$this->gateway}", []);
    }

    /**
     * Send SMS.
     */
    public function send(string $mobile, string $message): array
    {
        try {
            if ($this->gateway === 'sslwireless') {
                return $this->sendViaSSLWireless($mobile, $message);
            }

            // Add more gateways here in the future
            
            return [
                'success' => false,
                'message' => 'SMS gateway not configured',
            ];
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to send SMS',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send OTP via SMS.
     */
    public function sendOtp(string $mobile, string $otp): array
    {
        $message = "Your verification code is: {$otp}. Valid for 5 minutes. Do not share this code.";
        return $this->send($mobile, $message);
    }

    /**
     * Send SMS via SSL Wireless gateway.
     */
    protected function sendViaSSLWireless(string $mobile, string $message): array
    {
        if (empty($this->config)) {
            return [
                'success' => false,
                'message' => 'SSL Wireless not configured',
            ];
        }

        $apiToken = $this->config['api_token'] ?? '';
        $sid = $this->config['sid'] ?? '';
        $domain = $this->config['domain'] ?? '';

        if (empty($apiToken) || empty($sid) || empty($domain)) {
            return [
                'success' => false,
                'message' => 'SSL Wireless credentials missing',
            ];
        }

        // Clean mobile number (remove +880, keep 11 digits)
        $mobile = $this->cleanMobileNumber($mobile);

        // SSL Wireless API endpoint
        $url = "https://smsplus.sslwireless.com/api/v3/send-sms";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiToken,
            'Accept' => 'application/json',
        ])->post($url, [
            'api_token' => $apiToken,
            'sid' => $sid,
            'msisdn' => $mobile,
            'sms' => $message,
            'csms_id' => time() . rand(1000, 9999),
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            return [
                'success' => true,
                'message' => 'SMS sent successfully',
                'data' => $data,
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to send SMS',
            'error' => $response->body(),
        ];
    }

    /**
     * Clean mobile number for Bangladesh format.
     */
    protected function cleanMobileNumber(string $mobile): string
    {
        // Remove all non-numeric characters
        $mobile = preg_replace('/[^0-9]/', '', $mobile);

        // Remove country code if present
        if (substr($mobile, 0, 3) === '880') {
            $mobile = substr($mobile, 3);
        } elseif (substr($mobile, 0, 2) === '88') {
            $mobile = substr($mobile, 2);
        }

        // Ensure it starts with 0 and is 11 digits
        if (strlen($mobile) === 10) {
            $mobile = '0' . $mobile;
        }

        return $mobile;
    }

    /**
     * Validate mobile number format.
     */
    public function isValidMobile(string $mobile): bool
    {
        $mobile = $this->cleanMobileNumber($mobile);
        
        // Bangladesh mobile number format: 01XXXXXXXXX (11 digits)
        return preg_match('/^01[3-9]\d{8}$/', $mobile);
    }
}
