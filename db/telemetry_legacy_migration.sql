-- Migration: Add operational and status columns to telemetry_legacy
-- Created: 2025-12-09
-- Purpose: Fix Python Telemetry Generator compatibility

-- Add operational column (boolean flag)
ALTER TABLE telemetry_legacy
ADD COLUMN IF NOT EXISTS operational BOOLEAN DEFAULT TRUE;

-- Add status column (text status indicator)
ALTER TABLE telemetry_legacy
ADD COLUMN IF NOT EXISTS status TEXT DEFAULT 'OK';

-- Add index for status filtering (from optimizations.sql)
CREATE INDEX IF NOT EXISTS idx_telemetry_legacy_status
ON telemetry_legacy(status);

-- Add index for operational filtering (from optimizations.sql)
CREATE INDEX IF NOT EXISTS idx_telemetry_legacy_operational
ON telemetry_legacy(operational);

-- Update existing records to have default values
UPDATE telemetry_legacy
SET operational = TRUE, status = 'OK'
WHERE operational IS NULL OR status IS NULL;

-- Add comments
COMMENT ON COLUMN telemetry_legacy.operational IS 'Boolean flag indicating if the system is operational';
COMMENT ON COLUMN telemetry_legacy.status IS 'Status indicator: OK, WARNING, ERROR, OFFLINE';

-- Verify schema
SELECT column_name, data_type, column_default
FROM information_schema.columns
WHERE table_name = 'telemetry_legacy'
ORDER BY ordinal_position;
