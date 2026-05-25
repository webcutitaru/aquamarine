-- Per-slide overlay text for homepage offers carousel (run once on existing databases)
ALTER TABLE homepage_offers
    ADD COLUMN eyebrow VARCHAR(120) NOT NULL DEFAULT '' AFTER alt_ru,
    ADD COLUMN eyebrow_ru VARCHAR(120) NOT NULL DEFAULT '' AFTER eyebrow,
    ADD COLUMN heading VARCHAR(500) NOT NULL DEFAULT '' AFTER eyebrow_ru,
    ADD COLUMN heading_ru VARCHAR(500) NOT NULL DEFAULT '' AFTER heading,
    ADD COLUMN sub TEXT NULL AFTER heading_ru,
    ADD COLUMN sub_ru TEXT NULL AFTER sub;

-- Backfill existing rows (RO defaults; RU from lang pack)
UPDATE homepage_offers
SET
    eyebrow = 'Aquamarine',
    eyebrow_ru = 'Aquamarine',
    heading = 'Curățătorie profesională Aquamarine în Bălți, Edineț, Briceni și Drochia',
    heading_ru = 'Профессиональная химчистка Aquamarine в Бельцах, Единце, Бричене и Дрокии',
    sub = 'Campaniile și reducerile din sezon — confirmați detaliile în magazin sau pe WhatsApp.',
    sub_ru = 'Сезонные акции и скидки — уточняйте детали в пункте приёма или в WhatsApp.'
WHERE heading = '' OR heading IS NULL;
