-- Database Optimizations and Indexes
-- Applies performance improvements to the monolith database

-- ============================================================
-- ISS FETCH LOG - Add indexes for common queries
-- ============================================================

-- Index on fetched_at for time-based queries
CREATE INDEX IF NOT EXISTS idx_iss_fetch_log_fetched_at
ON iss_fetch_log (fetched_at DESC);

-- Index on created_at for sorting
CREATE INDEX IF NOT EXISTS idx_iss_fetch_log_created_at
ON iss_fetch_log (created_at DESC);

-- Composite index for trend queries
CREATE INDEX IF NOT EXISTS idx_iss_fetch_log_composite
ON iss_fetch_log (fetched_at DESC, id DESC);

-- ============================================================
-- OSDR ITEMS - Add indexes for search and filtering
-- ============================================================

-- Index on title for search (using LOWER for case-insensitive)
CREATE INDEX IF NOT EXISTS idx_osdr_items_title_lower
ON osdr_items (LOWER(title));

-- Index on osdr_id for unique lookups
CREATE INDEX IF NOT EXISTS idx_osdr_items_osdr_id
ON osdr_items (osdr_id);

-- Index on created_at for sorting
CREATE INDEX IF NOT EXISTS idx_osdr_items_created_at
ON osdr_items (created_at DESC);

-- Full-text search index on title (if PostgreSQL supports it)
CREATE INDEX IF NOT EXISTS idx_osdr_items_title_gin
ON osdr_items USING gin(to_tsvector('english', title));

-- ============================================================
-- TELEMETRY LEGACY - Add indexes for analytics
-- ============================================================

-- Index on recorded_at for time-series queries
CREATE INDEX IF NOT EXISTS idx_telemetry_legacy_recorded_at
ON telemetry_legacy (recorded_at DESC);

-- Index on status for filtering
CREATE INDEX IF NOT EXISTS idx_telemetry_legacy_status
ON telemetry_legacy (status);

-- Index on operational for filtering
CREATE INDEX IF NOT EXISTS idx_telemetry_legacy_operational
ON telemetry_legacy (operational);

-- Composite index for common filter combinations
CREATE INDEX IF NOT EXISTS idx_telemetry_legacy_composite
ON telemetry_legacy (recorded_at DESC, status, operational);

-- ============================================================
-- CACHE TABLES - Add indexes if they exist
-- ============================================================

-- Generic cache table indexes (assuming standard structure)
-- Note: Adjust table names based on actual cache table structure

-- CREATE INDEX IF NOT EXISTS idx_cache_key ON cache (key);
-- CREATE INDEX IF NOT EXISTS idx_cache_expiration ON cache (expiration);

-- ============================================================
-- VACUUM AND ANALYZE - Optimize query planner
-- ============================================================

-- Vacuum all tables to reclaim space and update statistics
VACUUM ANALYZE iss_fetch_log;
VACUUM ANALYZE osdr_items;
VACUUM ANALYZE telemetry_legacy;

-- ============================================================
-- STATISTICS - Ensure accurate query planning
-- ============================================================

-- Update statistics for better query planning
ANALYZE iss_fetch_log;
ANALYZE osdr_items;
ANALYZE telemetry_legacy;

-- ============================================================
-- COMMENTS - Document schema
-- ============================================================

COMMENT ON INDEX idx_iss_fetch_log_fetched_at IS 'Index for time-based ISS position queries';
COMMENT ON INDEX idx_osdr_items_title_lower IS 'Case-insensitive search index for OSDR titles';
COMMENT ON INDEX idx_telemetry_legacy_recorded_at IS 'Time-series index for telemetry analytics';

-- ============================================================
-- PERFORMANCE TIPS
-- ============================================================

-- 1. Use EXPLAIN ANALYZE to verify index usage:
--    EXPLAIN ANALYZE SELECT * FROM iss_fetch_log ORDER BY fetched_at DESC LIMIT 100;

-- 2. Monitor index usage with pg_stat_user_indexes:
--    SELECT * FROM pg_stat_user_indexes WHERE schemaname = 'public';

-- 3. Check for unused indexes:
--    SELECT * FROM pg_stat_user_indexes WHERE idx_scan = 0;

-- 4. Monitor table bloat and run VACUUM regularly

-- 5. Consider partitioning large tables by date if they grow significantly
