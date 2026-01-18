# Monnify Virtual Account & KYC Implementation: Gap Analysis & Action Plan

**Date:** January 18, 2026  
**Subject:** Monnify Customer Reserved Account Implementation Review & NIN/BVN KYC Integration Plan

---

## Table of Contents
1. [Executive Summary](#executive-summary)
2. [Monnify Documentation Requirements](#monnify-documentation-requirements)
3. [Current Implementation Analysis](#current-implementation-analysis)
4. [Gap Analysis](#gap-analysis)
5. [NIN/BVN Collection in Classic PHP Version](#ninbvn-collection-in-classic-php-version)
6. [Implementation Plan](#implementation-plan)
7. [Technical Specifications](#technical-specifications)

---

## Executive Summary

This document provides a comprehensive gap analysis between Monnify's Customer Reserved Account API documentation and the current implementation in both the Laravel and Classic PHP versions. It also outlines a plan for proper NIN/BVN collection and KYC compliance.

### Key Findings:
- ✅ **Current Implementation**: Partially functional with **hardcoded BVN**
- ❌ **Major Gap**: No actual NIN/BVN collection from users
- ⚠️ **Compliance Issue**: Using fake BVN ("22433145825") violates Monnify requirements
- 🔧 **Classic PHP**: Has UI for NIN/BVN but lacks backend integration
- 📊 **Database**: Schema supports NIN/BVN but fields are unused

---

## Monnify Documentation Requirements

### Required Fields for Reserved Account Creation

According to Monnify documentation, the following fields are **REQUIRED**:

```json
{
  "accountReference": "abc123",           // ✅ Implemented
  "accountName": "Test Reserved Account", // ✅ Implemented
  "currencyCode": "NGN",                  // ✅ Implemented
  "contractCode": "8389328412",          // ✅ Implemented
  "customerEmail": "test@tester.com",    // ✅ Implemented
  "bvn": "21212121212",                  // ❌ HARDCODED - NOT FROM USER
  "customerName": "John Doe",            // ✅ Implemented
  "getAllAvailableBanks": true           // ✅ Implemented (set to false)
}
```

### Critical Note from Documentation:
> **⚠️ "Please note that the BVN or NIN of your customer MUST be supplied when sending this request."**

This is a **MANDATORY requirement** that is currently violated.

---

## Current Implementation Analysis

### Laravel Version (`laravel_version/app/Services/MonnifyService.php`)

**Location:** `app/Services/MonnifyService.php` → `createVirtualAccount()` method

#### What's Working ✅
1. **Authentication Flow**
   - Bearer token authentication with caching
   - Automatic token refresh (55-minute cache)
   - Proper error handling

2. **Account Creation API Call**
   - Correct endpoint: `/api/v2/bank-transfer/reserved-accounts`
   - Proper HTTP headers
   - JSON payload structure

3. **Response Handling**
   - Parses multiple bank accounts
   - Saves virtual accounts to user record
   - Stores in `users.virtual_accounts` (JSON format)

#### Critical Issues ❌

```php
// Line ~121 in MonnifyService.php
$payload = [
    'accountReference' => $reference,
    'accountName' => $fullName,
    'currencyCode' => 'NGN',
    'contractCode' => $this->contractCode,
    'customerEmail' => $user->email,
    'customerName' => $fullName,
    'getAllAvailableBanks' => false,
    'preferredBanks' => ['035', '120', '232']
    // ❌ NO BVN OR NIN FIELD!
];
```

**Problem:** The Laravel implementation completely **omits** the BVN/NIN field, which is mandatory per Monnify documentation.

---

### Classic PHP Version (`core/Models/Account.php`)

**Location:** `core/Models/Account.php` → `createVirtualBankAccount()` methods

#### What's Working ✅
1. Three separate methods for different banks (Wema, Sterling, Fidelity/Moniepoint)
2. Basic authentication flow
3. Account creation POST requests

#### Critical Issues ❌

```php
// Line ~372 in Account.php
CURLOPT_POSTFIELDS =>
'{
    "accountReference": "' . $ref . '",
    "accountName": "' . $fullname . '",
    "currencyCode": "NGN",
    "contractCode": "' . $monnifyContract . '",
    "customerEmail": "' . $email . '",
    "bvn": "22433145825",  // ❌ HARDCODED FAKE BVN!
    "customerName": "' . $fullname . '",
    "getAllAvailableBanks": false,
    "preferredBanks": ["035"]
}'
```

**Problems:**
1. **Hardcoded BVN:** Uses "22433145825" - likely a test/fake number
2. **No NIN Support:** Never uses NIN even though it's accepted
3. **Same BVN for All Users:** Violates uniqueness requirement
4. **Compliance Risk:** May cause account creation failures or regulatory issues

---

### Classic PHP KYC Collection (`mobile/home/authentication.php`)

**Location:** `mobile/home/authentication.php`

#### What Exists ✅
1. **UI Form:** Nice form with NIN/BVN input fields
2. **Client-side Validation:** 11-digit restriction
3. **Monnify API Endpoints Referenced:**
   - NIN Verification: `/api/v1/vas/nin-details`
   - BVN Verification: `/api/v1/vas/bvn-details-match`

#### Critical Issues ❌

```javascript
// Line ~103-107 in authentication.php
function makeMonnifyRequest(endpoint, requestData) {
    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            // ❌ NO AUTHORIZATION HEADER!
        },
        body: JSON.stringify(requestData)
    })
    // ...
}
```

**Problems:**
1. **Missing Authentication:** No Bearer token in verification requests
2. **Not Integrated:** Form exists but data isn't saved to database
3. **Incomplete BVN Verification:** Requires name, DOB, mobile - but uses placeholders
4. **No Backend Processing:** All logic is client-side only

```javascript
// Line ~95 - Placeholder values
var name = "User Full Name"; // Replace with actual user's full name
var dob = "User Date of Birth"; // Replace with actual user's date of birth
var mobileNo = "User Mobile Number"; // Replace with actual user's mobile number
```

---

## Gap Analysis

### Summary Table

| Feature | Monnify Requirement | Laravel Implementation | Classic PHP Implementation | Gap Severity |
|---------|-------------------|----------------------|---------------------------|--------------|
| **BVN Field** | Required (one of BVN/NIN) | ❌ Missing | ❌ Hardcoded fake value | 🔴 **CRITICAL** |
| **NIN Field** | Required (one of BVN/NIN) | ❌ Missing | ❌ Not implemented | 🔴 **CRITICAL** |
| **User Input Collection** | Must be from customer | ❌ Not collected | ⚠️ UI exists but no backend | 🔴 **CRITICAL** |
| **Database Storage** | Not specified | ❌ No columns | ✅ Columns exist but unused | 🟡 **MEDIUM** |
| **KYC Verification** | Recommended | ❌ Not implemented | ⚠️ Client-only, no auth | 🟠 **HIGH** |
| **Uniqueness** | Each customer unique | ❌ N/A | ❌ Same for all users | 🔴 **CRITICAL** |

### Detailed Gaps

#### 1. **Laravel Version Gaps** 🔴

**File:** `laravel_version/app/Services/MonnifyService.php`

- **No BVN/NIN in payload** (Line ~121)
- **No database columns for NIN/BVN**
  - Migration `2025_11_05_002_enhance_existing_tables_for_php_app_compatibility.php` doesn't include these fields
  - SQL file `laravel_vtu.sql` (Line 976-977) has columns but not in Laravel migrations
- **No UI to collect NIN/BVN**
  - `fund-wallet.blade.php` doesn't prompt for KYC
  - `profile.blade.php` doesn't have NIN/BVN fields
- **No validation before account creation**

#### 2. **Classic PHP Version Gaps** 🔴

**Files:** 
- `core/Models/Account.php`
- `mobile/core/Models/Account.php`
- `mobile1/core/Models/Account.php`

- **Hardcoded BVN in all 3 methods**:
  - `createVirtualBankAccount()` - Line 372
  - `createVirtualBankAccount2()` - Line 437
  - `createVirtualBankAccount3()` - Line 509
- **Same fake BVN for all users** ("22433145825")
- **Database columns exist but never populated:**
  - SQL: `CREATE TABLE subscribers ... nin varchar(11), bvn varchar(11)`
- **KYC form exists but disconnected**

#### 3. **KYC Collection Gaps** 🟠

**File:** `mobile/home/authentication.php`

- **No Bearer token authentication** for Monnify VAS API
- **Placeholder data** instead of real user data
- **No server-side processing** - purely client-side
- **No database persistence** after verification
- **No error handling** for failed verifications
- **No integration with account creation** flow

---

## NIN/BVN Collection in Classic PHP Version

### Current State

The classic PHP version has a **KYC verification page** at `mobile/home/authentication.php` but it's incomplete.

#### What Exists:

```php
// UI Form
<input type="text" name="nin" id="nin" placeholder="Enter your NIN" />
<input type="text" name="bvn" id="bvn" placeholder="Enter your BVN" />
<button onclick="verifyNINandBVN()">Verify</button>
```

#### JavaScript Verification Logic:

```javascript
function verifyNIN(endpoint, nin) {
    var requestData = { nin: nin };
    makeMonnifyRequest(endpoint, requestData);
}

function verifyBVN(endpoint, bvn, name, dob, mobileNo) {
    var requestData = { bvn: bvn, name: name, dateOfBirth: dob, mobileNo: mobileNo };
    makeMonnifyRequest(endpoint, requestData);
}
```

#### Problems:

1. **No authentication token** - Monnify VAS APIs require Bearer token
2. **Placeholder user data** - BVN verification needs real name, DOB, mobile
3. **No backend persistence** - Data not saved to `subscribers.nin` or `subscribers.bvn`
4. **No integration** - Not linked to virtual account creation flow
5. **Client-side only** - Vulnerable to tampering

---

## Implementation Plan

### Phase 1: Database Schema Updates 🗄️

#### For Laravel Version

**Create Migration:** `2026_01_18_add_kyc_fields_to_users_table.php`

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // KYC Fields
        $table->string('nin', 11)->nullable()->after('phone');
        $table->string('bvn', 11)->nullable()->after('nin');
        $table->date('date_of_birth')->nullable()->after('bvn');
        
        // KYC Status
        $table->enum('kyc_status', ['pending', 'verified', 'rejected'])
              ->default('pending')
              ->after('date_of_birth');
        $table->timestamp('kyc_verified_at')->nullable()->after('kyc_status');
        $table->text('kyc_rejection_reason')->nullable()->after('kyc_verified_at');
        
        // Which method used
        $table->enum('kyc_method', ['nin', 'bvn', 'none'])->default('none')->after('kyc_rejection_reason');
        
        // Add indexes for quick lookup
        $table->index('nin');
        $table->index('bvn');
        $table->index('kyc_status');
    });
}
```

#### For Classic PHP Version

The database already has the columns in the SQL file:
```sql
nin varchar(11) DEFAULT NULL,
bvn varchar(11) DEFAULT NULL,
```

**Action Required:** Ensure these columns are in the actual database if not already present.

---

### Phase 2: Backend API Integration 🔧

#### 2A. Laravel KYC Service

**Create:** `laravel_version/app/Services/KycService.php`

```php
<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KycService
{
    private $monnifyService;
    
    public function __construct(MonnifyService $monnifyService)
    {
        $this->monnifyService = $monnifyService;
    }
    
    /**
     * Verify NIN via Monnify VAS API
     */
    public function verifyNin(string $nin): array
    {
        try {
            $accessToken = $this->monnifyService->getAccessToken();
            if (!$accessToken) {
                return ['success' => false, 'message' => 'Authentication failed'];
            }
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post(config('services.monnify.base_url') . '/api/v1/vas/nin-details', [
                    'nin' => $nin
                ]);
                
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['requestSuccessful']) && $data['requestSuccessful']) {
                    return [
                        'success' => true,
                        'message' => 'NIN verified successfully',
                        'data' => $data['responseBody']
                    ];
                }
            }
            
            return [
                'success' => false,
                'message' => 'NIN verification failed: ' . ($data['responseMessage'] ?? 'Unknown error')
            ];
            
        } catch (\Exception $e) {
            Log::error('NIN verification error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Verification service unavailable'];
        }
    }
    
    /**
     * Verify BVN via Monnify VAS API
     */
    public function verifyBvn(string $bvn, string $name, string $dob, string $mobileNo): array
    {
        try {
            $accessToken = $this->monnifyService->getAccessToken();
            if (!$accessToken) {
                return ['success' => false, 'message' => 'Authentication failed'];
            }
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post(config('services.monnify.base_url') . '/api/v1/vas/bvn-details-match', [
                    'bvn' => $bvn,
                    'name' => $name,
                    'dateOfBirth' => $dob,
                    'mobileNo' => $mobileNo
                ]);
                
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['requestSuccessful']) && $data['requestSuccessful']) {
                    return [
                        'success' => true,
                        'message' => 'BVN verified successfully',
                        'data' => $data['responseBody']
                    ];
                }
            }
            
            return [
                'success' => false,
                'message' => 'BVN verification failed: ' . ($data['responseMessage'] ?? 'Unknown error')
            ];
            
        } catch (\Exception $e) {
            Log::error('BVN verification error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Verification service unavailable'];
        }
    }
    
    /**
     * Save KYC data to user profile
     */
    public function saveKycData(User $user, array $kycData): bool
    {
        try {
            $user->update([
                'nin' => $kycData['nin'] ?? null,
                'bvn' => $kycData['bvn'] ?? null,
                'date_of_birth' => $kycData['date_of_birth'] ?? null,
                'kyc_status' => 'verified',
                'kyc_verified_at' => Carbon::now(),
                'kyc_method' => $kycData['method'] ?? 'none'
            ]);
            
            Log::info('KYC data saved for user', ['user_id' => $user->id]);
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to save KYC data: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user has completed KYC
     */
    public function hasCompletedKyc(User $user): bool
    {
        return !empty($user->nin) || !empty($user->bvn);
    }
}
```

#### 2B. Update MonnifyService to Use Real NIN/BVN

**Modify:** `laravel_version/app/Services/MonnifyService.php`

```php
// Around Line 90
public function createVirtualAccount(User $user)
{
    try {
        // ✅ NEW: Validate KYC before creating account
        if (empty($user->nin) && empty($user->bvn)) {
            return [
                'success' => false,
                'message' => 'Please complete KYC verification (NIN or BVN) before generating virtual account',
                'requires_kyc' => true
            ];
        }
        
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to authenticate with Monnify'];
        }
        
        $reference = 'VA_' . $user->id . '_' . uniqid() . rand(1000, 9999);
        $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        
        // ... (existing name validation code) ...
        
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
        
        // ✅ NEW: Add BVN or NIN to payload
        if (!empty($user->bvn)) {
            $payload['bvn'] = $user->bvn;
        } elseif (!empty($user->nin)) {
            $payload['nin'] = $user->nin;
        }
        
        // ... (rest of the method) ...
    }
}
```

---

### Phase 3: Frontend UI Development 🎨

#### 3A. Laravel KYC Verification Page

**Create:** `laravel_version/resources/views/kyc-verification.blade.php`

```blade
@extends('layouts.user-layout')

@section('title', 'KYC Verification')

@section('page-content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white mb-8">
            <div class="flex items-center justify-center mb-4">
                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                    <i class="fas fa-shield-alt text-4xl"></i>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-center mb-2">Complete KYC Verification</h1>
            <p class="text-blue-100 text-center">Verify your identity with NIN or BVN to enable virtual account</p>
        </div>

        <!-- Notice Banner -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>CBN Compliance Notice:</strong> In accordance with CBN regulations for virtual accounts, 
                        you must verify either your NIN or BVN. Your data is securely transmitted and not stored on our servers.
                    </p>
                </div>
            </div>
        </div>

        <!-- KYC Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Choose Verification Method</h2>
                <p class="text-gray-600">You can verify using either your NIN or BVN</p>
            </div>

            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 mb-6">
                <button 
                    onclick="switchTab('nin')" 
                    id="nin-tab"
                    class="flex-1 py-4 px-6 text-center border-b-2 border-blue-500 text-blue-600 font-medium"
                >
                    <i class="fas fa-id-card mr-2"></i>NIN Verification
                </button>
                <button 
                    onclick="switchTab('bvn')" 
                    id="bvn-tab"
                    class="flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 font-medium hover:text-gray-700"
                >
                    <i class="fas fa-university mr-2"></i>BVN Verification
                </button>
            </div>

            <!-- NIN Tab Content -->
            <div id="nin-content" class="tab-content">
                <form id="nin-form" onsubmit="verifyNIN(event)">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="nin" class="block text-sm font-medium text-gray-700 mb-2">
                                National Identification Number (NIN)
                            </label>
                            <input 
                                type="text" 
                                id="nin" 
                                name="nin" 
                                maxlength="11"
                                pattern="[0-9]{11}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter your 11-digit NIN"
                                required
                            >
                            <p class="mt-1 text-sm text-gray-500">Enter your 11-digit NIN</p>
                        </div>

                        <button 
                            type="submit" 
                            id="verify-nin-btn"
                            class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium"
                        >
                            <i class="fas fa-check-circle mr-2"></i>Verify NIN
                        </button>
                    </div>
                </form>
            </div>

            <!-- BVN Tab Content -->
            <div id="bvn-content" class="tab-content hidden">
                <form id="bvn-form" onsubmit="verifyBVN(event)">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="bvn" class="block text-sm font-medium text-gray-700 mb-2">
                                Bank Verification Number (BVN)
                            </label>
                            <input 
                                type="text" 
                                id="bvn" 
                                name="bvn" 
                                maxlength="11"
                                pattern="[0-9]{11}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter your 11-digit BVN"
                                required
                            >
                            <p class="mt-1 text-sm text-gray-500">Enter your 11-digit BVN</p>
                        </div>

                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-700 mb-2">
                                Date of Birth
                            </label>
                            <input 
                                type="date" 
                                id="dob" 
                                name="dob" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                required
                            >
                            <p class="mt-1 text-sm text-gray-500">As registered with your bank</p>
                        </div>

                        <button 
                            type="submit" 
                            id="verify-bvn-btn"
                            class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium"
                        >
                            <i class="fas fa-check-circle mr-2"></i>Verify BVN
                        </button>
                    </div>
                </form>
            </div>

            <!-- Status Message -->
            <div id="status-message" class="mt-6 hidden">
                <!-- Dynamic content -->
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <h3 class="font-bold text-blue-900 mb-2">
                    <i class="fas fa-lock mr-2"></i>Secure & Private
                </h3>
                <p class="text-sm text-blue-800">Your KYC data is encrypted and transmitted securely to Monnify for verification only.</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <h3 class="font-bold text-green-900 mb-2">
                    <i class="fas fa-clock mr-2"></i>Instant Verification
                </h3>
                <p class="text-sm text-green-800">Verification typically completes within seconds once submitted.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchTab(tab) {
    // Update tab buttons
    document.getElementById('nin-tab').className = tab === 'nin' 
        ? 'flex-1 py-4 px-6 text-center border-b-2 border-blue-500 text-blue-600 font-medium'
        : 'flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 font-medium hover:text-gray-700';
    
    document.getElementById('bvn-tab').className = tab === 'bvn'
        ? 'flex-1 py-4 px-6 text-center border-b-2 border-blue-500 text-blue-600 font-medium'
        : 'flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 font-medium hover:text-gray-700';
    
    // Update content
    document.getElementById('nin-content').classList.toggle('hidden', tab !== 'nin');
    document.getElementById('bvn-content').classList.toggle('hidden', tab !== 'bvn');
}

function verifyNIN(event) {
    event.preventDefault();
    
    const btn = document.getElementById('verify-nin-btn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verifying...';
    
    const formData = new FormData(event.target);
    
    fetch('/api/kyc/verify-nin', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        if (data.success) {
            showStatus('success', data.message);
            setTimeout(() => {
                window.location.href = '/user/fund-wallet';
            }, 2000);
        } else {
            showStatus('error', data.message);
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        showStatus('error', 'Verification request failed. Please try again.');
    });
}

function verifyBVN(event) {
    event.preventDefault();
    
    const btn = document.getElementById('verify-bvn-btn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verifying...';
    
    const formData = new FormData(event.target);
    
    fetch('/api/kyc/verify-bvn', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        if (data.success) {
            showStatus('success', data.message);
            setTimeout(() => {
                window.location.href = '/user/fund-wallet';
            }, 2000);
        } else {
            showStatus('error', data.message);
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        showStatus('error', 'Verification request failed. Please try again.');
    });
}

function showStatus(type, message) {
    const statusDiv = document.getElementById('status-message');
    statusDiv.className = 'mt-6 p-4 rounded-lg ' + (type === 'success' 
        ? 'bg-green-50 border border-green-200' 
        : 'bg-red-50 border border-red-200');
    
    statusDiv.innerHTML = `
        <div class="flex items-start">
            <i class="fas fa-${type === 'success' ? 'check-circle text-green-600' : 'exclamation-circle text-red-600'} mt-1 mr-3"></i>
            <p class="text-sm ${type === 'success' ? 'text-green-800' : 'text-red-800'}">${message}</p>
        </div>
    `;
    statusDiv.classList.remove('hidden');
}
</script>
@endpush
@endsection
```

#### 3B. Update Fund Wallet Page to Check KYC

**Modify:** `laravel_version/resources/views/user/fund-wallet.blade.php`

Add KYC check before showing virtual account generation:

```blade
@if(!empty($virtualAccounts) && count($virtualAccounts) > 0)
    <!-- Existing virtual accounts display -->
@else
    @if(auth()->user()->kyc_status !== 'verified')
        <!-- KYC Required Notice -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-2xl p-8 text-center">
                <div class="bg-yellow-500 p-4 rounded-full w-20 h-20 mx-auto mb-6">
                    <i class="fas fa-shield-alt text-white text-3xl mt-2"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">KYC Verification Required</h3>
                <p class="text-gray-600 mb-8 text-lg">
                    Before you can generate virtual account numbers, you need to complete KYC verification 
                    by providing your NIN or BVN. This is a CBN requirement for all virtual accounts.
                </p>

                <a href="{{ route('kyc.verification') }}"
                   class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-12 py-4 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-shield-alt mr-3"></i>Complete KYC Verification
                </a>
            </div>
        </div>
    @else
        <!-- Existing virtual account generation button -->
    @endif
@endif
```

---

### Phase 4: API Controllers 🎮

**Create:** `laravel_version/app/Http/Controllers/KycController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Services\KycService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KycController extends Controller
{
    private $kycService;
    
    public function __construct(KycService $kycService)
    {
        $this->middleware('auth');
        $this->kycService = $kycService;
    }
    
    /**
     * Show KYC verification page
     */
    public function showVerificationForm()
    {
        $user = Auth::user();
        
        // Redirect if already verified
        if ($user->kyc_status === 'verified') {
            return redirect()->route('user.fund-wallet')
                ->with('info', 'Your KYC is already verified.');
        }
        
        return view('kyc-verification');
    }
    
    /**
     * Verify NIN
     */
    public function verifyNin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nin' => 'required|digits:11|unique:users,nin'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        
        $result = $this->kycService->verifyNin($request->nin);
        
        if ($result['success']) {
            // Save NIN to user profile
            $saved = $this->kycService->saveKycData(Auth::user(), [
                'nin' => $request->nin,
                'method' => 'nin'
            ]);
            
            if ($saved) {
                return response()->json([
                    'success' => true,
                    'message' => 'NIN verified successfully! Redirecting to fund wallet...'
                ]);
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }
    
    /**
     * Verify BVN
     */
    public function verifyBvn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bvn' => 'required|digits:11|unique:users,bvn',
            'dob' => 'required|date|before:today'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        
        $user = Auth::user();
        $fullName = trim($user->first_name . ' ' . $user->last_name);
        
        $result = $this->kycService->verifyBvn(
            $request->bvn,
            $fullName,
            $request->dob,
            $user->phone
        );
        
        if ($result['success']) {
            // Save BVN to user profile
            $saved = $this->kycService->saveKycData($user, [
                'bvn' => $request->bvn,
                'date_of_birth' => $request->dob,
                'method' => 'bvn'
            ]);
            
            if ($saved) {
                return response()->json([
                    'success' => true,
                    'message' => 'BVN verified successfully! Redirecting to fund wallet...'
                ]);
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }
}
```

---

### Phase 5: Routes Configuration 🛣️

**Add to:** `laravel_version/routes/web.php`

```php
// KYC Verification Routes
Route::middleware('auth')->prefix('kyc')->name('kyc.')->group(function () {
    Route::get('/verification', [KycController::class, 'showVerificationForm'])->name('verification');
});

Route::middleware('auth')->prefix('api/kyc')->group(function () {
    Route::post('/verify-nin', [KycController::class, 'verifyNin']);
    Route::post('/verify-bvn', [KycController::class, 'verifyBvn']);
});
```

---

### Phase 6: Classic PHP Implementation 🔧

#### 6A. Update Account.php to Use Real BVN/NIN

**Modify:** `core/Models/Account.php` (and similar in `mobile/` and `mobile1/`)

```php
// Around Line 318
public function createVirtualBankAccount($id, $fname, $lname, $phone, $email, $monnifyApi, $monnifySecret, $monnifyContract)
{
    $fullname = $fname . " " . $lname;
    $accessKey = "$monnifyApi:$monnifySecret";
    $apiKey = base64_encode($accessKey);
    
    // ✅ NEW: Get user's BVN or NIN from database
    $dbh = self::connect();
    $sql = "SELECT nin, bvn FROM subscribers WHERE sId = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $userData = $query->fetch(PDO::FETCH_OBJ);
    
    // ✅ NEW: Validate that user has BVN or NIN
    if (empty($userData->bvn) && empty($userData->nin)) {
        // Log error or return without creating account
        error_log("Cannot create virtual account for user $id - No BVN/NIN found");
        return false;
    }
    
    // Get Authorization Data
    $url = 'https://api.monnify.com/api/v1/auth/login';
    $url2 = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts";
    
    // ... (existing auth code) ...
    
    // ✅ MODIFIED: Build payload with real BVN or NIN
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
    
    // ✅ NEW: Add BVN or NIN (prefer BVN if both exist)
    if (!empty($userData->bvn)) {
        $payload['bvn'] = $userData->bvn;
    } elseif (!empty($userData->nin)) {
        $payload['nin'] = $userData->nin;
    }
    
    // ✅ MODIFIED: Use JSON encoding for payload
    curl_setopt_array($curl, array(
        CURLOPT_URL =>  $url2,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $accessToken,
            "Content-Type: application/json"
        ),
    ));
    
    // ... (rest of the method) ...
}
```

#### 6B. Create Backend for KYC Submission

**Create:** `mobile/home/submit-kyc.php`

```php
<?php
session_start();
require_once '../core/autoload.php';

use Controllers\ApiAccess as ApiAccessController;
$controller = new ApiAccessController();

// Get logged-in user
if (!isset($_SESSION['sId'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$userId = $_SESSION['sId'];

// Get POST data
$nin = isset($_POST['nin']) ? trim($_POST['nin']) : '';
$bvn = isset($_POST['bvn']) ? trim($_POST['bvn']) : '';

// Validate
if (empty($nin) && empty($bvn)) {
    echo json_encode(['success' => false, 'message' => 'Please provide NIN or BVN']);
    exit;
}

// Validate format
if (!empty($nin) && !preg_match('/^\d{11}$/', $nin)) {
    echo json_encode(['success' => false, 'message' => 'NIN must be 11 digits']);
    exit;
}

if (!empty($bvn) && !preg_match('/^\d{11}$/', $bvn)) {
    echo json_encode(['success' => false, 'message' => 'BVN must be 11 digits']);
    exit;
}

// Get Monnify credentials
$data = $controller->getApiConfiguration();
$monifyApi = $controller->getConfigValue($data, "monifyApi");
$monifySecrete = $controller->getConfigValue($data, "monifySecrete");

// Get Monnify access token
$accessKey = "$monifyApi:$monifySecrete";
$apiKey = base64_encode($accessKey);

$authUrl = 'https://api.monnify.com/api/v1/auth/login';
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => $authUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => array("Authorization: Basic {$apiKey}"),
));

$json = curl_exec($ch);
$result = json_decode($json);
curl_close($ch);

if (!isset($result->responseBody->accessToken)) {
    echo json_encode(['success' => false, 'message' => 'Authentication failed']);
    exit;
}

$accessToken = $result->responseBody->accessToken;

// Verify NIN or BVN
if (!empty($nin)) {
    // Verify NIN
    $verifyUrl = 'https://api.monnify.com/api/v1/vas/nin-details';
    $payload = json_encode(['nin' => $nin]);
} else {
    // Verify BVN
    // Get user details for BVN verification
    $userData = $controller->getSubscriber($userId);
    $fullName = $userData->sFname . ' ' . $userData->sLname;
    
    $verifyUrl = 'https://api.monnify.com/api/v1/vas/bvn-details-match';
    $payload = json_encode([
        'bvn' => $bvn,
        'name' => $fullName,
        'dateOfBirth' => $_POST['dob'] ?? '',
        'mobileNo' => $userData->sPhone
    ]);
}

$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => $verifyUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer {$accessToken}",
        "Content-Type: application/json"
    ),
));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$responseData = json_decode($response);

if ($httpCode == 200 && isset($responseData->requestSuccessful) && $responseData->requestSuccessful) {
    // Verification successful - save to database
    $dbh = $controller->connect();
    
    if (!empty($nin)) {
        $sql = "UPDATE subscribers SET nin = :nin WHERE sId = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':nin', $nin, PDO::PARAM_STR);
    } else {
        $sql = "UPDATE subscribers SET bvn = :bvn WHERE sId = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':bvn', $bvn, PDO::PARAM_STR);
    }
    
    $query->bindParam(':id', $userId, PDO::PARAM_INT);
    
    if ($query->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Verification successful! You can now generate virtual account.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Verification successful but failed to save data'
        ]);
    }
} else {
    $errorMsg = isset($responseData->responseMessage) 
        ? $responseData->responseMessage 
        : 'Verification failed';
    
    echo json_encode(['success' => false, 'message' => $errorMsg]);
}
?>
```

#### 6C. Update authentication.php

**Modify:** `mobile/home/authentication.php`

Change the JavaScript to call the new backend:

```javascript
function makeMonnifyRequest(endpoint, requestData) {
    // ✅ NEW: Call our backend instead of Monnify directly
    const formData = new FormData();
    
    if (requestData.nin) {
        formData.append('nin', requestData.nin);
    } else {
        formData.append('bvn', requestData.bvn);
        formData.append('dob', requestData.dateOfBirth);
    }
    
    fetch('submit-kyc.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayVerificationStatus(
                '<div class="alert alert-success">' + data.message + '</div>'
            );
            // Redirect to fund wallet after 2 seconds
            setTimeout(() => {
                window.location.href = 'fund-wallet.php';
            }, 2000);
        } else {
            displayVerificationStatus(
                '<div class="alert alert-danger">' + data.message + '</div>'
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        displayVerificationStatus(
            '<div class="alert alert-danger">Request failed. Please try again.</div>'
        );
    });
}
```

---

### Phase 7: Testing Checklist ✅

#### Unit Tests

- [ ] Test KYC service NIN verification
- [ ] Test KYC service BVN verification
- [ ] Test virtual account creation with NIN
- [ ] Test virtual account creation with BVN
- [ ] Test virtual account creation without KYC (should fail)
- [ ] Test duplicate NIN/BVN prevention

#### Integration Tests

- [ ] Test complete flow: Register → KYC → Virtual Account
- [ ] Test KYC verification page UI
- [ ] Test fund wallet page with KYC check
- [ ] Test Monnify API authentication
- [ ] Test Monnify VAS API endpoints
- [ ] Test database storage of KYC data

#### Manual Testing

- [ ] Register new user
- [ ] Complete NIN verification
- [ ] Generate virtual account with NIN
- [ ] Verify account creation successful
- [ ] Test with BVN instead
- [ ] Test with invalid NIN/BVN
- [ ] Test duplicate prevention
- [ ] Test UI/UX flow

---

### Phase 8: Deployment Plan 🚀

#### Pre-Deployment

1. **Backup Database**
   ```bash
   mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
   ```

2. **Run Migrations**
   ```bash
   cd laravel_version
   php artisan migrate
   ```

3. **Clear Caches**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

#### Deployment Steps

1. **Push to Version Control**
   ```bash
   git add .
   git commit -m "Implement NIN/BVN KYC verification and Monnify integration"
   git push origin main
   ```

2. **Deploy to Server**
   - Pull latest changes
   - Run migrations
   - Clear caches

3. **Verify Configuration**
   - Check Monnify credentials in database/config
   - Test API connectivity
   - Verify database columns

#### Post-Deployment

1. **Monitor Logs**
   - Check `laravel_version/storage/logs/` for errors
   - Monitor Monnify API responses

2. **Test Live**
   - Create test account
   - Complete KYC process
   - Generate virtual account

3. **Notify Users**
   - Send email/notification about new KYC requirement
   - Provide help documentation

---

## Technical Specifications

### Database Schema Changes

```sql
-- Laravel version
ALTER TABLE `users` 
ADD COLUMN `nin` varchar(11) NULL AFTER `phone`,
ADD COLUMN `bvn` varchar(11) NULL AFTER `nin`,
ADD COLUMN `date_of_birth` date NULL AFTER `bvn`,
ADD COLUMN `kyc_status` enum('pending','verified','rejected') DEFAULT 'pending' AFTER `date_of_birth`,
ADD COLUMN `kyc_verified_at` timestamp NULL AFTER `kyc_status`,
ADD COLUMN `kyc_rejection_reason` text NULL AFTER `kyc_verified_at`,
ADD COLUMN `kyc_method` enum('nin','bvn','none') DEFAULT 'none' AFTER `kyc_rejection_reason`,
ADD INDEX `idx_nin` (`nin`),
ADD INDEX `idx_bvn` (`bvn`),
ADD INDEX `idx_kyc_status` (`kyc_status`);

-- Classic PHP version (if columns don't exist)
ALTER TABLE `subscribers`
ADD COLUMN `nin` varchar(11) NULL,
ADD COLUMN `bvn` varchar(11) NULL;
```

### API Endpoints

#### Laravel Routes

- `GET /kyc/verification` - Show KYC verification form
- `POST /api/kyc/verify-nin` - Verify NIN
- `POST /api/kyc/verify-bvn` - Verify BVN

#### Classic PHP Endpoints

- `mobile/home/authentication.php` - KYC verification page
- `mobile/home/submit-kyc.php` - Process KYC submission

### Monnify API Endpoints Used

1. **Authentication**
   - `POST /api/v1/auth/login`
   - Headers: `Authorization: Basic {base64(apiKey:secretKey)}`

2. **Virtual Account Creation**
   - `POST /api/v2/bank-transfer/reserved-accounts`
   - Headers: `Authorization: Bearer {accessToken}`
   - Body: Includes `bvn` or `nin`

3. **NIN Verification** (Optional)
   - `POST /api/v1/vas/nin-details`
   - Body: `{ "nin": "12345678901" }`

4. **BVN Verification** (Optional)
   - `POST /api/v1/vas/bvn-details-match`
   - Body: `{ "bvn": "12345678901", "name": "Full Name", "dateOfBirth": "YYYY-MM-DD", "mobileNo": "08012345678" }`

### Security Considerations

1. **Data Encryption**
   - NIN/BVN should be encrypted at rest if stored
   - Use HTTPS for all API calls

2. **Input Validation**
   - 11-digit numeric validation
   - Prevent SQL injection
   - CSRF token validation

3. **Rate Limiting**
   - Limit KYC verification attempts
   - Prevent brute force attacks

4. **Audit Trail**
   - Log all KYC attempts
   - Track verification timestamps

---

## Conclusion

### Summary of Gaps

1. **Critical Gaps (🔴 Must Fix):**
   - No BVN/NIN in Laravel Monnify payload
   - Hardcoded fake BVN in classic PHP version
   - No user input collection mechanism
   - Same BVN used for all users

2. **High Priority (🟠 Should Fix):**
   - KYC verification not integrated with account creation
   - No authentication in KYC API calls

3. **Medium Priority (🟡 Nice to Have):**
   - Database fields exist but unused
   - Better error handling and user feedback

### Recommended Implementation Priority

**Immediate (Week 1):**
1. Add database migrations for NIN/BVN
2. Create KYC service and controller in Laravel
3. Update MonnifyService to use real BVN/NIN
4. Fix hardcoded BVN in classic PHP

**Short-term (Week 2-3):**
5. Build KYC verification UI
6. Integrate KYC with fund wallet flow
7. Update classic PHP backend

**Long-term (Week 4+):**
8. Comprehensive testing
9. User migration for existing accounts
10. Documentation and training

### Success Metrics

- ✅ All new virtual accounts created with real NIN/BVN
- ✅ Zero hardcoded BVN/NIN in production code
- ✅ 100% KYC completion before virtual account generation
- ✅ Monnify API compliance
- ✅ Regulatory compliance with CBN requirements

---

**Document Version:** 1.0  
**Last Updated:** January 18, 2026  
**Next Review:** After Phase 1 completion
