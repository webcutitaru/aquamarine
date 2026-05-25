-- Bilingual pricing columns (run once on existing databases)
ALTER TABLE price_settings
    ADD COLUMN note_ru TEXT NULL AFTER note;

ALTER TABLE price_categories
    ADD COLUMN name_ru VARCHAR(255) NULL AFTER name,
    ADD COLUMN footnote_ru TEXT NULL AFTER footnote;

ALTER TABLE price_items
    ADD COLUMN service_ru VARCHAR(500) NULL AFTER service,
    ADD COLUMN description_ru TEXT NULL AFTER description,
    ADD COLUMN note_ru TEXT NULL AFTER note;

ALTER TABLE homepage_offers
    ADD COLUMN alt_ru VARCHAR(500) NOT NULL DEFAULT '' AFTER alt;
