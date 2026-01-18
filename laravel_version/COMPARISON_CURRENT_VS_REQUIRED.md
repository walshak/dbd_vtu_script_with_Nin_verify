# Monnify Implementation: Current vs Required

## 📊 Gap Analysis Comparison

### What Monnify Requires ✅

```json
{
  "accountReference": "abc123",
  "accountName": "John Doe",
  "currencyCode": "NGN",
  "contractCode": "8389328412",
  "customerEmail": "john@example.com",
  "bvn": "21212121212",              // ⚠️ REQUIRED - Must be real
  "customerName": "John Doe",
  "getAllAvailableBanks": true
}
```

**Critical Note:** BVN or NIN **MUST** be supplied from actual customer data.

---

## 🔴 What We Currently Have (Laravel)

### File: `app/Services/MonnifyService.php`

```php
$payload = [
    'accountReference' => $reference,     // ✅ OK
    'accountName' => $fullName,           // ✅ OK
    'currencyCode' => 'NGN',              // ✅ OK
    'contractCode' => $this->contractCode,// ✅ OK
    'customerEmail' => $user->email,      // ✅ OK
    'customerName' => $fullName,          // ✅ OK
    'getAllAvailableBanks' => false,      // ✅ OK
    'preferredBanks' => ['035', '120']    // ✅ OK
];
// ❌ MISSING: No 'bvn' or 'nin' field!
```

**Problem:** Completely missing required BVN/NIN field.

---

## 🔴 What We Currently Have (Classic PHP)

### Files: `core/Models/Account.php`, `mobile/core/Models/Account.php`

```php
CURLOPT_POSTFIELDS =>
'{
    "accountReference": "' . $ref . '",        // ✅ OK
    "accountName": "' . $fullname . '",        // ✅ OK
    "currencyCode": "NGN",                     // ✅ OK
    "contractCode": "' . $monnifyContract . '",// ✅ OK
    "customerEmail": "' . $email . '",         // ✅ OK
    "bvn": "22433145825",                      // ❌ HARDCODED FAKE!
    "customerName": "' . $fullname . '",       // ✅ OK
    "getAllAvailableBanks": false,             // ✅ OK
    "preferredBanks": ["035"]                  // ✅ OK
}'
```

**Problems:**
- Uses hardcoded fake BVN "22433145825"
- Same BVN for ALL users
- Never uses database nin/bvn columns

---

## ✅ What We Should Have

### Laravel (Fixed):

```php
$payload = [
    'accountReference' => $reference,
    'accountName' => $fullName,
    'currencyCode' => 'NGN',
    'contractCode' => $this->contractCode,
    'customerEmail' => $user->email,
    'customerName' => $fullName,
    'getAllAvailableBanks' => false,
    'preferredBanks' => ['035', '120', '232']
];

// ✅ ADD THIS: Use real BVN or NIN from database
if (!empty($user->bvn)) {
    $payload['bvn'] = $user->bvn;
} elseif (!empty($user->nin)) {
    $payload['nin'] = $user->nin;
} else {
    // Don't create account without KYC
    return ['success' => false, 'message' => 'KYC required'];
}
```

### Classic PHP (Fixed):

```php
// ✅ ADD THIS: Get real BVN/NIN from database
$dbh = self::connect();
$sql = "SELECT nin, bvn FROM subscribers WHERE sId = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$userData = $query->fetch(PDO::FETCH_OBJ);

if (empty($userData->bvn) && empty($userData->nin)) {
    error_log("Cannot create account - No KYC data");
    return false;
}

// Build payload with real BVN or NIN
$payload = [
    'accountReference' => $ref,
    'accountName' => $fullname,
    'currencyCode' => 'NGN',
    'contractCode' => $monnifyContract,
    'customerEmail' => $email,
    'customerName' => $fullname,
    'getAllAvailableBanks' => false,
    'preferredBanks' => ['035']
];

// ✅ Use real BVN or NIN
if (!empty($userData->bvn)) {
    $payload['bvn'] = $userData->bvn;
} elseif (!empty($userData->nin)) {
    $payload['nin'] = $userData->nin;
}

CURLOPT_POSTFIELDS => json_encode($payload),
```

---

## 🔄 Complete User Flow Comparison

### Current Flow (Broken) ❌

```
User Registers
     ↓
Fund Wallet Page
     ↓
Click "Generate Account"
     ↓
❌ Laravel: Missing BVN/NIN (might fail)
❌ Classic: Uses fake BVN (compliance issue)
     ↓
Virtual Account Created (incorrectly)
```

### Fixed Flow (Compliant) ✅

```
User Registers
     ↓
Prompted for KYC
     ↓
Enters NIN or BVN
     ↓
✅ System verifies via Monnify VAS API
     ↓
✅ Saves verified data to database
     ↓
Fund Wallet Page
     ↓
Click "Generate Account"
     ↓
✅ Uses real BVN/NIN from database
     ↓
Virtual Account Created (correctly)
```

---

## 📊 Database Schema Comparison

### Current (Classic PHP):
```sql
CREATE TABLE subscribers (
    sId INT PRIMARY KEY,
    sFname VARCHAR(50),
    sLname VARCHAR(50),
    sEmail VARCHAR(100),
    sPhone VARCHAR(20),
    nin VARCHAR(11) DEFAULT NULL,     -- ⚠️ Exists but unused
    bvn VARCHAR(11) DEFAULT NULL,     -- ⚠️ Exists but unused
    ...
);
```

### Current (Laravel):
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100),
    phone VARCHAR(20),
    -- ❌ nin column MISSING
    -- ❌ bvn column MISSING
    ...
);
```

### Required (Both):
```sql
-- Add these columns
nin VARCHAR(11) NULL,
bvn VARCHAR(11) NULL,
date_of_birth DATE NULL,
kyc_status ENUM('pending','verified','rejected') DEFAULT 'pending',
kyc_verified_at TIMESTAMP NULL,
kyc_method ENUM('nin','bvn','none') DEFAULT 'none',

-- Add indexes for performance
INDEX idx_nin (nin),
INDEX idx_bvn (bvn),
INDEX idx_kyc_status (kyc_status)
```

---

## 🎨 UI Flow Comparison

### Current Laravel UI:
```
Fund Wallet Page
├── Tab: Bank Transfer
│   ├── [Generate Account Button]  ❌ No KYC check
│   └── Virtual Account Cards
├── Tab: Card Payment
└── Tab: Manual Transfer
```

### Fixed Laravel UI:
```
Fund Wallet Page
├── Tab: Bank Transfer
│   ├── IF kyc_status = 'verified':
│   │   ├── [Generate Account Button]
│   │   └── Virtual Account Cards
│   └── ELSE:
│       └── [Complete KYC Button] ✅ Redirects to KYC page
├── Tab: Card Payment
└── Tab: Manual Transfer

KYC Verification Page (NEW)
├── Tab: NIN Verification
│   └── Input: 11-digit NIN
└── Tab: BVN Verification
    ├── Input: 11-digit BVN
    └── Input: Date of Birth
```

### Current Classic PHP UI:
```
Fund Wallet Page
└── Shows virtual accounts (with fake BVN)

Authentication Page (exists but broken)
└── NIN/BVN form
    └── ❌ No backend integration
    └── ❌ Not connected to account creation
```

### Fixed Classic PHP UI:
```
Fund Wallet Page
├── IF has nin OR bvn:
│   └── Show virtual accounts
└── ELSE:
    └── [Complete KYC Button] → authentication.php

Authentication Page (fixed)
└── NIN/BVN form
    └── ✅ Backend: submit-kyc.php
    └── ✅ Saves to database
    └── ✅ Redirects to fund-wallet
```

---

## 🔍 Code Location Quick Reference

### Laravel Version Files:

| File | Current Status | Action Required |
|------|---------------|-----------------|
| `app/Services/MonnifyService.php` | ❌ Missing BVN/NIN | Add to payload |
| `app/Services/KycService.php` | ❌ Doesn't exist | Create new |
| `app/Http/Controllers/KycController.php` | ❌ Doesn't exist | Create new |
| `database/migrations/xxx_add_kyc.php` | ❌ Doesn't exist | Create new |
| `resources/views/kyc-verification.blade.php` | ❌ Doesn't exist | Create new |
| `resources/views/user/fund-wallet.blade.php` | ⚠️ No KYC check | Add check |

### Classic PHP Files:

| File | Current Status | Action Required |
|------|---------------|-----------------|
| `core/Models/Account.php` | ❌ Hardcoded BVN | Use database |
| `mobile/core/Models/Account.php` | ❌ Hardcoded BVN | Use database |
| `mobile1/core/Models/Account.php` | ❌ Hardcoded BVN | Use database |
| `mobile/home/authentication.php` | ⚠️ No backend | Fix JS calls |
| `mobile/home/submit-kyc.php` | ❌ Doesn't exist | Create new |
| Database `subscribers` table | ⚠️ Has columns | Start using them |

---

## 📈 Migration Path for Existing Users

### Problem:
Existing users already have virtual accounts created with fake BVN.

### Solution:

1. **Create KYC Campaign:**
   ```
   - Email all existing users
   - Explain CBN requirement
   - Deadline to complete KYC
   ```

2. **Grace Period:**
   ```
   - Existing virtual accounts remain active
   - New accounts require KYC
   - Gradually enforce for all
   ```

3. **Database Update:**
   ```sql
   -- Flag existing accounts
   UPDATE users 
   SET kyc_status = 'pending_migration' 
   WHERE virtual_accounts IS NOT NULL 
   AND (nin IS NULL AND bvn IS NULL);
   
   -- After user completes KYC, update to 'verified'
   ```

4. **Re-generate Accounts:**
   ```
   - After user completes KYC
   - Optionally regenerate virtual account with real BVN/NIN
   - Or keep existing if Monnify allows update
   ```

---

## 🚦 Implementation Priority Matrix

| Priority | Task | Laravel | Classic PHP | Estimated Time |
|----------|------|---------|-------------|----------------|
| 🔴 P1 | Add database columns | ✅ Required | ⚠️ May exist | 2 hours |
| 🔴 P1 | Remove hardcoded BVN | N/A | ✅ Required | 2 hours |
| 🔴 P1 | Add BVN/NIN to payload | ✅ Required | ✅ Required | 1 hour |
| 🟠 P2 | Create KYC service | ✅ Required | ✅ Required | 4 hours |
| 🟠 P2 | Create KYC UI | ✅ Required | ⚠️ Fix existing | 6 hours |
| 🟡 P3 | Add KYC check in fund wallet | ✅ Required | ✅ Required | 2 hours |
| 🟡 P3 | Testing & validation | ✅ Required | ✅ Required | 4 hours |
| 🟢 P4 | Migrate existing users | Optional | Optional | Ongoing |

**Total Estimated Time:** 20-25 hours of development

---

## ✅ Success Criteria

Implementation is successful when:

1. ✅ **No hardcoded BVN/NIN anywhere in codebase**
2. ✅ **All virtual accounts created with real, verified KYC data**
3. ✅ **Users cannot generate accounts without completing KYC**
4. ✅ **Monnify API accepts all requests (no validation errors)**
5. ✅ **Database stores all KYC data properly**
6. ✅ **UI flows smoothly from KYC to account generation**
7. ✅ **CBN compliance requirements met**

---

## 📞 Next Steps

1. **Review this document** with development team
2. **Prioritize Laravel or Classic PHP** (or both in parallel)
3. **Start with database migration**
4. **Create KYC service layer**
5. **Build UI components**
6. **Test with Monnify sandbox**
7. **Deploy to production**
8. **Monitor and iterate**

---

**For detailed implementation code, see:** `MONNIFY_KYC_GAP_ANALYSIS_AND_PLAN.md`
