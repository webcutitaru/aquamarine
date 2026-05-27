-- Copy & i18n feedback Sergiu Bîzdîga (2026-05) — rulare pe DB existent

UPDATE homepage_offers SET
    heading = 'Curățătorie profesională Aquamarine|în Bălți, Edineț, Briceni și Drochia',
    heading_ru = 'Профессиональная химчистка Aquamarine|Бельцы, Единец, Бричень, Дрокия'
WHERE heading LIKE '%Aquamarine%' OR heading_ru LIKE '%Aquamarine%' OR heading = '' OR heading_ru = '';

UPDATE price_categories SET name = 'Draperii, plăpumi, pături, perne, jucării și genți'
WHERE name LIKE 'Draperii, plapume%';

UPDATE price_items SET description_ru = 'шт.' WHERE description_ru = 'unit.';
UPDATE price_items SET description_ru = 'кг' WHERE description_ru = 'kg';

UPDATE price_items SET service_ru = 'Брюки с кожаными вставками'
WHERE service_ru = 'Брюки комбинированные кожей';

UPDATE price_items SET service_ru = REPLACE(service_ru, 'балеро', 'болеро')
WHERE service_ru LIKE '%балеро%';

UPDATE price_items SET service_ru = 'Верхняя штора, до ...'
WHERE service_ru = 'Верхняя штора до';

UPDATE price_items SET service_ru = 'Верхняя штора, от ...'
WHERE service_ru = 'Верхняя штора от';
