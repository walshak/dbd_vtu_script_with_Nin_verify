# Uzobest API Integration Mapping

This document describes how the VTU application integrates with the Uzobest GSM API for various services.

## API Configuration

**Base URL**: `https://uzobestgsm.com/api`  
**Authentication**: Token-based via `Authorization` header  
**Format**: `Authorization: Token {your_api_key}`

Configuration in Laravel:
```php
// config/services.php
'uzobest' => [
    'url' => env('UZOBEST_API_URL', 'https://uzobestgsm.com/api'),
    'key' => env('UZOBEST_API_KEY'),
],
```

## Service Mappings

### 1. Data Plans

#### Endpoint: GET /api/network/
**Purpose**: Fetch all networks and their available data plans

**Response Structure**:
```json
{
  "MTN": {
    "SME": [
      {"id": 1, "plan": "500MB", "price": 100, "validity": "30 days"},
      {"id": 2, "plan": "1GB", "price": 200, "validity": "30 days"}
    ],
    "Gifting": [...],
    "Corporate": [...]
  },
  "AIRTEL": {...},
  "GLO": {...},
  "9MOBILE": {...}
}
```

**Network IDs** (used in purchase requests):
- MTN: `1`
- GLO: `2`
- 9MOBILE: `3`
- AIRTEL: `4`

**Data Plan Types**:
- `SME` - SME data sharing
- `Gifting` - Data gifting
- `Corporate` - Corporate data plans

#### Endpoint: POST /api/data/
**Purpose**: Purchase data bundle

**Request Body**:
```json
{
  "network": 1,
  "mobile_number": "08012345678",
  "plan": 123,
  "Ported_number": false
}
```

**Parameters**:
- `network` (int): Network ID (1=MTN, 2=GLO, 3=9MOBILE, 4=AIRTEL)
- `mobile_number` (string): Recipient phone number (format: 080XXXXXXXX)
- `plan` (int): Plan ID from network listing
- `Ported_number` (boolean): Whether the number is ported to another network

**Internal Mapping**:
- Database: `dataplans` table
- Primary Key: `dId`
- Uzobest Plan ID stored in: `planid` column
- Network mapping stored in: `dNetwork` (foreign key to `network_ids.nId`)
- Plan type stored in: `type` column (SME/Gifting/Corporate)

---

### 2. Airtime (VTU)

#### Endpoint: POST /api/topup/
**Purpose**: Purchase airtime

**Request Body**:
```json
{
  "network": 1,
  "amount": 100,
  "mobile_number": "08012345678",
  "Ported_number": false,
  "airtime_type": "VTU"
}
```

**Airtime Types**:
- `VTU` - Regular virtual top-up (standard airtime)
- `Share and Sell` - Airtime that can be resold/transferred
- `awuf4U` - Bonus airtime with extra value

**Network IDs**: Same as data (1=MTN, 2=GLO, 3=9MOBILE, 4=AIRTEL)

**Internal Mapping**:
- Database: `rechargepin` table for discount rates
- Primary Key: `aId`
- Stores discount percentages per network for user/agent/vendor types
- Does not store individual airtime transactions as plans (dynamic pricing)

---

### 3. Cable TV Subscription

#### Endpoint: GET /api/validateiuc
**Purpose**: Validate IUC/Smartcard number before purchase

**Query Parameters**:
- `smart_card_number` (string): IUC or smartcard number
- `cablename` (int): Cable provider ID

**Cable Provider IDs**:
- DSTV: `1`
- GOTV: `2`
- STARTIMES: `3`

**Response** (on success):
```json
{
  "Customer_Name": "John Doe",
  "Status": "successful"
}
```

#### Endpoint: POST /api/cablesub/
**Purpose**: Subscribe to cable TV service

**Request Body**:
```json
{
  "cablename": 1,
  "cableplan": 45,
  "smart_card_number": "1234567890"
}
```

**Parameters**:
- `cablename` (int): Cable provider ID (1=DSTV, 2=GOTV, 3=STARTIMES)
- `cableplan` (int): Plan ID (obtained from Uzobest documentation/support)
- `smart_card_number` (string): IUC or smartcard number

**Internal Mapping**:
- Database: `cableplans` table
- Primary Key: `cpId`
- Uzobest Plan ID stored in: `planid` column
- Cable provider mapping stored in: `cableprovider` (foreign key to `cable_ids.cId`)
- Provider IDs table: `cable_ids` with mapping to Uzobest IDs

**Note**: Uzobest does not provide an endpoint to list available cable plans. Plans must be entered manually based on Uzobest documentation or obtained from their support team.

---

### 4. Electricity Bill Payment

#### Endpoint: GET /api/validatemeter
**Purpose**: Validate meter number before purchase

**Query Parameters**:
- `meternumber` (string): Electricity meter number
- `disconame` (int): Disco provider ID
- `mtype` (int): Meter type (1=PREPAID, 2=POSTPAID)

**Disco Provider IDs**:
- EKEDC (Eko Electricity): `1`
- IKEDC (Ikeja Electric): `2`
- AEDC (Abuja Electricity): `3`
- KEDCO (Kano Electricity): `4`
- PHED (Port Harcourt Electricity): `5`
- JED (Jos Electricity): `6`
- IBEDC (Ibadan Electricity): `7`
- KAEDCO (Kaduna Electric): `8`
- EEDC (Enugu Electricity): `9`

**Meter Types**:
- PREPAID: `1`
- POSTPAID: `2`

**Response** (on success):
```json
{
  "Customer_Name": "John Doe",
  "Address": "123 Main Street, Lagos",
  "Status": "successful"
}
```

#### Endpoint: POST /api/billpayment/
**Purpose**: Pay electricity bill

**Request Body**:
```json
{
  "disco_name": 1,
  "amount": 5000,
  "meter_number": "12345678901",
  "MeterType": 1
}
```

**Parameters**:
- `disco_name` (int): Disco provider ID
- `amount` (number): Amount to pay
- `meter_number` (string): Meter number
- `MeterType` (int): Meter type (1=PREPAID, 2=POSTPAID)

**Internal Mapping**:
- Database: `electricity` table
- Primary Key: `eId`
- Provider name stored in: `ePlan` column
- Provider ID mapping stored in: `eProviderId` column
- Pricing: `eBuyingPrice` (cost), `ePrice` (selling price)

---

### 5. Transaction Querying

#### Query Data Transaction
**Endpoint**: GET /api/data/{transaction_id}

#### Query Airtime Transaction
**Endpoint**: GET /api/topup/{transaction_id}

#### Query Cable Transaction
**Endpoint**: GET /api/cablesub/{transaction_id}

#### Query Electricity Transaction
**Endpoint**: GET /api/billpayment/{transaction_id}

**Purpose**: Check status of a transaction

---

### 6. Account Information

#### Endpoint: GET /api/user/
**Purpose**: Get account details and balance

**Response**:
```json
{
  "username": "your_username",
  "Account_Balance": 50000.00,
  "email": "user@example.com"
}
```

---

## Laravel Service Classes

### UzobestSyncService
**Location**: `app/Services/UzobestSyncService.php`

**Methods**:
- `fetchDataPlans()` - Get all networks and plans from /api/network/
- `fetchUserDetails()` - Get account balance from /api/user/
- `validateIUC()` - Validate cable IUC number
- `validateMeter()` - Validate electricity meter number
- `parseDataPlans()` - Parse API response into structured format
- `getCachedDataPlans()` - Get cached plans (1 hour cache)

**ID Mapping Methods**:
- `getNetworkMapping()` - Returns network name to ID mapping
- `getCableProviderMapping()` - Returns cable provider to ID mapping
- `getDiscoProviderMapping()` - Returns disco provider to ID mapping
- `getMeterTypeMapping()` - Returns meter type to ID mapping
- `getAirtimeTypeMapping()` - Returns airtime type mapping

### UzobestApiAdapter
**Location**: `app/Services/UzobestApiAdapter.php`

**Purpose**: Transform between internal format and Uzobest API format

**Methods**:
- `transformDataPurchaseRequest()` - Format data purchase request
- `transformAirtimePurchaseRequest()` - Format airtime purchase request
- `transformCablePurchaseRequest()` - Format cable purchase request
- `transformElectricityPurchaseRequest()` - Format electricity purchase request
- `parseResponse()` - Parse Uzobest API response into standard format

---

## Admin Sync Functionality

### Data Plans Sync
**Route**: `POST /admin/data-plans/sync-from-uzobest`  
**Controller**: `App\Http\Controllers\Admin\DataPlanController@syncFromUzobest`

**Process**:
1. Calls `/api/network/` to fetch all networks and plans
2. Parses response by network and plan type (SME/Gifting/Corporate)
3. Creates or updates plans in `dataplans` table
4. Matches by `planid` (Uzobest plan ID)
5. Applies pricing markup: User (+5%), Agent (+3%), Vendor (+2%)

### Cable IUC Validation
**Route**: `POST /admin/cable-plans/validate-iuc`  
**Controller**: `App\Http\Controllers\Admin\CablePlanController@validateIUC`

**Process**:
1. Gets cable provider ID from `cable_ids` table
2. Maps to Uzobest provider ID
3. Calls `/api/validateiuc` endpoint
4. Returns customer name and validation status

### Electricity Meter Validation
**Route**: `POST /admin/electricity/validate-meter`  
**Controller**: `App\Http\Controllers\Admin\ElectricityController@validateMeter`

**Process**:
1. Gets disco provider ID from `electricity` table
2. Maps to Uzobest disco ID
3. Converts meter type to ID (PREPAID=1, POSTPAID=2)
4. Calls `/api/validatemeter` endpoint
5. Returns customer name, address, and validation status

### Electricity Providers Sync
**Route**: `POST /admin/electricity/sync-from-uzobest`  
**Controller**: `App\Http\Controllers\Admin\ElectricityController@syncFromUzobest`

**Process**:
1. Uses predefined disco provider mapping
2. Creates or updates providers in `electricity` table
3. Sets default pricing (₦45 buying, ₦50 selling)

---

## User-Facing Controllers

### DataController
**Location**: `app/Http/Controllers/DataController.php`
- Uses `DataService` to purchase data
- Calls Uzobest `/api/data/` endpoint via service layer

### AirtimeController
**Location**: `app/Http/Controllers/AirtimeController.php`
- Uses `AirtimeService` to purchase airtime
- Supports VTU and "Share and Sell" types
- Calls Uzobest `/api/topup/` endpoint

### CableTVController
**Location**: `app/Http/Controllers/CableTVController.php`
- Uses `CableTVService` for subscriptions
- Calls Uzobest `/api/cablesub/` endpoint

### ElectricityController
**Location**: `app/Http/Controllers/ElectricityController.php`
- Uses `ElectricityService` for bill payments
- Calls Uzobest `/api/billpayment/` endpoint

---

## Database Schema Mapping

### dataplans
| Column | Uzobest Mapping | Description |
|--------|----------------|-------------|
| dId | - | Primary key (auto-increment) |
| dNetwork | network ID | Foreign key to network_ids.nId |
| name | plan name | E.g., "1GB SME Data" |
| type | plan type | SME, Gifting, or Corporate |
| planid | plan ID | Uzobest's plan ID |
| day | validity | Duration in days |
| price | base price | Buying price from Uzobest |
| userprice | - | User selling price (+5% markup) |
| agentprice | - | Agent selling price (+3% markup) |
| vendorprice | - | Vendor selling price (+2% markup) |
| datanetwork | network name | Lowercase network name |

### network_ids
| Column | Uzobest Mapping | Description |
|--------|----------------|-------------|
| nId | - | Primary key (auto-increment) |
| network | network name | MTN, AIRTEL, GLO, 9MOBILE |
| nStatus | - | 1=active, 0=inactive |

### cableplans
| Column | Uzobest Mapping | Description |
|--------|----------------|-------------|
| cpId | - | Primary key (auto-increment) |
| name | plan name | E.g., "DStv Premium" |
| planid | plan ID | Uzobest's cable plan ID |
| cableprovider | provider ID | Foreign key to cable_ids.cId |
| day | duration | Subscription duration in days |
| price | base price | Buying price |
| userprice | - | User selling price |
| agentprice | - | Agent selling price |
| vendorprice | - | Vendor selling price |

### cable_ids
| Column | Uzobest Mapping | Description |
|--------|----------------|-------------|
| cId | - | Primary key (auto-increment) |
| provider | provider name | DSTV, GOTV, STARTIMES |
| cStatus | - | 1=active, 0=inactive |

### electricity
| Column | Uzobest Mapping | Description |
|--------|----------------|-------------|
| eId | - | Primary key (auto-increment) |
| ePlan | disco name | EKEDC, IKEDC, AEDC, etc. |
| eProviderId | - | Internal provider identifier |
| eBuyingPrice | - | Cost per naira |
| ePrice | - | Selling price per naira |
| eStatus | - | 1=active, 0=inactive |

### rechargepin
| Column | Uzobest Mapping | Description |
|--------|----------------|-------------|
| aId | - | Primary key (auto-increment) |
| aNetwork | network ID | Foreign key to network_ids.nId |
| aUserDiscount | - | User discount percentage |
| aAgentDiscount | - | Agent discount percentage |
| aVendorDiscount | - | Vendor discount percentage |

---

## Error Handling

### Common Error Responses

**Insufficient Balance**:
```json
{
  "detail": "Insufficient balance"
}
```

**Invalid Parameters**:
```json
{
  "error": "Invalid network ID"
}
```

**API Errors**:
- HTTP 400: Bad Request (validation error)
- HTTP 401: Unauthorized (invalid API key)
- HTTP 500: Server Error (Uzobest API issue)

### Internal Error Handling
All service methods return standardized responses:
```php
[
    'success' => true/false,
    'message' => 'Description',
    'data' => [...], // On success
    'error_code' => 'ERROR_TYPE' // On failure
]
```

---

## Testing Endpoints

Use the included Postman collection for testing:
**File**: `UZOBESTGSM API DOCUMENTATION.postman_collection.json`

Update the API key in the collection's Authorization settings before testing.

---

## Caching Strategy

- **Data Plans**: Cached for 1 hour (`uzobest_data_plans` key)
- **Network IDs**: Static mapping (no cache needed)
- **Provider IDs**: Static mapping (no cache needed)

Clear cache when syncing:
```php
Cache::forget('uzobest_data_plans');
```

---

## Pricing Strategy

### Data Plans
- Base: Uzobest price
- User: Base + 5%
- Agent: Base + 3%
- Vendor: Base + 2%

### Airtime
- Discount-based (percentage off face value)
- User: Highest discount
- Agent: Medium discount
- Vendor: Lowest discount

### Cable TV
- Fixed pricing per plan
- Different prices for user/agent/vendor tiers

### Electricity
- Cost per naira (₦45 buying, ₦50 selling by default)
- Can be adjusted per disco provider

---

## Notes

1. **Cable Plans**: Uzobest does not provide an API endpoint to list cable plans. Plans must be manually entered based on Uzobest documentation or obtained from their support team.

2. **Validation**: Always validate IUC numbers (cable) and meter numbers (electricity) before processing transactions to prevent failed transactions.

3. **Network IDs**: The network ID mapping is hardcoded based on Uzobest's documentation. If Uzobest changes these IDs, update the mapping in `UzobestSyncService`.

4. **Transaction History**: Use Uzobest's query endpoints to verify transaction status when needed.

5. **Webhooks**: Uzobest may provide webhook functionality for transaction status updates. This is not documented in the current implementation but should be considered for future enhancements.

---

## Support

For Uzobest API issues:
- Website: https://uzobestgsm.com
- API Documentation: Contact Uzobest support
- Postman Collection: Included in project

For application issues:
- Check logs: `storage/logs/laravel.log`
- Database errors: Check seeder/migration files
- Service errors: Check relevant service classes in `app/Services/`
