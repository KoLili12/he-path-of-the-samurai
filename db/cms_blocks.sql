-- CMS Blocks table for dynamic content management
-- This table stores reusable content blocks that can be displayed on various pages

CREATE TABLE IF NOT EXISTS cms_blocks (
    id SERIAL PRIMARY KEY,
    slug VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255),
    content TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Create index for fast lookups by slug
CREATE INDEX IF NOT EXISTS idx_cms_blocks_slug ON cms_blocks(slug);
CREATE INDEX IF NOT EXISTS idx_cms_blocks_is_active ON cms_blocks(is_active);

-- Insert default CMS blocks
INSERT INTO cms_blocks (slug, title, content, is_active) VALUES
(
    'dashboard_experiment',
    'Dashboard Experiment Block',
    '<div class="alert alert-info">
        <h5>🚀 Добро пожаловать в Space Dashboard!</h5>
        <p>Здесь вы можете:</p>
        <ul>
            <li>📍 Отслеживать положение МКС в реальном времени</li>
            <li>🔭 Просматривать изображения телескопа James Webb</li>
            <li>📊 Анализировать данные NASA OSDR</li>
            <li>🌌 Следить за астрономическими событиями</li>
        </ul>
    </div>',
    TRUE
),
(
    'footer_info',
    'Footer Information',
    '<div class="text-center text-muted">
        <p><strong>Space Data Collector</strong> - Кассиопея © 2025</p>
        <p>Практическая работа №3 - Рефакторинг распределённой системы</p>
        <p>Технологии: Rust (Axum) • PHP (Laravel) • Python • PostgreSQL • Redis • Docker</p>
    </div>',
    TRUE
),
(
    'sidebar_widget',
    'Sidebar Widget',
    '<div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong>📌 Быстрые ссылки</strong>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                <li>🛰️ <a href="/dashboard">Dashboard</a></li>
                <li>🌍 <a href="/osdr">NASA OSDR</a></li>
                <li>🔭 <a href="/api/jwst/feed">JWST Gallery</a></li>
                <li>📡 <a href="/api/iss/last">ISS Tracker</a></li>
            </ul>
        </div>
    </div>',
    TRUE
),
(
    'welcome_message',
    'Welcome Message',
    '<div class="jumbotron bg-gradient p-4 rounded shadow-sm mb-4">
        <h1 class="display-4">🌌 Космические данные в реальном времени</h1>
        <p class="lead">Система сбора и визуализации данных из открытых космических API</p>
        <hr class="my-4">
        <p>Проект демонстрирует современные паттерны проектирования и best practices в разработке распределённых систем.</p>
    </div>',
    TRUE
)
ON CONFLICT (slug) DO NOTHING;

-- Add comments for documentation
COMMENT ON TABLE cms_blocks IS 'CMS content blocks for dynamic page content';
COMMENT ON COLUMN cms_blocks.slug IS 'Unique identifier for the block (used in code)';
COMMENT ON COLUMN cms_blocks.content IS 'HTML content of the block';
COMMENT ON COLUMN cms_blocks.is_active IS 'Whether the block is currently active';
