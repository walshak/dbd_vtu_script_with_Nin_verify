# Uzobest API Official Defaults and Mappings

## ⚠️ CRITICAL: Official Uzobest IDs (Last Updated: Dec 16, 2025)

This file contains the **official** Uzobest API mappings. All implementations must match these exactly.

## Network IDs (Data Plans)

```
Network ID | Network Name
-----------|-------------
1          | MTN
2          | GLO
3          | 9MOBILE
4          | AIRTEL
```

## Cable Provider IDs

```
Cable ID | Cable Name
---------|------------
1        | GOTV
2        | DSTV
3        | STARTIME
```

**⚠️ CRITICAL FIX REQUIRED:**
- Our system had: 1=DSTV, 2=GOTV, 3=STARTIMES
- Correct mapping: 1=GOTV, 2=DSTV, 3=STARTIME
- **Cable provider IDs are REVERSED!**

## Cable Plan IDs (Full List)

### GOTV Plans (Cable ID: 1)
```
ID | Plan Name                    | Amount
---|------------------------------|--------
2  | GOtv Max - Monthly           | 8500
16 | GOtv Jinja - Monthly         | 3900
17 | GOtv Jolli - Monthly         | 5800
34 | GOtv Smallie - Monthly       | 1900
35 | GOtv Smallie - Quarterly     | 5100
36 | GOtv Smallie - Yearly        | 15000
47 | GOtv Supa - Monthly          | 11400
48 | GOtv Supa Plus - Monthly     | 16800
```

### DSTV Plans (Cable ID: 2)
```
ID | Plan Name                     | Amount
---|-------------------------------|--------
6  | DStv Yanga                    | 6000
7  | DStv Compact                  | 19000
8  | DStv Compact Plus             | 30000
9  | DStv Premium                  | 44500
19 | DStv Confam                   | 11000
20 | DStv Padi                     | 4400
26 | DStv Confam + ExtraView       | 17000
27 | DStv Yanga + ExtraView        | 12000
28 | DStv Padi + ExtraView         | 10400
29 | DStv Compact + Extra View     | 25000
30 | DStv Premium + Extra View     | 42000
31 | DStv Compact Plus Extra View  | 36000
33 | ExtraView Access              | 6000
```

### STARTIME Plans (Cable ID: 3)
```
ID | Plan Name                     | Amount
---|-------------------------------|--------
11 | Classic - 6000 - 1 Month      | 6000
12 | Basic - 4000 - 1 Month        | 4000
13 | Smart - 5100 - 1 Month        | 5100
14 | Nova - 2100 - 1 Month         | 2100
15 | Super - 9800 - 1 Month        | 9800
37 | Nova - 700 - 1 Week           | 700
38 | Basic - 1400 - 1 Week         | 1400
39 | Smart - 1700 - 1 Week         | 1700
40 | Classic - 2500 - 1 Week       | 2500
41 | Super - 3300 - 1 Week         | 3300
49 | Global - 7000 - 1 Week        | 7000
50 | Global - 21000 - 1 Month      | 21000
```

## Electricity/Disco IDs

```
Disco ID | Disco Name
---------|-------------------
1        | Ikeja Electric (IKEDC)
2        | Eko Electric (EKEDC)
3        | Abuja Electric (AEDC)
4        | Kano Electric (KEDCO)
5        | Enugu Electric (EEDC)
6        | Port Harcourt Electric (PHED)
7        | Ibadan Electric (IBEDC)
8        | Kaduna Electric (KAEDCO)
9        | Jos Electric (JED)
10       | Benin Electric
11       | Yola Electric
12       | Aba Electric
```

**⚠️ Our System Issues:**
- We only have 9 DISCOs, Uzobest has 12
- Missing: Benin Electric (10), Yola Electric (11), Aba Electric (12)
- Our ID mapping was close but needs verification

## Airtime/Recharge Card Plans

```
ID | Network    | Amount
---|------------|--------
1  | MTN        | 100
2  | MTN        | 200
3  | MTN        | 500
4  | GLO        | 100
5  | GLO        | 200
6  | GLO        | 500
7  | 9MOBILE    | 100
8  | 9MOBILE    | 200
9  | 9MOBILE    | 500
10 | AIRTEL     | 100
11 | AIRTEL     | 200
12 | AIRTEL     | 500
```

## 🔴 CRITICAL ISSUES FOUND

### 1. Cable Provider IDs - REVERSED! ❌
**Our Implementation:**
```php
'DSTV' => 1,      // ❌ WRONG
'GOTV' => 2,      // ❌ WRONG
'STARTIMES' => 3  // ❌ WRONG (also typo: STARTIME vs STARTIMES)
```

**Correct Uzobest Mapping:**
```php
'GOTV' => 1,      // ✅ CORRECT
'DSTV' => 2,      // ✅ CORRECT
'STARTIME' => 3   // ✅ CORRECT (note: STARTIME not STARTIMES)
```

### 2. Cable Plan IDs - Completely Wrong! ❌
**Our Implementation:**
- Used sequential IDs: 1, 2, 3, 4, 5, 6...
- Example: DStv Padi = 1 ❌

**Correct Uzobest IDs:**
- Non-sequential: 2, 6, 7, 8, 9, 11, 12, 14, 15, 16, 17, 19, 20...
- Example: DStv Padi = 20 ✅
- Example: GOtv Max = 2 ✅

### 3. Missing Cable Plans
We have 16 plans, Uzobest has 33 plans total.

**Missing Plans:**
- DSTV: ExtraView plans (26-31, 33)
- GOTV: Smallie Quarterly (35), Smallie Yearly (36), Supa Plus (48)
- STARTIME: Weekly plans (37-41), Global plans (49-50)

### 4. Missing Electricity Companies
We have 9 DISCOs, Uzobest supports 12.

**Missing:**
- Benin Electric (ID: 10)
- Yola Electric (ID: 11)
- Aba Electric (ID: 12)

## 📝 Required Actions

### Priority 1: Fix Cable Provider IDs (CRITICAL)
```sql
-- Swap DSTV and GOTV in cable_ids table
UPDATE cable_ids SET cableid = 2 WHERE provider = 'dstv';
UPDATE cable_ids SET cableid = 1 WHERE provider = 'gotv';
UPDATE cable_ids SET cableid = 3, provider = 'startime' WHERE provider = 'startimes';
```

### Priority 2: Fix Cable Plan IDs (CRITICAL)
All cable plans need their `uzobest_plan_id` updated to match Uzobest's actual IDs.

**Example Mapping:**
```
Our DB Plan          | Correct Uzobest ID
---------------------|-------------------
DStv Padi           | 20 (not 1!)
DStv Yanga          | 6 (not 2!)
DStv Confam         | 19 (not 3!)
DStv Compact        | 7 (not 4!)
GOtv Smallie        | 34 (not 1!)
GOtv Jinja          | 16 (not 2!)
GOtv Max            | 2 (not 4!)
```

### Priority 3: Add Missing Cable Plans
Add 17 missing cable plans to support all Uzobest options.

### Priority 4: Add Missing Electricity Companies
Add 3 missing DISCOs (Benin, Yola, Aba).

## 🧪 Testing Requirements

After fixes:
1. Test IUC validation for each provider
2. Test cable purchase with correct provider and plan IDs
3. Verify API request shows: cablename=1 for GOTV, cablename=2 for DSTV
4. Verify API request shows correct plan IDs (e.g., cableplan=20 for DStv Padi)

## 📞 Notes

- This data is directly from Uzobest API documentation
- Any discrepancies between our system and this file must be fixed
- Plan prices may change - verify with Uzobest periodically
- Always use these official IDs when making API calls
