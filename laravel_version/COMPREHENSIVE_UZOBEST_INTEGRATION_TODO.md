# COMPREHENSIVE UZOBEST VTU INTEGRATION TODO
**Date:** December 5, 2025  
**Objective:** Ensure 100% end-to-end compatibility with Uzobest API for all supported services

---

## 🎯 SUPPORTED SERVICES
1. **Airtime** (VTU)
2. **Data** (All networks)
3. **Electricity** (Bill Payment)
4. **Cable TV** (DSTV, GOTV, Startimes)

---

## ✅ COMPLETED ITEMS

### Database & Models
- ✅ Migration: Added `cost_price`, `selling_price`, `profit_margin`, `uzobest_*_id` columns to all service tables
- ✅ Migration: Removed agent/vendor pricing tiers
- ✅ DataPlan model updated with unified pricing methods
- ✅ CablePlan model updated with unified pricing methods
- ✅ ElectricityProvider model updated with unified pricing methods
- ✅ Models use `selling_price` for all users (no tiers)

### Admin UI
- ✅ Data plans admin page shows: Uzobest Cost | Selling Price | Profit with color coding
- ✅ Data plans simplified edit modal (selling price only)
- ✅ Cable plans table shows unified pricing columns
- ✅ Cable plans simplified edit modal created
- ✅ Electricity providers table shows unified pricing columns
- ✅ DataTables CSS/JS added to data and cable plans pages

### API Integration
- ✅ Ported_number parameter added to airtime purchase UI and controller
- ✅ Ported_number parameter added to data purchase flow
- ✅ UzobestApiAdapter transforms airtime_type correctly

---

## 🔴 CRITICAL FIXES NEEDED

### 1. AIRTIME API - Invalid Type Error
**Issue:** Uzobest API expects `airtime_type: "VTU"` but we may be sending invalid values

**Files to Fix:**
- `app/Services/UzobestApiAdapter.php` (line 74)
  - ✅ Already has `getAirtimeType()` method
  - ⚠️ **FIX:** Ensure default is "VTU" not "Share and Sell"
  
- `app/Http/Controllers/AirtimeController.php`
  - ⚠️ **CHECK:** Verify `airtime_type` parameter validation
  - ⚠️ **FIX:** Default to "VTU" if not provided

**Uzobest API Contract:**
```json
{
  "network": 1,  // Integer network ID
  "amount": 100,
  "mobile_number": "08012345678",
  "Ported_number": true,
  "airtime_type": "VTU"  // Must be: "VTU", "awuf4U", or "Share and Sell"
}
```

---

## 🚨 HIGH PRIORITY TASKS

### 2. USE APILINK TABLE FOR CONFIG
**Current State:** Multiple config sources (ApiConfig, hardcoded values)  
**Goal:** Single source of truth in `apilinks` table

**Tasks:**
- [ ] Migrate Uzobest API token to `apilinks` table
  - name: "uzobest_api"
  - type: "primary"
  - value: "https://uzobestgsm.com/api/"
  - auth_type: "token"
  - auth_params: `{"token": "66f2e5c39ac8640f13cd888f161385b12f7e5e92"}`
  
- [ ] Update ExternalApiService to read from ApiLink model
- [ ] Remove hardcoded API URLs and tokens
- [ ] Add ApiLink seeder for Uzobest configuration

**Files to Modify:**
- `app/Services/ExternalApiService.php`
- `database/seeders/ApiLinkSeeder.php` (create if not exists)

---

### 3. ADMIN PRICE SYNC FROM UZOBEST

#### 3.1 Data Plans Sync
**Endpoint:** `GET https://uzobestgsm.com/api/network/`  
**Returns:** All networks with their data plans and prices

**Tasks:**
- [ ] Create `SyncDataPlansCommand` artisan command
- [ ] Implement sync logic in `DataPlanController`:
  - Fetch plans from Uzobest
  - Update `cost_price` from API
  - Set `uzobest_plan_id` for tracking
  - Preserve admin-customized `selling_price`
  - Calculate new `profit_margin`
  
- [ ] Add "Sync Prices" button to data plans admin page
- [ ] Show last sync time and status

**Files:**
- `app/Console/Commands/SyncDataPlansCommand.php` (create)
- `app/Http/Controllers/Admin/DataPlanController.php` (enhance sync method)
- `resources/views/admin/data-plans/index.blade.php`

#### 3.2 Cable Plans Sync
**Note:** Uzobest doesn't provide cable plan listing API

**Tasks:**
- [ ] Remove "Sync" button from cable plans (manual entry only)
- [ ] Update cable plans admin to clarify manual pricing
- [ ] Document cable plan IDs in admin interface

#### 3.3 Electricity Providers Sync
**Note:** Uzobest doesn't provide disco listing API

**Tasks:**
- [ ] Remove "Sync" button from electricity (manual entry only)
- [ ] Update electricity admin to clarify manual pricing
- [ ] Document disco IDs in admin interface

---

### 4. REMOVE MANUAL CREATION ENDPOINTS

**Goal:** Admin only sets selling prices, all cost prices come from Uzobest

**Tasks:**
- [ ] Remove "Add New" button from data plans (sync only)
- [ ] Keep "Add New" for cable plans (no Uzobest listing)
- [ ] Keep "Add New" for electricity providers (no Uzobest listing)
- [ ] Update DataPlanController to block manual creation
- [ ] Add validation to prevent creating plans without Uzobest cost_price

**Files:**
- `app/Http/Controllers/Admin/DataPlanController.php`
- `resources/views/admin/data-plans/index.blade.php`

---

### 5. TAILWIND MIGRATION FOR ALL ADMIN PAGES

**Current:** Tables use Bootstrap classes  
**Goal:** 100% Tailwind CSS

**Tasks:**
- [ ] Data plans table: Convert Bootstrap to Tailwind
  - ✅ Already uses Tailwind wrapper
  - ⚠️ Check table cell classes
  
- [ ] Cable plans table: Convert to Tailwind
  - ✅ Already uses Tailwind wrapper
  - ⚠️ Check table cell classes
  
- [ ] Electricity table: Convert to Tailwind
  - ✅ Already uses Tailwind layout
  - ⚠️ Check table cell classes
  
- [ ] Remove all Bootstrap dependencies from admin layout
- [ ] Test DataTables compatibility with Tailwind

**Files:**
- `resources/views/admin/data-plans/index.blade.php`
- `resources/views/admin/cable-plans/index.blade.php`
- `resources/views/admin/electricity/index.blade.php`
- `resources/views/layouts/admin.blade.php`

---

### 6. USER PURCHASE PAGES ALIGNMENT

**Goal:** User-facing pages match admin pricing logic

**Tasks:**
- [ ] Data purchase page (`resources/views/data/index.blade.php`)
  - ✅ Ported_number toggle exists
  - [ ] Verify pricing display matches selling_price
  - [ ] Test end-to-end purchase flow
  
- [ ] Airtime purchase page (`resources/views/airtime/index.blade.php`)
  - ✅ Ported_number toggle exists
  - [ ] Add airtime_type selector (VTU default)
  - [ ] Verify pricing calculation
  - [ ] Test end-to-end purchase flow
  
- [ ] Cable purchase page (`resources/views/cable/index.blade.php`)
  - [ ] Verify plan prices from selling_price
  - [ ] Test IUC validation
  - [ ] Test end-to-end purchase flow
  
- [ ] Electricity purchase page (`resources/views/electricity/index.blade.php`)
  - [ ] Verify pricing from selling_price
  - [ ] Test meter validation
  - [ ] Test end-to-end purchase flow

---

### 7. COMMENT OUT NON-CORE SERVICES

**Services to Hide:**
- Exam Pins
- Recharge Pins
- Data Pins
- Any other non-Uzobest services

**Tasks:**
- [ ] Update admin sidebar to hide non-core services
  - `resources/views/layouts/admin.blade.php` (or sidebar component)
  
- [ ] Update user sidebar/dashboard to hide non-core services
  - `resources/views/layouts/app.blade.php`
  
- [ ] Add config flag to enable/disable services
  - `config/services.php` add:
    ```php
    'enabled_services' => ['airtime', 'data', 'cable', 'electricity']
    ```

**Files:**
- `resources/views/components/sidebar.blade.php` (if exists)
- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/app.blade.php`
- `config/services.php`

---

### 8. REMOVE SEPARATE API CONFIGS

**Goal:** Only Uzobest configuration in admin

**Tasks:**
- [ ] Remove API config pages for non-Uzobest providers
- [ ] Keep single "API Configuration" page for Uzobest only
- [ ] Move Uzobest config to ApiLink table
- [ ] Remove ApiConfig model/table references

**Files:**
- `resources/views/admin/api-config/*.blade.php`
- `app/Http/Controllers/Admin/ApiConfigController.php`
- `app/Models/ApiConfig.php`

---

## 🧪 TESTING & VALIDATION

### 9. END-TO-END TESTING CHECKLIST

#### Airtime Purchase Test
- [ ] Test with MTN, Airtel, Glo, 9Mobile
- [ ] Test with ported number toggle ON/OFF
- [ ] Test with airtime_type: VTU (default)
- [ ] Verify correct network_id sent to Uzobest (1=MTN, 4=Airtel, 2=Glo, 3=9Mobile)
- [ ] Verify correct airtime_type sent ("VTU" not "Share and Sell")
- [ ] Confirm selling_price charged to user
- [ ] Confirm profit_margin calculated correctly
- [ ] Verify transaction saved with correct status

#### Data Purchase Test
- [ ] Test with all networks
- [ ] Test with ported number toggle
- [ ] Verify correct plan ID sent to Uzobest (uzobest_plan_id)
- [ ] Confirm selling_price charged
- [ ] Confirm profit_margin calculated
- [ ] Verify data delivered to user

#### Cable Purchase Test
- [ ] Test with DSTV, GOTV, Startimes
- [ ] Test IUC validation endpoint
- [ ] Verify correct cablename ID sent (1=DSTV, 2=GOTV, 3=Startimes)
- [ ] Verify correct cableplan ID sent (uzobest_plan_id)
- [ ] Confirm selling_price charged
- [ ] Verify subscription activated

#### Electricity Purchase Test
- [ ] Test with all DISCOs
- [ ] Test meter validation endpoint
- [ ] Verify MeterType ID (1=PREPAID, 2=POSTPAID)
- [ ] Confirm selling_price charged
- [ ] Verify token generated and delivered

---

## 📋 DETAILED IMPLEMENTATION STEPS

### PHASE 1: FIX CRITICAL AIRTIME BUG (Priority 1)

**Step 1.1:** Fix UzobestApiAdapter default airtime_type
```php
// app/Services/UzobestApiAdapter.php
private function getAirtimeType(string $type): string
{
    // FIX: Default to VTU, not empty or invalid
    if (empty($type)) {
        return 'VTU';
    }
    
    return self::AIRTIME_TYPE_MAP[$type] ?? 'VTU';
}
```

**Step 1.2:** Update AirtimeController validation
```php
// app/Http/Controllers/AirtimeController.php - purchase() method
$validator = Validator::make($request->all(), [
    'network' => 'required|string',
    'phone' => 'required|string|min:11|max:11',
    'amount' => 'required|numeric|min:50',
    'airtime_type' => 'sometimes|in:VTU,awuf4U,Share and Sell',
    'ported_number' => 'sometimes|boolean',
]);

// Set default
$airtimeType = $request->input('airtime_type', 'VTU');
```

**Step 1.3:** Test airtime purchase
- Test cases with VTU type
- Verify API request payload
- Check Uzobest response

---

### PHASE 2: APILINK CONFIGURATION (Priority 2)

**Step 2.1:** Create ApiLink seeder
```php
// database/seeders/ApiLinkSeeder.php
public function run()
{
    ApiLink::create([
        'name' => 'Uzobest Primary API',
        'type' => 'primary',
        'value' => 'https://uzobestgsm.com/api/',
        'is_active' => true,
        'priority' => 1,
        'auth_type' => 'token',
        'auth_params' => [
            'token' => env('UZOBEST_API_TOKEN', '66f2e5c39ac8640f13cd888f161385b12f7e5e92'),
            'header_name' => 'Authorization',
            'header_format' => 'Token {token}'
        ],
    ]);
}
```

**Step 2.2:** Update ExternalApiService
```php
// app/Services/ExternalApiService.php
protected function getUzobestConfig()
{
    $apiLink = ApiLink::where('type', 'primary')
        ->where('is_active', true)
        ->first();
    
    if (!$apiLink) {
        throw new Exception('Uzobest API configuration not found');
    }
    
    return [
        'base_url' => $apiLink->value,
        'token' => $apiLink->auth_params['token'] ?? '',
    ];
}
```

---

### PHASE 3: ADMIN PRICE SYNC (Priority 3)

**Step 3.1:** Create sync command for data plans
```bash
php artisan make:command SyncDataPlansCommand
```

**Step 3.2:** Implement sync logic
```php
// app/Console/Commands/SyncDataPlansCommand.php
public function handle()
{
    $response = Http::withHeaders([
        'Authorization' => 'Token ' . config('services.uzobest.token')
    ])->get('https://uzobestgsm.com/api/network/');
    
    $networks = $response->json();
    
    foreach ($networks as $network) {
        foreach ($network['plans'] as $plan) {
            $existingPlan = DataPlan::where('uzobest_plan_id', $plan['id'])->first();
            
            if ($existingPlan) {
                // Update cost, preserve selling price
                $existingPlan->update([
                    'cost_price' => $plan['price'],
                    'profit_margin' => $this->calculateMargin(
                        $existingPlan->selling_price, 
                        $plan['price']
                    ),
                ]);
            } else {
                // New plan - set 5% markup
                DataPlan::create([
                    'nId' => $this->getNetworkId($network['network']),
                    'dPlan' => $plan['name'],
                    'dAmount' => $plan['size'],
                    'cost_price' => $plan['price'],
                    'selling_price' => $plan['price'] * 1.05,
                    'profit_margin' => 5.0,
                    'uzobest_plan_id' => $plan['id'],
                ]);
            }
        }
    }
}
```

---

### PHASE 4: UI CLEANUP & TAILWIND (Priority 4)

**Step 4.1:** Remove Bootstrap classes from tables
- Replace `table`, `table-striped` with Tailwind equivalents
- Update modals to use Tailwind utility classes
- Ensure DataTables works with Tailwind styling

**Step 4.2:** Comment out non-core services
```blade
{{-- resources/views/layouts/admin.blade.php --}}
@if(in_array('airtime', config('services.enabled_services')))
    <li><a href="/admin/airtime">Airtime</a></li>
@endif

{{-- Comment out --}}
{{-- <li><a href="/admin/exam-pins">Exam Pins</a></li> --}}
{{-- <li><a href="/admin/recharge-pins">Recharge Pins</a></li> --}}
```

---

### PHASE 5: END-TO-END TESTING (Priority 5)

**Test each service thoroughly following checklist in section 9**

---

## 🎯 SUCCESS CRITERIA

- [ ] All 4 services (Airtime, Data, Cable, Electricity) work end-to-end
- [ ] Uzobest API receives correctly formatted requests
- [ ] Admin can sync prices from Uzobest (Data only)
- [ ] Admin can set selling prices for all services
- [ ] Profit margins calculate correctly
- [ ] Users charged correct selling_price
- [ ] No airtime "invalid type" errors
- [ ] All admin tables use Tailwind CSS
- [ ] DataTables look good and function properly
- [ ] Non-core services hidden from UI
- [ ] Single ApiLink configuration source
- [ ] No manual plan creation (Data plans sync only)

---

## 📝 NOTES

### Uzobest API Network IDs
- MTN = 1
- GLO = 2
- 9MOBILE = 3
- AIRTEL = 4

### Uzobest Meter Types
- PREPAID = 1
- POSTPAID = 2

### Uzobest Cable IDs (verify from docs)
- DSTV = 1
- GOTV = 2
- STARTIMES = 3

### Authentication Format
```
Authorization: Token 66f2e5c39ac8640f13cd888f161385b12f7e5e92
```

---

## 🚀 EXECUTION ORDER

1. **FIX AIRTIME BUG** (30 mins)
2. **APILINK MIGRATION** (1 hour)
3. **DATA SYNC IMPLEMENTATION** (2 hours)
4. **UI CLEANUP** (2 hours)
5. **END-TO-END TESTING** (3 hours)

**Total Estimated Time:** 8.5 hours

---

**Last Updated:** December 5, 2025  
**Status:** Ready for implementation
