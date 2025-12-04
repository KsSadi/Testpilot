# AI Pricing Management - Database-Driven Pricing

## Overview
Pricing for all AI models is now **stored in the database** instead of hardcoded in provider files. This allows you to update pricing through the admin UI without any code changes.

---

## How It Works

### 1. **Database Structure**

**Table:** `ai_settings`

| Column | Type | Description |
|--------|------|-------------|
| `provider_id` | Foreign Key | Links to `ai_providers` table |
| `key` | String | Always `'model_pricing'` for pricing records |
| `value` | String | Model name (e.g., `'gemini-2.5-flash'`) |
| `input_price` | Decimal(10,4) | Price per 1M input tokens |
| `output_price` | Decimal(10,4) | Price per 1M output tokens |
| `type` | String | `'pricing'` |
| `group` | String | `'pricing'` |

---

## How to Update Pricing

### **Option 1: Admin UI (Recommended)**

1. Navigate to `/ai/settings`
2. Click **"Show/Hide"** under "Model Pricing" section
3. Update the input/output prices for each model
4. Click **Save**
5. Pricing is **immediately active** - no deployment needed!

### **Option 2: Database Directly**

```sql
UPDATE ai_settings 
SET input_price = 0.50, output_price = 3.00 
WHERE provider_id = 2 
  AND key = 'model_pricing' 
  AND value = 'gemini-2.5-flash';
```

### **Option 3: PHP Code**

```php
use App\Modules\AI\Models\AIProvider;

$gemini = AIProvider::where('name', 'gemini')->first();
$gemini->updatePricing('gemini-2.5-flash', 0.50, 3.00);
```

### **Option 4: Update Seeder**

Edit `database/seeders/AIProvidersSeeder.php`:

```php
$geminiPricing = [
    'gemini-2.5-flash' => ['input' => 0.50, 'output' => 3.00], // Change here
];
```

Then run:
```bash
php artisan db:seed --class=AIProvidersSeeder
```

---

## Current Pricing (as of Jan 2025)

### **OpenAI**
| Model | Input (per 1M) | Output (per 1M) |
|-------|----------------|-----------------|
| gpt-4 | $30.00 | $60.00 |
| gpt-4-turbo | $10.00 | $30.00 |
| gpt-4o | $5.00 | $15.00 |
| gpt-3.5-turbo | $0.50 | $1.50 |

### **Gemini**
| Model | Input (per 1M) | Output (per 1M) |
|-------|----------------|-----------------|
| gemini-3-pro-preview | $2.00 | $12.00 |
| gemini-2.5-pro | $1.25 | $10.00 |
| gemini-2.5-flash | $0.30 | $2.50 |
| gemini-2.5-flash-lite | $0.10 | $0.40 |
| gemini-2.0-flash | $0.10 | $0.40 |
| gemini-2.0-flash-lite | $0.075 | $0.30 |

### **DeepSeek**
| Model | Input (per 1M) | Output (per 1M) |
|-------|----------------|-----------------|
| deepseek-chat | $0.28 | $0.42 |
| deepseek-reasoner | $0.28 | $0.42 |

---

## Code Changes

### **Before (Hardcoded)**
```php
// GeminiProvider.php
private const PRICING = [
    'gemini-2.5-flash' => ['input' => 0.30, 'output' => 2.50],
];

public function estimateCost(int $promptTokens, int $completionTokens): float
{
    $pricing = self::PRICING[$model] ?? self::PRICING['gemini-2.5-flash'];
    // ...
}
```

### **After (Database-Driven)**
```php
// GeminiProvider.php - No hardcoded pricing!
public function estimateCost(int $promptTokens, int $completionTokens): float
{
    $pricing = $this->provider->getPricing($model); // Reads from database
    // ...
}
```

---

## API Methods

### **AIProvider Model**

```php
// Get pricing for specific model
$pricing = $provider->getPricing('gemini-2.5-flash');
// Returns: ['input' => 0.30, 'output' => 2.50]

// Get all pricing for provider
$allPricing = $provider->getAllPricing();
// Returns: ['gemini-2.5-flash' => ['input' => 0.30, 'output' => 2.50], ...]

// Update pricing
$provider->updatePricing('gemini-2.5-flash', 0.50, 3.00);

// Get pricing settings relationship
$provider->pricingSettings; // Eloquent relationship
```

---

## Benefits

✅ **No Code Deployment** - Update prices instantly from admin UI  
✅ **No Downtime** - Changes are immediate  
✅ **Historical Tracking** - Database timestamps track when prices changed  
✅ **Per-Model Control** - Different pricing for each model  
✅ **Future-Proof** - New models automatically get pricing fields  
✅ **Audit Trail** - Can track who changed prices (with user_id column)  

---

## When Pricing Changes

**Example: Google announces Gemini 2.5 Flash price drop**

1. Open `/ai/settings`
2. Click "Show/Hide" under Google Gemini pricing
3. Find "Gemini 2.5 Flash"
4. Change: Input $0.30 → $0.20, Output $2.50 → $1.50
5. Click Save
6. **Done!** All new requests use updated pricing immediately

**No server restart, no code changes, no deployment required!**

---

## Migration Files

- `2025_12_04_090203_add_pricing_to_ai_settings_table.php` - Added price columns
- `2025_12_04_090327_add_provider_id_to_ai_settings_table.php` - Added provider relationship

---

## Verification

Check current pricing in database:
```bash
php artisan tinker
>>> AISetting::where('key', 'model_pricing')->get(['provider_id', 'value', 'input_price', 'output_price'])
```

Test cost calculation:
```bash
php artisan tinker
>>> $gemini = AIProvider::where('name', 'gemini')->first();
>>> $pricing = $gemini->getPricing('gemini-2.5-flash');
>>> print_r($pricing);
```

---

## UI Screenshot Locations

- **Settings Page**: `/ai/settings`
- **Pricing Section**: Click "Show/Hide" next to "Model Pricing (Per 1M Tokens)"
- **Each model** has separate input/output price fields
- **Blue info box** confirms pricing is database-driven

---

## Support

If pricing appears incorrect:
1. Check database: `SELECT * FROM ai_settings WHERE key = 'model_pricing'`
2. Re-seed: `php artisan db:seed --class=AIProvidersSeeder`
3. Clear cache: `php artisan cache:clear`
4. Test connection: Click "Test" button on provider card
