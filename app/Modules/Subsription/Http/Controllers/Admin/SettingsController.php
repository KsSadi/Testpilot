<?php

namespace App\Modules\Subsription\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $currencySettings = SystemSetting::where('group', 'currency')->get();
        $paymentSettings = SystemSetting::where('group', 'payment')->get();
        $paymentInstructions = SystemSetting::where('group', 'payment_instructions')->get();
        
        // Group instructions by payment method
        $bkashSettings = $paymentInstructions->filter(fn($s) => str_starts_with($s->key, 'payment_bkash'));
        $nagadSettings = $paymentInstructions->filter(fn($s) => str_starts_with($s->key, 'payment_nagad'));
        $rocketSettings = $paymentInstructions->filter(fn($s) => str_starts_with($s->key, 'payment_rocket'));
        $bankSettings = $paymentInstructions->filter(fn($s) => str_starts_with($s->key, 'payment_bank'));
        
        return view('Subsription::admin.settings', compact(
            'currencySettings', 
            'paymentSettings', 
            'bkashSettings',
            'nagadSettings',
            'rocketSettings',
            'bankSettings'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            
            if ($setting) {
                $setting->update(['value' => $value]);
                cache()->forget("setting_{$key}");
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
