-- Fix Uzobest API Mappings in Database
-- This SQL file corrects provider IDs and plan IDs to match official Uzobest API documentation
-- IMPORTANT: Provider IDs were REVERSED in our system!

-- ============================================
-- 1. FIX CABLE PROVIDER IDs (Critical - IDs were reversed!)
-- ============================================
-- Swap DSTV and GOTV IDs, and fix STARTIMES spelling
UPDATE cable_ids SET cableid = 999 WHERE provider = 'dstv';  -- Temporary value
UPDATE cable_ids SET cableid = 1 WHERE provider = 'gotv';    -- GOTV should be 1
UPDATE cable_ids SET cableid = 2 WHERE provider = 'dstv' AND cableid = 999;  -- DSTV should be 2
UPDATE cable_ids SET cableid = 3, provider = 'startime' WHERE provider = 'startimes';  -- Fix spelling

-- ============================================
-- 2. FIX ELECTRICITY DISCO PROVIDER IDs
-- ============================================
-- Update uzobest_disco_id to match official Uzobest API IDs
-- The ePlan contains provider names like "IKEDC - Ikeja Electric"
UPDATE electricity SET uzobest_disco_id = 1 WHERE ePlan LIKE '%IKEDC%';  -- Ikeja Electric
UPDATE electricity SET uzobest_disco_id = 2 WHERE ePlan LIKE '%EKEDC%';  -- Eko Electric
UPDATE electricity SET uzobest_disco_id = 3 WHERE ePlan LIKE '%AEDC%';   -- Abuja Electric
UPDATE electricity SET uzobest_disco_id = 4 WHERE ePlan LIKE '%KEDCO%';  -- Kano Electric
UPDATE electricity SET uzobest_disco_id = 5 WHERE ePlan LIKE '%EEDC%';   -- Enugu Electric
UPDATE electricity SET uzobest_disco_id = 6 WHERE ePlan LIKE '%PHED%';   -- Port Harcourt Electric
UPDATE electricity SET uzobest_disco_id = 7 WHERE ePlan LIKE '%IBEDC%';  -- Ibadan Electric
UPDATE electricity SET uzobest_disco_id = 8 WHERE ePlan LIKE '%KAEDCO%'; -- Kaduna Electric
UPDATE electricity SET uzobest_disco_id = 9 WHERE ePlan LIKE '%JED%';    -- Jos Electric

-- Note: Missing providers (BEDC, YEDC, ABA) will need to be added manually with proper plan details

-- ============================================
-- 3. VERIFY CABLE PLANS HAVE CORRECT PROVIDER IDs
-- ============================================
-- After swapping, cable plans should reference the correct provider IDs
-- Note: cableprovider links to cable_ids.cableid (which we just fixed)
-- DSTV plans should reference 2, GOTV plans should reference 1, STARTIME plans should reference 3
UPDATE cable_plans SET uzobest_cable_id = 2 WHERE cableprovider = 2;  -- DSTV
UPDATE cable_plans SET uzobest_cable_id = 1 WHERE cableprovider = 1;  -- GOTV
UPDATE cable_plans SET uzobest_cable_id = 3 WHERE cableprovider = 3;  -- STARTIME

-- ============================================
-- VERIFICATION QUERIES
-- ============================================
-- Run these after the updates to verify correctness:
-- SELECT * FROM cable_ids ORDER BY cableid;
-- SELECT * FROM electricity ORDER BY eProviderId;
-- SELECT provider_name, plan_name, uzobest_cable_id, uzobest_plan_id FROM cable_plans ORDER BY provider_name, plan_name;
