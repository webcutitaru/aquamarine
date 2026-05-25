-- Aquamarine minidashboard schema (MySQL 8+ / MariaDB 10.3+)
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS admin_user (
    id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_admin_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS price_settings (
    id TINYINT UNSIGNED NOT NULL PRIMARY KEY DEFAULT 1,
    note TEXT NULL,
    note_ru TEXT NULL,
    currency VARCHAR(8) NOT NULL DEFAULT 'MDL',
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_price_settings_singleton CHECK (id = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO price_settings (id, note, currency) VALUES (1, NULL, 'MDL');

CREATE TABLE IF NOT EXISTS price_categories (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    name_ru VARCHAR(255) NULL,
    footnote TEXT NULL,
    footnote_ru TEXT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS price_items (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    service VARCHAR(500) NOT NULL,
    service_ru VARCHAR(500) NULL,
    price VARCHAR(64) NOT NULL,
    description TEXT NULL,
    description_ru TEXT NULL,
    note TEXT NULL,
    note_ru TEXT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_price_items_category FOREIGN KEY (category_id) REFERENCES price_categories (id) ON DELETE CASCADE,
    KEY idx_price_items_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS homepage_offers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(512) NOT NULL,
    alt VARCHAR(500) NOT NULL DEFAULT '',
    alt_ru VARCHAR(500) NOT NULL DEFAULT '',
    eyebrow VARCHAR(120) NOT NULL DEFAULT '',
    eyebrow_ru VARCHAR(120) NOT NULL DEFAULT '',
    heading VARCHAR(500) NOT NULL DEFAULT '',
    heading_ru VARCHAR(500) NOT NULL DEFAULT '',
    sub TEXT NULL,
    sub_ru TEXT NULL,
    href VARCHAR(512) NOT NULL DEFAULT '',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_homepage_offers_active_sort (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leads (
    id CHAR(36) NOT NULL PRIMARY KEY,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    name VARCHAR(190) NOT NULL,
    phone VARCHAR(64) NOT NULL,
    email VARCHAR(190) NULL,
    service_interest VARCHAR(255) NULL,
    preferred_mag VARCHAR(64) NULL,
    message TEXT NULL,
    attachments_json JSON NULL,
    ip_hashed CHAR(64) NULL,
    lang VARCHAR(8) NOT NULL DEFAULT 'ro',
    status ENUM('new', 'contacted', 'closed', 'spam') NOT NULL DEFAULT 'new',
    admin_notes TEXT NULL,
    contacted_at DATETIME NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    legacy_ts VARCHAR(64) NULL COMMENT 'ISO timestamp from NDJSON import',
    KEY idx_leads_status (status),
    KEY idx_leads_created (created_at),
    KEY idx_leads_phone (phone),
    KEY idx_leads_legacy (legacy_ts, phone, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
