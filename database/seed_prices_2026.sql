-- Aquamarine catalog prețuri 2026 (bilingv RO/RU)
-- Sursă: PRETURI_2026_Aquamarine_Bilingv_RO_RU_v2_citibil.pdf
-- Import: phpMyAdmin (tab Import) sau php database/seed_prices_2026.php
--
-- NOTĂ phpMyAdmin: mesajul „MySQL returned an empty result set” la SET/DELETE/UPDATE/INSERT
-- este NORMAL (comenzile nu returnează rânduri). La final trebuie să vedeți 16 categorii, 222 servicii.
-- Înainte de import: rulați database/migrate_i18n_pricing.sql dacă tabelele nu au coloanele _ru.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM price_items;
DELETE FROM price_categories;

UPDATE price_settings SET note = 'Prețurile sunt indicate în lei moldovenești. Pentru articole complexe, materiale combinate, pete dificile, curățare urgentă și prelucrări suplimentare, costul final se confirmă la recepție după evaluarea articolului. Costul poate varia în funcție de mărime, compoziția materialului, gradul de murdărire, decor, furnitură, piele, blană și materiale combinate. Serviciile urgente se execută doar dacă există posibilitate tehnică și după confirmarea tehnologului.', note_ru = 'Цены указаны в молдавских леях. Для сложных изделий, комбинированных материалов, сильных загрязнений, срочной чистки и дополнительной обработки окончательная стоимость подтверждается после осмотра изделия. Стоимость может корректироваться в зависимости от размера, состава материала, степени загрязнения, декора, фурнитуры, кожи, меха и комбинированных материалов. Срочные заказы выполняются при наличии технической возможности и только после подтверждения технологом.', currency = 'MDL' WHERE id = 1;

INSERT INTO price_categories (name, name_ru, footnote, footnote_ru, sort_order) VALUES
  ('Cămăși, bluze, tricouri și tricotaje', 'Блузы, рубашки, футболки и трикотаж', NULL, NULL, 0),
  ('Pantaloni, blugi și șorturi', 'Брюки, джинсы и шорты', NULL, NULL, 1),
  ('Costume, sacouri, veste și combinezoane', 'Костюмы, пиджаки, жилеты и комбинезоны', NULL, NULL, 2),
  ('Îmbrăcăminte exterioară', 'Верхняя одежда', NULL, NULL, 3),
  ('Rochii, fuste și articole de ocazie', 'Платья, юбки и праздничные изделия', NULL, NULL, 4),
  ('Accesorii și articole speciale', 'Аксессуары и специальные изделия', NULL, NULL, 5),
  ('Draperii, plapume, pături, perne, jucării și genți', 'Шторы, одеяла, пледы, подушки, игрушки и сумки', NULL, NULL, 6),
  ('Articole din blană naturală', 'Изделия из натурального меха', NULL, NULL, 7),
  ('Articole din blană artificială', 'Изделия из искусственного меха', NULL, NULL, 8),
  ('Aqua-curățare individuală și textile pentru casă', 'Индивидуальная аквачистка и домашний текстиль', NULL, NULL, 9),
  ('Curățare articole din piele întoarsă, nubuc și crac', 'Замша, нубук, крэк — чистка', NULL, NULL, 10),
  ('Curățare și vopsire articole din piele netedă', 'Гладкая кожа — чистка и покраска', NULL, NULL, 11),
  ('Curățare și vopsire articole din piele întoarsă, nubuc și crac', 'Замша, нубук, крэк — чистка и покраска', NULL, NULL, 12),
  ('Cojoace, piei de oaie și piei decorative', 'Дублёнки, овчина и декоративные шкуры', NULL, NULL, 13),
  ('Curățare încălțăminte', 'Чистка обуви', NULL, NULL, 14),
  ('Călcare, reduceri, adaosuri și servicii urgente', 'Глажка, скидки, надбавки и срочные услуги', 'Adaosurile procentuale se calculează din prețul curățării articolului. Serviciile urgente — doar după confirmarea tehnologului.', 'Процентные надбавки рассчитываются от стоимости чистки изделия. Срочные заказы — только после подтверждения технологом.', 15);

INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Bluză simplă / golf / pulover / bolero, mânecă scurtă', 'Блуза простая, гольф, свитер, балеро с коротким рукавом', '130', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Bluză simplă / golf / pulover / bolero, mânecă lungă', 'Блуза простая, гольф, свитер, балеро с длинным рукавом', '150', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Bluză complexă (furnitură, strasuri, aplicații)', 'Блуза сложная (фурнитура, стразы, аппликация)', '210', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cămașă clasică (bărbați / femei)', 'Рубашка классическая (мужская, женская)', '90', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cămașă clasică cu mânecă scurtă (bărbați / femei)', 'Рубашка классическая с коротким рукавом (мужская, женская)', '70', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cămașă din mătase (bărbați / femei)', 'Рубашка шелковая (мужская, женская)', '145', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Bluză tricotată / pulover', 'Кофта вязаная, свитер', '185', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Bluză tricotată cu mânecă scurtă', 'Кофта (вязаная, трикотажная с коротким рукавом)', '140', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pulover / raglan / golf voluminos', 'Пуловер, реглан, свитер объемный, гольф объемный', '260', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă / jachetă tricotată', 'Куртка вязаная', '210', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Jachetă tricotată cu căptușeală', 'Жакет вязаный с подкладкой', '210', 'unit.', 'unit.', NULL, NULL, 10
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cardigan', 'Кардиган', '220', 'unit.', 'unit.', NULL, NULL, 11
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Top', 'Топ', '110', 'unit.', 'unit.', NULL, NULL, 12
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Tunică simplă / complexă', 'Туника простая / сложная', '160 / 260', 'unit.', 'unit.', NULL, NULL, 13
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Tricou', 'Футболка', '95', 'unit.', 'unit.', NULL, NULL, 14
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Tricou cu mânecă lungă și detalii', 'Футболка с длинным рукавом и отделкой', '125', 'unit.', 'unit.', NULL, NULL, 15
FROM price_categories c WHERE c.sort_order = 0;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pantaloni', 'Брюки', '150', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 1;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pantaloni trei-sferturi / bridji', 'Бриджи', '100', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 1;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pantaloni din mătase / model complex', 'Брюки шёлковые, сложные', '210', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 1;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pantaloni combinați cu piele', 'Брюки комбинированные кожей', '295', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 1;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pantaloni căptușiți (sintepon)', 'Брюки утепленные (на синтепоне)', '200', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 1;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Blugi simpli', 'Джинсы', '150', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 1;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Blugi complicați (aplicații, strasuri)', 'Джинсы сложные (аппликации, стразы)', '220', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 1;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pantaloni costum sportiv', 'Костюм спортивный (брюки)', '145', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 1;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Șorturi simple', 'Шорты', '110', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 1;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Șorturi complexe (mătase, aplicații, strasuri)', 'Шорты сложные (шёлк, аппликации, стразы)', '145', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 1;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Vestă simplă', 'Жилет', '105', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 2;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Vestă puf-pene', 'Жилет пух-перо', '290', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 2;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Vestă cu blană naturală / piele', 'Жилет отороченный натуральным мехом/кожей', '310', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 2;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Vestă căptușită (sintepon)', 'Жилет утепленный (на синтепоне)', '230', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 2;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă costum sportiv', 'Костюм спортивный (куртка)', '145', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 2;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sacou', 'Пиджак', '190', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 2;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sacou căptușit', 'Пиджак утепленный', '250', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 2;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sacou complex (mătase, strasuri, inserții din piele)', 'Пиджак сложный (шёлк, стразы, кожаные вставки)', '275', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 2;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Combinezon simplu', 'Комбинезон', '245', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 2;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Combinezon cu mâneci, căptușit (sintepon)', 'Комбинезон (с рукавами) утепленный (на синтепоне)', '390', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 2;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Corset simplu / complex', 'Корсет простой / сложный', '180 / 350', 'unit.', 'unit.', NULL, NULL, 10
FROM price_categories c WHERE c.sort_order = 2;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă demi-sezon, până la 70 cm', 'Куртка демисезонная (до 70 см)', '250', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă demi-sezon, peste 70 cm', 'Куртка демисезонная (от 70 см)', '280', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă de iarnă, până la 70 cm', 'Куртка зимняя (до 70 см)', '330', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă de iarnă cu guler de blană, până la 70 cm', 'Куртка зимняя с меховым воротником (до 70 см)', '370', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă de iarnă, peste 70 cm', 'Куртка зимняя (от 70 см)', '360', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă de iarnă cu guler de blană, peste 70 cm', 'Куртка зимняя с меховым воротником (от 70 см)', '410', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă de iarnă fără mâneci', 'Куртка зимняя без рукавов', '250', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă vânt / windbreaker', 'Куртка-ветровка', '240', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă din denim', 'Куртка джинсовая', '190', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă căptușită cu căptușeală detașabilă', 'Куртка утеплённая (со съёмной подкладкой)', '350', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Palton tricotat', 'Пальто вязаное', '285', 'unit.', 'unit.', NULL, NULL, 10
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Palton demi-sezon', 'Пальто демисезонное', '340', 'unit.', 'unit.', NULL, NULL, 11
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Semi-palton demi-sezon', 'Полупальто демисезонное', '315', 'unit.', 'unit.', NULL, NULL, 12
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Palton de iarnă', 'Пальто зимнее', '390', 'unit.', 'unit.', NULL, NULL, 13
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Semi-palton de iarnă', 'Полупальто зимнее', '350', 'unit.', 'unit.', NULL, NULL, 14
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pardesiu / trenci', 'Плащ', '310', 'unit.', 'unit.', NULL, NULL, 15
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pardesiu / trenci căptușit', 'Плащ утеплённый', '360', 'unit.', 'unit.', NULL, NULL, 16
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Căptușeală detașabilă textilă', 'Подстёжка тканевая', '135', 'unit.', 'unit.', NULL, NULL, 17
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Căptușeală detașabilă din blană artificială', 'Подстёжка из искусственного меха', '310', 'unit.', 'unit.', NULL, NULL, 18
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Căptușeală detașabilă din blană naturală', 'Подстёжка из натурального меха', '375', 'unit.', 'unit.', NULL, NULL, 19
FROM price_categories c WHERE c.sort_order = 3;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie simplă', 'Платье простое', '195', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie complexă', 'Платье сложное', '285', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie / sarafan din mătase', 'Платье, сарафан шелковые', '310', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie de seară simplă', 'Платье вечернее', '340', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie tricotată / din lână', 'Платье вязаное, шерстяное', '220', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie de seară complexă', 'Платье вечернее (сложное)', '470', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie de mireasă simplă', 'Платье свадебное простое', '740', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie de mireasă complexă', 'Платье свадебное сложное', '980', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Voal / fustă din tul', 'Фата, фатиновая юбка', '210', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fustă simplă', 'Юбка простая', '110', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fustă complexă', 'Юбка сложная', '180', 'unit.', 'unit.', NULL, NULL, 10
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fustă națională', 'Юбка национальная', '240', 'unit.', 'unit.', NULL, NULL, 11
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fustă model complex (plisată, gofrată, pliuri etc.)', 'Юбка сложной модели (плиссе, гофре, складки и т.д.)', '210', 'unit.', 'unit.', NULL, NULL, 12
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fustă tricotată / din lână / din mătase', 'Юбка вязаная, шерстяная, шелковая', '180', 'unit.', 'unit.', NULL, NULL, 13
FROM price_categories c WHERE c.sort_order = 4;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cravată', 'Галстук', '85', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Articol pentru acoperirea capului', 'Головной убор', '165', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Eșarfă / fular / glugă / mănuși din lână / cozoroc / guler cu blană artificială', 'Платок, шарф, капюшон, перчатки шерстяные, козырек, воротник с искусственным мехом', '110', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Guler simplu', 'Воротник простой', '105', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Basma / șal din puf', 'Платок пуховый', '110', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fular', 'Шарф', '150', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Șal / batic / palantină / pașmină (cașmir, mătase)', 'Шаль, косынка, палантин, пашмина (кашемир, шёлк)', '150', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pijama din mătase (set)', 'Пижама шелковая (комплект)', '220', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Halat frotir / velur / mătase', 'Халат махровый, велюровый, шелковый', '180', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Șorț (pentru murdărie puternică se aplică +50%)', 'Фартук (надбавка за сильное загрязнение +50%)', '90', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sutană', 'Ряса', '325', 'unit.', 'unit.', NULL, NULL, 10
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sutană complexă', 'Ряса сложная', '495', 'unit.', 'unit.', NULL, NULL, 11
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Halat medical', 'Медицинский халат', '99', 'unit.', 'unit.', NULL, NULL, 12
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pantaloni medicali', 'Медицинские брюки', '65', 'unit.', 'unit.', NULL, NULL, 13
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cămașă medicală', 'Медицинская рубашка', '75', 'unit.', 'unit.', NULL, NULL, 14
FROM price_categories c WHERE c.sort_order = 5;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Jucărie moale, până la 1 kg', 'Игрушка мягкая, до 1 кг', '120', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Jucărie moale, peste 1 kg', 'Игрушка мягкая, более 1 кг', '120', 'kg', 'kg', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Plapumă sintepon, 1 persoană', 'Одеяло синтепоновое полуторное', '305', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Plapumă sintepon, 2 persoane', 'Одеяло синтепоновое двуспальное', '355', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Plapumă cu puf, 1 persoană', 'Одеяло пуховое полуторное', '390', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Plapumă cu puf, 2 persoane', 'Одеяло пуховое двуспальное', '435', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Plapumă lână / semilână / vată, 1 persoană', 'Одеяло шерстяное, полушерстяное, ватное полуторное', '380', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Plapumă lână / semilână / vată, 2 persoane', 'Одеяло шерстяное, полушерстяное, ватное двуспальное', '440', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pled din lână, 1 persoană', 'Плед полуторный (шерсть)', '320', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pled din lână, 2 persoane', 'Плед двуспальный (шерсть)', '395', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pled din blană artificială, 1 persoană', 'Плед из искусственного меха полуторный', '250', 'unit.', 'unit.', NULL, NULL, 10
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pled din blană artificială, 2 persoane', 'Плед из искусственного меха двуспальный', '290', 'unit.', 'unit.', NULL, NULL, 11
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pled dublu din blană artificială, 1 persoană', 'Плед двойной из искусственного меха полуторный', '330', 'unit.', 'unit.', NULL, NULL, 12
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pled dublu din blană artificială, 2 persoane', 'Плед двойной из искусственного меха двуспальный', '380', 'unit.', 'unit.', NULL, NULL, 13
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pernă sintepon', 'Подушка синтепоновая', '130', 'unit.', 'unit.', NULL, NULL, 14
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pernă din lână', 'Подушка шерстяная', '160', 'unit.', 'unit.', NULL, NULL, 15
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pernă pentru gravide', 'Подушка для беременных', '190', 'unit.', 'unit.', NULL, NULL, 16
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sac de dormit cu puf', 'Спальный мешок пуховой', '385', 'unit.', 'unit.', NULL, NULL, 17
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sac de dormit sintepon', 'Спальный мешок синтепон', '240', 'unit.', 'unit.', NULL, NULL, 18
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Plic / sac pentru nou-născuți', 'Детский конверт для новорожденных', '225', 'unit.', 'unit.', NULL, NULL, 19
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geantă / rucsac', 'Сумка, рюкзак', '170', 'unit.', 'unit.', NULL, NULL, 20
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geantă de călătorie', 'Сумка дорожная', '250', 'unit.', 'unit.', NULL, NULL, 21
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Perdele subțiri / tul', 'Шторы тонкие, тюль', '30', 'm²', 'm²', NULL, NULL, 22
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Draperii dense / portiere', 'Шторы плотные, портьеры', '40', 'm²', 'm²', NULL, NULL, 23
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Draperie superioară, până la ...', 'Верхняя штора до', '200', 'unit.', 'unit.', NULL, NULL, 24
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Draperie superioară, peste ...', 'Верхняя штора от', '250', 'unit.', 'unit.', NULL, NULL, 25
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Legătoare pentru draperii', 'Завязка для штор', '45', 'unit.', 'unit.', NULL, NULL, 26
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cuvertură, 1 persoană', 'Покрывало полуторное', '210', 'unit.', 'unit.', NULL, NULL, 27
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cuvertură, 2 persoane', 'Покрывало двуспальное', '280', 'unit.', 'unit.', NULL, NULL, 28
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Valiză / geamantan', 'Чемодан', '295', 'unit.', 'unit.', NULL, NULL, 29
FROM price_categories c WHERE c.sort_order = 6;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Guler din blană naturală cat. I (nurcă, vulpe polară, vulpe argintie etc.)', 'Воротник из натурального меха (норка, песец, чернобурка и т.п.)', '180', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 7;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Guler din blană naturală cat. II (iepure, ondatră, nutrie, ied etc.)', 'Воротник из натурального меха (кролик, ондатра, нутрия, козлик и т.п.)', '150', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 7;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Vestă / bolero din blană naturală', 'Жилетка, балеро из натурального меха', '360', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 7;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Vestă lungă din blană naturală', 'Жилет меховой, длинный', '440', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 7;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Haină scurtă din blană naturală cat. I, până la 70 cm', 'Полушубок из натурального меха (норка, песец, чернобурка и т.п.) (до 70 см)', '750', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 7;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Haină scurtă din blană naturală cat. II, până la 70 cm', 'Полушубок из натурального меха (кролик, ондатра, нутрия, козлик и т.п.) (до 70 см)', '570', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 7;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Căciulă / pălărie din blană naturală cat. I', 'Шапка / шляпа из натурального меха (норка, песец, чёрнобурка и т.п.)', '245', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 7;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Căciulă din blană naturală cat. II', 'Шапка из натурального меха (кролик, ондатра, нутрия, козлик и т.п.)', '170', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 7;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Haină de blană naturală cat. I, peste 70 cm', 'Шуба из натурального меха (норка, песец, чернобурка и т.п.) (от 70 см)', '895', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 7;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Haină de blană naturală cat. II, peste 70 cm', 'Шуба из натурального меха (кролик, ондатра, нутрия, козлик и т.п.) (от 70 см)', '675', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 7;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cojoc / haină pe blană artificială, scurtă, până la 70 cm', 'Дубленка на искусственном меху короткая (до 70 см)', '380', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 8;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cojoc / haină pe blană artificială, lungă, peste 70 cm', 'Дубленка на искусственном меху длинная (от 70 см)', '420', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 8;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Vestă / bolero din blană artificială', 'Жилетка, балеро из искусственного меха', '320', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 8;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Haină scurtă din blană artificială, până la 70 cm', 'Полушубок из искусственного меха до 70 см', '360', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 8;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Căciulă din blană artificială / chipiu / beretă', 'Шапка из искусственного меха, кепка, берет', '140', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 8;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Haină din blană artificială, peste 70 cm', 'Шуба из искусственного меха от 70 см', '420', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 8;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Prosoape până la 50 cm / peste 50 cm', 'Полотенце до 50 см / от 50 см', '45 / 110', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Lenjerie de pat, inclusiv călcare (fără apret)', 'Постельное белье, включая глажку (без крахмала)', '65', 'kg', 'kg', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Lenjerie intimă (maiou, chiloți, șosete)', 'Нижнее белье (майка, трусы, носки)', '55', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cearșaf frotir, 1 persoană', 'Простынь махровая, полуторная', '80', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cearșaf frotir, 2 persoane', 'Простынь махровая, двухспальная', '99', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pijama / peignoir / cămașă de noapte', 'Пижама, пеньюар, ночная рубашка', '98', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Față de masă simplă, până la 1,5 m²', 'Скатерти простые до 1,5 м²', '90', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Față de masă simplă, peste 1,5 m²', 'Скатерти простые от 1,5 м²', '115', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Față de masă complexă (broderie manuală)', 'Скатерти сложные (ручная вышивка)', '165', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Husă mobilă până la 70 cm / peste 70 cm', 'Мебельные чехлы до 70 см / от 70 см', '115 / 255', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Husă scaun (șezut + spătar)', 'Чехлы для стульев (сидушка + спинка)', '110', 'unit.', 'unit.', NULL, NULL, 10
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Husă scaun (șezut)', 'Чехлы для стульев (сидушка)', '60', 'unit.', 'unit.', NULL, NULL, 11
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Covoraș mic pentru baie', 'Коврики маленькие (для ванной)', '115', 'unit.', 'unit.', NULL, NULL, 12
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Covoraș mare pentru baie', 'Коврики большие (для ванной)', '215', 'unit.', 'unit.', NULL, NULL, 13
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Chipiu / șapcă', 'Кепка', '95', 'unit.', 'unit.', NULL, NULL, 14
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Costum carnaval', 'Карнавальный костюм', '395', 'unit.', 'unit.', NULL, NULL, 15
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Prosop național', 'Полотенце национальное', '205', 'unit.', 'unit.', NULL, NULL, 16
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cărucior / scaun auto pentru copii', 'Коляска / автокресло', '350 / 450', 'unit.', 'unit.', NULL, NULL, 17
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Jaluzele demontate / montate', 'Жалюзи разобранные/собранные', '40 / 60', 'm²', 'm²', NULL, NULL, 18
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cort până la 3 m / peste 3 m', 'Палатка до 3 м / от 3 м', '350 / 550', 'unit.', 'unit.', NULL, NULL, 19
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Saltea pentru leagăn', 'Матрас для качели', '480', 'unit.', 'unit.', NULL, NULL, 20
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Umbrelă de terasă', 'Зонт для террасы', '300', 'unit.', 'unit.', NULL, NULL, 21
FROM price_categories c WHERE c.sort_order = 9;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cămașă', 'Рубашка', '520', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fustă peste 50 cm', 'Юбка (от 50 см)', '490', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fustă până la 50 cm', 'Юбка (до 50 см)', '360', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pardesiu / trenci', 'Плащ', '1100', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pantaloni', 'Брюки', '490', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie peste 90 cm', 'Платье (от 90 см)', '890', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie până la 90 cm', 'Платье (до 90 см)', '690', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sarafan peste 90 cm', 'Сарафан (от 90 см)', '690', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sarafan până la 90 cm', 'Сарафан (до 90 см)', '580', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă peste 70 cm', 'Куртка (от 70 см)', '820', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă până la 70 cm', 'Куртка (до 70 см)', '760', 'unit.', 'unit.', NULL, NULL, 10
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Șorturi', 'Шорты', '350', 'unit.', 'unit.', NULL, NULL, 11
FROM price_categories c WHERE c.sort_order = 10;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Curea / brâu', 'Ремень/Пояс', '295', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cămașă', 'Рубашка', '850', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fustă peste 50 cm', 'Юбка (от 50 см)', '850', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fustă până la 50 cm', 'Юбка (до 50 см)', '600', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pardesiu / trenci', 'Плащ', '1100', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pantaloni', 'Брюки', '900', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie peste 90 cm', 'Платье (от 90 см)', '1100', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie până la 90 cm', 'Платье (до 90 см)', '900', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sarafan peste 90 cm', 'Сарафан (от 90 см)', '1000', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sarafan până la 90 cm', 'Сарафан (до 90 см)', '800', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă peste 70 cm', 'Куртка (от 70 см)', '1100', 'unit.', 'unit.', NULL, NULL, 10
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă până la 70 cm', 'Куртка (до 70 см)', '850', 'unit.', 'unit.', NULL, NULL, 11
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Șorturi', 'Шорты', '550', 'unit.', 'unit.', NULL, NULL, 12
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geantă damă - curățare', 'Сумка женская — чистка', '390', 'unit.', 'unit.', NULL, NULL, 13
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geantă damă - vopsire', 'Сумка женская — покраска', '590', 'unit.', 'unit.', NULL, NULL, 14
FROM price_categories c WHERE c.sort_order = 11;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cămașă', 'Рубашка', '690', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fustă peste 50 cm', 'Юбка (от 50 см)', '690', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Fustă până la 50 cm', 'Юбка (до 50 см)', '590', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pardesiu / trenci', 'Плащ', '1100', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pantaloni', 'Брюки', '900', 'unit.', 'unit.', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie peste 90 cm', 'Платье (от 90 см)', '890', 'unit.', 'unit.', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Rochie până la 90 cm', 'Платье (до 90 см)', '790', 'unit.', 'unit.', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sarafan peste 90 cm', 'Сарафан (от 90 см)', '800', 'unit.', 'unit.', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Sarafan până la 90 cm', 'Сарафан (до 90 см)', '750', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă peste 70 cm', 'Куртка (от 70 см)', '1100', 'unit.', 'unit.', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Geacă până la 70 cm', 'Куртка (до 70 см)', '850', 'unit.', 'unit.', NULL, NULL, 10
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Șorturi', 'Шорты', '450', 'unit.', 'unit.', NULL, NULL, 11
FROM price_categories c WHERE c.sort_order = 12;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cojoc - curățare, peste 70 cm', 'Дубленка (от 70 см)', '950', 'unit.', 'unit.', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 13;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cojoc - curățare, până la 70 cm', 'Дубленка (до 70 см)', '850', 'unit.', 'unit.', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 13;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cojoc - curățare și vopsire, peste 70 cm', 'Дубленка (от 70 см)', '1100', 'unit.', 'unit.', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 13;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cojoc - curățare și vopsire, până la 70 cm', 'Дубленка (до 70 см)', '990', 'unit.', 'unit.', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 13;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Curățare articole din blană de oaie / piele decorativă', 'Чистка изделий из меховой овчины / шкура декоративная', '520', 'm²', 'm²', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 13;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pantofi / adidași / mocasini', 'Туфли, кроссовки, мокасины', '320', 'pereche', 'pereche', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 14;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cizme scurte', 'Полусапожки', '390', 'pereche', 'pereche', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 14;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cizme', 'Сапоги', '420', 'pereche', 'pereche', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 14;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Bocanci', 'Ботинки', '380', 'pereche', 'pereche', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 14;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Cizme înalte / botforte', 'Ботфорды', '510', 'pereche', 'pereche', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 14;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Balerini / sandale', 'Балетки, босоножки, сандали', '290', 'pereche', 'pereche', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 14;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'UGG / încălțăminte tip UGG', 'Уги', '490', 'pereche', 'pereche', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 14;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Curățare încălțăminte din material textil', 'Чистка обуви из текстиля', '250', 'pereche', 'pereche', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 14;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Călcare articole', 'Глажка изделий', '60% din prețul curățării', 'serviciu', 'serviciu', NULL, NULL, 0
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Articole pentru copii până la 7 ani / 120 cm', 'Детские вещи (до 7 лет/120 см)', '70% din prețul curățării', 'serviciu', 'serviciu', NULL, NULL, 1
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Articole pentru copii până la 12 ani / 150 cm', 'Детские вещи (до 12 лет/150см)', '80% din prețul curățării', 'serviciu', 'serviciu', NULL, NULL, 2
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Articole foarte murdare', 'Особо загрязнённые изделия', '+30% la prețul curățării', 'adaos', 'adaos', NULL, NULL, 3
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Îndepărtare pilling / scame', 'Пиллинг (удаление катышков)', '+30% la prețul curățării', 'adaos', 'adaos', NULL, NULL, 4
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Pieptănarea fibrei la articole din blană artificială (pături, cuverturi etc.)', 'Прочёсывание ворса изделий из искусственного меха (одеяла, покрывала и т.п.)', '+25% la prețul curățării', 'adaos', 'adaos', NULL, NULL, 5
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Articole cu ornamente (broderie, mărgele, strasuri etc.) / demontare și montare furnitură complexă', 'Чистка изделий с оформлением (вышивка, бисер, стразы и т.п.), снятие и установка сложной фурнитуры', '+35% la prețul curățării', 'adaos', 'adaos', NULL, NULL, 6
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Articole cu combinații de culori', 'Изделия с комбинацией цветов', '+30% la prețul curățării', 'adaos', 'adaos', NULL, NULL, 7
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Scoatere expres a petelor (1 pată până la 5 cm²)', 'Экспресс-выведение пятен (1 пятно размером не более 5 см²)', '35', 'unit.', 'unit.', NULL, NULL, 8
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Curățare urgentă 2-4 ore lucrătoare (doar în atelier)', 'Срочная химчистка в течение 2-4-х рабочих часов (только в цеху)', '+100% la prețul curățării', 'adaos', 'adaos', NULL, NULL, 9
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Curățare urgentă 5-6 ore lucrătoare (doar în atelier)', 'Срочная химчистка в течение 5-6-ти рабочих часов (только в цеху)', '+70% la prețul curățării', 'adaos', 'adaos', NULL, NULL, 10
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Curățare urgentă 7-8 ore lucrătoare (doar în atelier)', 'Срочная химчистка в течение 7-8-ми рабочих часов (только в цеху)', '+40% la prețul curățării', 'adaos', 'adaos', NULL, NULL, 11
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Curățare urgentă pentru ziua următoare, ora 17:00', 'Срочная химчистка (на следующий день, в 17:00)', '+30% la prețul curățării', 'adaos', 'adaos', NULL, NULL, 12
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Reparație mică / unitate', 'Мелкий ремонт/единица', '55', 'unit.', 'unit.', NULL, NULL, 13
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Plată suplimentară (+40%)', 'Дополнительная плата (+40%)', '+40% la prețul curățării', 'adaos', 'adaos', NULL, NULL, 14
FROM price_categories c WHERE c.sort_order = 15;
INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
SELECT c.id, 'Plată suplimentară (+50%)', 'Дополнительная плата (+50%)', '+50% la prețul curățării', 'adaos', 'adaos', NULL, NULL, 15
FROM price_categories c WHERE c.sort_order = 15;

SET FOREIGN_KEY_CHECKS = 1;

-- Verificare import (trebuie: 16 categorii, 222 servicii)
SELECT 'categorii' AS tip, COUNT(*) AS total FROM price_categories
UNION ALL
SELECT 'servicii', COUNT(*) FROM price_items;
