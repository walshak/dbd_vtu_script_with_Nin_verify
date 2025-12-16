# UZOBEST API INTEGRATION - COMPREHENSIVE TODO

## 🎯 OVERVIEW
Standardize all VTU services (Airtime, Data, Cable, Electricity) to:
- Fetch prices from Uzobest API
- Admin configures selling prices (cost price from Uzobest + markup)
- Remove agent/vendor tiers (focus on user pricing only)
- Use apilinks table for API configuration
- Unified Tailwind UI across all service pages
- Ensure purchase endpoints match Uzobest API structure

---

## 📋 PHASE 1: UZOBEST API COMPATIBILITY FIXES

### ✅ **Task 1.1: Fix Airtime Purchase API**
**Priority: CRITICAL**

**Issues:**
- Current: Sends `type` field with value "VTU"
- Uzobest expects: `airtime_type` with values: "VTU" | "awuf4U" | "Share and Sell"
- Missing `Ported_number` boolean field

**Files to Update:**
- `app/Http/Controllers/AirtimeController.php` - Update purchase method
- `app/Services/AirtimeService.php` - Fix API call parameters
- `resources/views/airtime/index.blade.php` - Change form field from `type` to `airtime_type`
- `public/js/vtu-services.js` - Update AJAX parameters

**Uzobest Endpoint:**
```
POST https://uzobestgsm.com/api/topup/
Body: {
    "network": network_id (1=MTN, 2=GLO, 3=9Mobile, 4=Airtel),
    "amount": amount,
    "mobile_number": phone,
    "Ported_number": true/false,
    "airtime_type": "VTU"
}
```

### ✅ **Task 1.2: Fix Data Purchase API**
**Priority: HIGH**

**Issues:**
- Verify network parameter is network ID (not name)
- Add `Ported_number` field
- Ensure plan parameter is Uzobest plan ID

**Files to Update:**
- `app/Http/Controllers/DataController.php`
- `app/Services/DataService.php`
- `resources/views/data/index.blade.php`

**Uzobest Endpoint:**
```
POST https://uzobestgsm.com/api/data/
Body: {
    "network": network_id,
    "mobile_number": "09037346247",
    "plan": plan_id,
    "Ported_number": true/false
}
```

### ✅ **Task 1.3: Fix Cable TV Purchase API**
**Priority: HIGH**

**Issues:**
- Ensure using Uzobest cable name ID and plan ID
- Implement IUC validation

**Files to Update:**
- `app/Http/Controllers/CableTVController.php`
- `app/Services/CableTVService.php`
- `resources/views/cable/index.blade.php`

**Uzobest Endpoints:**
```
Validate IUC: GET /api/validateiuc?smart_card_number=XXX&cablename=id
Purchase: POST /api/cablesub/
Body: {
    "cablename": cable_id,
    "cableplan": plan_id,
    "smart_card_number": iuc_number
}
```

### ✅ **Task 1.4: Fix Electricity Purchase API**
**Priority: HIGH**

**Issues:**
- Ensure using correct disco name ID
- Add MeterType field (1=PREPAID, 2=POSTPAID)
- Implement meter validation

**Files to Update:**
- `app/Http/Controllers/ElectricityController.php`
- `app/Services/ElectricityService.php`
- `resources/views/electricity/index.blade.php`

**Uzobest Endpoints:**
```
Validate Meter: GET /api/validatemeter?meternumber=XXX&disconame=id&mtype=1
Purchase: POST /api/billpayment/
Body: {
    "disco_name": disco_id,
    "amount": amount,
    "meter_number": meter_number,
    "MeterType": 1 or 2
}
```

---

## 📋 PHASE 2: ADMIN PRICE MANAGEMENT SYSTEM

### ✅ **Task 2.1: Create Unified Admin Pattern**
**Priority: CRITICAL**

**Requirements:**
- Each service admin page should have:
  1. **Sync Button** - Fetch latest prices from Uzobest
  2. **Pricing Table** - Show: Service Name, Uzobest Cost Price, Our Selling Price, Profit Margin
  3. **Edit Modal** - Admin can update selling price only (cost price is read-only from Uzobest)
  4. **No Manual Creation** - All services must come from Uzobest sync

**Implementation:**
1. Remove all "Add Provider" / "Add Plan" buttons
2. Add "Sync from Uzobest" button
3. Table columns:
   - Service/Plan Name
   - Cost Price (from Uzobest) - Read-only, highlighted
   - Selling Price (editable by admin)
   - Profit (calculated: selling - cost)
   - Status (Active/Inactive toggle)
   - Actions (Edit selling price only)

### ✅ **Task 2.2: Airtime Admin Page**
**File:** `resources/views/admin/airtime/index.blade.php`

**Changes:**
- Remove agent/vendor discount fields
- Change to: Cost Price (from Uzobest) vs User Selling Price
- Show markup percentage
- Remove manual add functionality

### ✅ **Task 2.3: Data Admin Page**
**File:** `resources/views/admin/data-plans/index.blade.php`

**Current Status:** Already has sync, but needs pricing model update
**Changes:**
- Ensure cost_price column exists (from Uzobest)
- Selling_price column for admin configuration
- Remove agent/vendor pricing
- Update sync to populate cost_price from Uzobest

### ✅ **Task 2.4: Cable Admin Page**
**File:** `resources/views/admin/cable-plans/index.blade.php`

**Changes:**
- Same pattern as data plans
- Sync cable packages from Uzobest
- Admin sets selling price per package

### ✅ **Task 2.5: Electricity Admin Page**
**File:** `resources/views/admin/electricity/index.blade.php`

**Changes:**
- Same pattern as other services
- Sync disco providers from Uzobest
- Show cost per unit, selling price per unit

---

## 📋 PHASE 3: DATABASE SCHEMA UPDATES

### ✅ **Task 3.1: Add Cost/Selling Price Columns**

**Migrations Needed:**

```php
// Data Plans
ALTER TABLE data_plans ADD COLUMN cost_price DECIMAL(10,2) AFTER plan_price;
ALTER TABLE data_plans ADD COLUMN selling_price DECIMAL(10,2) AFTER cost_price;
ALTER TABLE data_plans ADD COLUMN uzobest_plan_id INT AFTER network;

// Cable TV
ALTER TABLE cabletv ADD COLUMN cost_price DECIMAL(10,2) AFTER price;
ALTER TABLE cabletv ADD COLUMN selling_price DECIMAL(10,2) AFTER cost_price;
ALTER TABLE cabletv ADD COLUMN uzobest_cable_id INT AFTER provider;

// Electricity
ALTER TABLE electricity ADD COLUMN cost_price DECIMAL(10,2) AFTER eBuyingPrice;
ALTER TABLE electricity ADD COLUMN selling_price DECIMAL(10,2) AFTER cost_price;
ALTER TABLE electricity ADD COLUMN uzobest_disco_id INT AFTER ePlan;

// Airtime Pricing
ALTER TABLE airtimepinprice ADD COLUMN cost_percentage DECIMAL(5,2) AFTER aNetwork;
ALTER TABLE airtimepinprice MODIFY aUserDiscount TO selling_percentage DECIMAL(5,2);
```

### ✅ **Task 3.2: Remove Agent/Vendor Columns**

**Tables to Update:**
- `airtimepinprice` - Remove aAgentDiscount, aVendorDiscount
- `rechargepin` - Remove agent/vendor columns
- Any other tables with user type pricing tiers

---

## 📋 PHASE 4: SERVICES & CONTROLLERS UPDATE

### ✅ **Task 4.1: Update UzobestSyncService**
**File:** `app/Services/UzobestSyncService.php`

**Add Methods:**
- `syncDataPlans()` - Fetch from /api/network/
- `syncCablePlans()` - Fetch cable packages
- `syncElectricityProviders()` - Fetch electricity discos
- `getAirtimePricing()` - Get airtime cost structure

**Store:**
- Uzobest ID (for API calls)
- Cost price (from Uzobest)
- Default selling price (cost + default markup %)

### ✅ **Task 4.2: Update Purchase Controllers**

**Files:**
- `app/Http/Controllers/AirtimeController.php`
- `app/Http/Controllers/DataController.php`
- `app/Http/Controllers/CableTVController.php`
- `app/Http/Controllers/ElectricityController.php`

**Changes:**
- Use selling_price from database (not calculated discount)
- Send correct parameters to Uzobest API
- Validate using Uzobest validation endpoints before purchase

### ✅ **Task 4.3: Update Admin Controllers**

**Files:**
- `app/Http/Controllers/Admin/AirtimePricingController.php`
- `app/Http/Controllers/Admin/DataPlanController.php`
- `app/Http/Controllers/Admin/CablePlanController.php`
- `app/Http/Controllers/Admin/ElectricityController.php`

**Add Methods:**
- `syncFromUzobest()` - Sync latest prices
- `updateSellingPrice()` - Admin updates selling price
- Remove `store()` methods (no manual creation)

---

## 📋 PHASE 5: UI/UX STANDARDIZATION

### ✅ **Task 5.1: Convert DataTables to Tailwind**

**Files:**
- All admin service pages using DataTables
- Replace with custom Tailwind tables
- Add pagination using Laravel pagination
- Remove Bootstrap DataTables CSS/JS

### ✅ **Task 5.2: Unified Gradient Headers**

**Colors by Service:**
- Airtime: `bg-gradient-to-r from-green-500 to-emerald-600`
- Data: `bg-gradient-to-r from-blue-500 to-indigo-600`
- Cable: `bg-gradient-to-r from-purple-500 to-pink-600`
- Electricity: `bg-gradient-to-r from-yellow-500 to-orange-600`

### ✅ **Task 5.3: Unified Modal Pattern**

**Structure:**
```html
<div id="editPriceModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <!-- Header -->
            <!-- Form: Show cost price (read-only) + selling price (editable) -->
            <!-- Profit calculation display -->
            <!-- Actions -->
        </div>
    </div>
</div>
```

---

## 📋 PHASE 6: CONFIGURATION & CLEANUP

### ✅ **Task 6.1: Use ApiLink Table**

**File:** `database/migrations/XXXX_update_apilinks_for_uzobest.php`

**Add Records:**
```sql
INSERT INTO apilinks (name, type, value, is_active) VALUES
('Uzobest API URL', 'uzobest_base', 'https://uzobestgsm.com/api', 1),
('Uzobest API Key', 'uzobest_key', 'Token YOUR_KEY_HERE', 1),
('Data Plans Endpoint', 'uzobest_data', '/network/', 1),
('Airtime Endpoint', 'uzobest_airtime', '/topup/', 1),
('Cable Endpoint', 'uzobest_cable', '/cablesub/', 1),
('Electricity Endpoint', 'uzobest_electricity', '/billpayment/', 1);
```

### ✅ **Task 6.2: Remove API Config Pages**

**Files to Remove/Comment:**
- `resources/views/admin/api-configuration/*` (except index for Uzobest settings)
- Keep only one unified Uzobest API config page

### ✅ **Task 6.3: Update Sidebar**

**File:** `resources/views/components/admin-sidebar.blade.php`

**Comment Out:**
- Exam Pins
- Recharge Pins
- Any other non-core services

**Keep Only:**
- Dashboard
- Airtime Pricing
- Data Plans
- Cable Plans
- Electricity
- Transactions
- Users
- Settings

### ✅ **Task 6.4: Update Dashboard**

**File:** `resources/views/admin/dashboard.blade.php`

**Show Only:**
- Airtime revenue
- Data revenue
- Cable revenue
- Electricity revenue
- Total transactions
- Active users

---

## 📋 PHASE 7: TESTING CHECKLIST

### ✅ **Test 7.1: Admin Sync**
- [ ] Airtime: Click sync, verify prices update from Uzobest
- [ ] Data: Click sync, verify all plans populate with Uzobest IDs and prices
- [ ] Cable: Click sync, verify all packages populate
- [ ] Electricity: Click sync, verify all DISCOs populate

### ✅ **Test 7.2: Admin Price Configuration**
- [ ] Edit selling price for each service
- [ ] Verify profit calculation is correct
- [ ] Save and confirm price persists
- [ ] Verify users see updated prices

### ✅ **Test 7.3: User Purchases**
- [ ] Airtime: Purchase ₦100 MTN airtime, verify correct parameters sent to Uzobest
- [ ] Data: Purchase data plan, verify correct network ID and plan ID sent
- [ ] Cable: Validate IUC, purchase subscription
- [ ] Electricity: Validate meter, purchase electricity

### ✅ **Test 7.4: Transaction Flow**
- [ ] Verify correct amount charged (selling price)
- [ ] Verify transaction recorded
- [ ] Verify Uzobest API response handled correctly
- [ ] Verify user receives service

---

## 🚀 IMPLEMENTATION ORDER

1. **START HERE:** Fix airtime `type` → `airtime_type` parameter (CRITICAL BUG)
2. Update database schema (add cost_price, selling_price columns)
3. Update UzobestSyncService for all services
4. Update admin pages (one service at a time)
5. Update user purchase pages
6. Test each service end-to-end
7. UI/UX cleanup and Tailwind conversion
8. Remove unused code and routes
9. Final testing

---

## 📊 SUCCESS CRITERIA

- ✅ All services sync prices from Uzobest API
- ✅ Admins can only edit selling prices (not create services manually)
- ✅ Users see correct selling prices
- ✅ Profit margins calculated accurately
- ✅ All purchases work end-to-end
- ✅ Unified Tailwind UI across all pages
- ✅ No agent/vendor pricing complexity
- ✅ Clean, maintainable codebase focused on 4 core services
