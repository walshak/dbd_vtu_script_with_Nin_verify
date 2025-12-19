# Electricity Implementation Plan

## API Documentation Summary (from Uzobest)

### 1. Validate Meter Endpoint
- **URL**: `GET https://uzobestgsm.com/api/validatemeter`
- **Parameters**:
  - `meternumber`: The meter number to validate
  - `disconame`: DISCO ID (lowercase: ekedc, ikedc, aedc, kedco, phed, jed, ibedc, kaedco, eedc)
  - `mtype`: Meter type (PREPAID: 1, POSTPAID: 2)
- **Authentication**: Token-based (Header: `Authorization: Token {token}`)

### 2. Bill Payment Endpoint
- **URL**: `POST https://uzobestgsm.com/api/billpayment/`
- **Body** (JSON):
  ```json
  {
    "disco_name": "ekedc",
    "amount": 5000,
    "meter_number": "12345678901",
    "MeterType": 1
  }
  ```
- **Authentication**: Token-based

### 3. Query Transaction Endpoint
- **URL**: `GET https://uzobestgsm.com/api/billpayment/{transaction_id}`
- **Authentication**: Token-based

## Database Structure

### Electricity Providers Table (`electricity`)
```sql
eId (PK) - Provider ID
ePlan - Display name (e.g., "EKEDC", "IKEDC")
eProviderId - Lowercase DISCO code (e.g., "ekedc", "ikedc") - Used in API calls
uzobest_disco_id - Numeric mapping (1-9) - Internal reference
eBuyingPrice - Cost from Uzobest (per unit charge)
ePrice - Selling price (per unit charge)
cost_price - Same as eBuyingPrice
selling_price - Same as ePrice
profit_margin - Calculated difference
eStatus - Active (1) or Inactive (0)
```

### DISCO Mapping
| DISCO Code | eProviderId | uzobest_disco_id | ePlan |
|------------|-------------|------------------|-------|
| EKEDC | ekedc | 1 | EKEDC |
| IKEDC | ikedc | 2 | IKEDC |
| AEDC | aedc | 3 | AEDC |
| KEDCO | kedco | 4 | KEDCO |
| PHED | phed | 5 | PHED |
| JED | jed | 6 | JED |
| IBEDC | ibedc | 7 | IBEDC |
| KAEDCO | kaedco | 8 | KAEDCO |
| EEDC | eedc | 9 | EEDC |

## Implementation Checklist

### ✅ Completed Tasks

1. **Database Setup**
   - [x] Providers table populated with 9 DISCOs
   - [x] eProviderId field populated with lowercase codes
   - [x] uzobest_disco_id field populated with numeric IDs
   - [x] cost_price, selling_price, profit_margin columns exist

2. **Model Configuration**
   - [x] ElectricityProvider model has correct primary key (eId)
   - [x] getRouteKeyName() returns 'eId' for route model binding
   - [x] eProviderId added to fillable array
   - [x] scopeActive() method for filtering active providers

3. **Admin Controller**
   - [x] performAutoSync() populates all DISCO providers on page load
   - [x] update() method uses route model binding
   - [x] Uses UzobestSyncService for DISCO mapping

4. **Admin View**
   - [x] No orphaned HTML after @endpush
   - [x] Edit provider modal configured

5. **User Purchase Page**
   - [x] ElectricityController fetches active providers
   - [x] No duplicate script loading (vtu-services.js checked)

### 🔄 Verification Needed

1. **Test Admin Functionality**
   - [ ] Load http://localhost:8000/admin/electricity
   - [ ] Verify all 9 DISCOs appear in table
   - [ ] Click "Edit" on a provider
   - [ ] Modal should show correct provider details
   - [ ] Update selling price
   - [ ] Verify update saves correctly

2. **Test User Purchase Page**
   - [ ] Load http://127.0.0.1:8000/electricity
   - [ ] Verify all 9 active DISCOs display
   - [ ] Select a DISCO
   - [ ] Enter meter number
   - [ ] Select meter type (Prepaid/Postpaid)
   - [ ] Click "Validate Meter"
   - [ ] Verify validation calls correct API endpoint
   - [ ] Enter amount
   - [ ] Submit purchase
   - [ ] Verify transaction created

3. **API Integration Compliance**
   - [ ] ElectricityService uses lowercase disco codes in API calls
   - [ ] Validate meter endpoint called with correct parameters
   - [ ] Bill payment endpoint receives correct JSON body
   - [ ] Meter type mapping: PREPAID=1, POSTPAID=2
   - [ ] Authorization header includes correct token

### 📋 Implementation Details

#### Admin Page Workflow
1. Page loads → performAutoSync() runs
2. Syncs 9 DISCO providers from getDiscoProviderMapping()
3. Displays providers in DataTable
4. Edit button opens modal with provider details
5. Modal uses route: `/admin/electricity/{provider}` where {provider} = eId
6. Update uses route model binding: `update(Request $request, ElectricityProvider $provider)`

#### User Purchase Workflow
1. Page loads → ElectricityProvider::active()->get()
2. User selects DISCO → stores eProviderId (lowercase)
3. User enters meter number → validates using lowercase disco code
4. Validation: GET `/api/validatemeter?meternumber={num}&disconame={eProviderId}&mtype={type}`
5. User enters amount → calculates total based on selling_price
6. Purchase: POST `/api/billpayment/` with `{disco_name: eProviderId, amount, meter_number, MeterType}`
7. Transaction saved to database

### 🎯 Key Differences from Data Plans

| Aspect | Data Plans | Electricity |
|--------|-----------|-------------|
| Plans | Multiple plans per network | Single provider per DISCO |
| Selection | User picks specific plan | User picks DISCO + enters amount |
| Validation | Phone number validation | Meter number validation |
| Pricing | Fixed plan prices | Per-unit charge × amount |
| API Param | plan_id | disco_name (lowercase) |
| Sync | Fetch plans from API | Static DISCO list |

### 🔍 Testing Scenarios

1. **Happy Path**
   - Select EKEDC
   - Enter valid prepaid meter: 12345678901
   - Validate meter → Success
   - Enter ₦5000
   - Purchase → Success
   - Verify transaction in database

2. **Validation Failure**
   - Select IKEDC
   - Enter invalid meter: 999
   - Validate → Error message displayed
   - Purchase button remains disabled

3. **Price Markup**
   - Admin sets eBuyingPrice = 45, ePrice = 50
   - User buys ₦1000 worth
   - System charges user: 1000 × (50/45) = ₦1111.11
   - Profit = ₦111.11

4. **Inactive Provider**
   - Admin sets PHED to inactive (eStatus = 0)
   - User page should not display PHED
   - Admin page shows all providers with status toggle

### 🚨 Critical Points

1. **Always use eProviderId (lowercase) in API calls**, never ePlan (uppercase)
2. **MeterType parameter**: 1 = PREPAID, 2 = POSTPAID (case-sensitive in API)
3. **Validation is required** before purchase (unlike data plans where phone validation is optional)
4. **Amount handling**: User enters amount, system applies markup based on selling_price
5. **Error handling**: Uzobest API may return validation errors, handle gracefully

### 📝 Code References

- **Model**: `app/Models/ElectricityProvider.php`
- **Admin Controller**: `app/Http/Controllers/Admin/ElectricityController.php`
- **User Controller**: `app/Http/Controllers/ElectricityController.php`
- **Service**: `app/Services/ElectricityService.php`
- **Sync Service**: `app/Services/UzobestSyncService.php`
- **Admin View**: `resources/views/admin/electricity/index.blade.php`
- **User View**: `resources/views/electricity/index.blade.php`

### ✅ Compliance Status

**Admin Page**: ✅ Ready
- Auto-syncs providers from UzobestSyncService
- Route model binding implemented
- No orphaned HTML
- Edit modal configured

**User Page**: 🔄 Needs Verification
- Fetches active providers correctly
- Need to verify meter validation endpoint
- Need to verify purchase endpoint parameters
- Need to check pricing calculation logic

**API Integration**: 🔄 Needs Verification
- Check ElectricityService uses correct parameter names
- Verify disco_name sent as lowercase
- Verify MeterType sent as integer (1 or 2)
- Confirm Authorization header format

### 🎬 Next Steps

1. **Test admin edit modal** - Verify route model binding works
2. **Check ElectricityService** - Ensure API calls match documentation
3. **Test meter validation** - Verify correct parameters sent to Uzobest
4. **Test purchase flow** - End-to-end transaction
5. **Verify error handling** - Invalid meter, insufficient balance, API errors
