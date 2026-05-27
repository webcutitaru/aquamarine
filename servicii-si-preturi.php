<?php

declare(strict_types=1);

require __DIR__ . '/includes/init.php';

$s = lang_data('servicii');
$navCurrent = 'servicii-preturi';
$pageTitle = (string) $s['meta']['title'];
$pageDescription = (string) $s['meta']['description'];

$catalog = pricing_fetch_catalog(aquamarine_pdo());
$note = $catalog['note'];
$currency = (string) ($catalog['currency_display'] ?? $catalog['currency']);
$categories = $catalog['categories'];

require __DIR__ . '/includes/header.php';
?>

<main id="continut">
    <div class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:py-14">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) $s['eyebrow']) ?></p>
            <h1 class="font-display mt-2 text-pretty text-4xl font-bold tracking-tight text-ink lg:text-5xl"><?= esc((string) $s['h1']) ?></h1>
            <?php if ($note !== '') { ?>
                <p class="mt-5 max-w-3xl text-base leading-relaxed text-slate-600"><?= esc($note) ?></p>
            <?php } ?>
        </div>
    </div>

    <section
        class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:py-14"
        aria-labelledby="catalog-heading"
        id="catalog-root"
        data-search-no-results="<?= esc((string) $s['search_no_results']) ?>"
        data-search-results-one="<?= esc((string) $s['search_results_one']) ?>"
        data-search-results-many="<?= esc((string) $s['search_results_many']) ?>"
    >
        <h2 id="catalog-heading" class="sr-only"><?= esc((string) $s['panels_sr']) ?></h2>

        <?php if ($categories === []) { ?>
            <p class="text-center text-slate-600"><?= esc((string) $s['empty']) ?></p>
        <?php } else { ?>
            <div class="flex flex-col gap-10 lg:flex-row lg:items-start lg:gap-12">
                <aside class="shrink-0 lg:w-[min(22rem,34%)]">
                    <label for="catalog-search" class="block text-xs font-semibold uppercase tracking-wider text-slate-500"><?= esc((string) $s['search_label']) ?></label>
                    <input
                        id="catalog-search"
                        type="search"
                        name="q"
                        autocomplete="off"
                        placeholder="<?= esc((string) $s['search_placeholder']) ?>"
                        class="mt-2 w-full border-0 border-b border-slate-300 bg-transparent px-0 py-2 text-sm text-ink placeholder:text-slate-400 focus:border-brand-600 focus:outline-none focus:ring-0"
                        aria-describedby="catalog-search-hint catalog-search-status"
                    >
                    <p id="catalog-search-hint" class="mt-1 text-xs text-slate-400"><?= esc((string) $s['search_hint']) ?></p>
                    <p id="catalog-search-status" class="mt-2 hidden text-xs font-medium text-brand-800" aria-live="polite"></p>

                    <nav class="mt-8 flex flex-col gap-1" aria-label="<?= esc((string) $s['categories_aria']) ?>" id="catalog-tabs" role="tablist" aria-orientation="vertical">
                        <?php
                        $ci = 0;
                        foreach ($categories as $cat) {
                            if (! is_array($cat)) {
                                continue;
                            }
                            $name = isset($cat['name']) ? (string) $cat['name'] : (string) $s['category_fallback'];
                            $isFirst = $ci === 0;
                            ?>
                            <button
                                type="button"
                                class="catalog-tab text-left text-[15px] font-semibold uppercase leading-snug tracking-wide text-brand-700 transition-colors hover:text-brand-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-50 md:text-base <?= $isFirst ? 'catalog-tab-active text-slate-800' : 'text-brand-600/90' ?>"
                                id="catalog-tab-<?= esc((string) $ci) ?>"
                                role="tab"
                                aria-selected="<?= $isFirst ? 'true' : 'false' ?>"
                                aria-controls="catalog-panel-<?= esc((string) $ci) ?>"
                                tabindex="<?= $isFirst ? '0' : '-1' ?>"
                                data-catalog-index="<?= esc((string) $ci) ?>"
                            ><?= esc($name) ?></button>
                            <?php
                            ++$ci;
                        }
                        ?>
                    </nav>
                </aside>

                <div class="min-w-0 flex-1 space-y-4" id="catalog-panels">
                    <?php
                    $pi = 0;
                    foreach ($categories as $cat) {
                        if (! is_array($cat)) {
                            continue;
                        }

                        $catName = isset($cat['name']) ? (string) $cat['name'] : (string) $s['category_fallback'];
                        $footnote = isset($cat['footnote']) ? (string) $cat['footnote'] : '';
                        $itemsList = isset($cat['items']) && is_array($cat['items']) ? $cat['items'] : [];
                        ?>
                        <div
                            class="catalog-panel space-y-3<?= $pi > 0 ? ' hidden' : '' ?>"
                            id="catalog-panel-<?= esc((string) $pi) ?>"
                            role="tabpanel"
                            aria-labelledby="catalog-tab-<?= esc((string) $pi) ?>"
                            data-catalog-panel="<?= esc((string) $pi) ?>"
                        >
                            <p class="font-display text-lg font-semibold text-ink lg:hidden"><?= esc($catName) ?></p>
                            <?php foreach ($itemsList as $row) {
                                if (! is_array($row)) {
                                    continue;
                                }

                                $service = isset($row['service']) ? (string) $row['service'] : '';
                                $price = isset($row['price']) ? (string) $row['price'] : '';
                                $description = isset($row['description']) ? (string) $row['description'] : '';
                                $itemNote = isset($row['note']) ? (string) $row['note'] : '';
                                if ($service === '') {
                                    continue;
                                }
                                $showCurrency = $price !== ''
                                    && preg_match('/\d/u', $price) === 1
                                    && str_contains($price, '%') === false
                                    && stripos($price, 'prețul curățării') === false
                                    && stripos($price, 'стоимости') === false;
                                $searchText = mb_strtolower(
                                    $catName . ' ' . $service . ' ' . $description . ' ' . $price . ' ' . $itemNote,
                                    'UTF-8'
                                );
                                ?>
                                <article
                                    class="catalog-item rounded-2xl border border-slate-200/80 bg-slate-50/80 px-5 py-4 shadow-sm sm:px-6 sm:py-5"
                                    data-search="<?= esc($searchText) ?>"
                                    data-category-name="<?= esc($catName) ?>"
                                >
                                    <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:gap-6">
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-sm font-bold text-ink sm:text-base"><?= esc($service) ?></h3>
                                            <?php if ($description !== '') { ?>
                                                <p class="mt-2 text-xs leading-relaxed text-slate-600 sm:text-sm"><?= esc($description) ?></p>
                                            <?php } ?>
                                            <?php if ($itemNote !== '') { ?>
                                                <p class="mt-2 text-xs font-medium text-red-600 sm:text-sm"><?= esc($itemNote) ?></p>
                                            <?php } ?>
                                        </div>
                                        <div class="shrink-0 sm:max-w-[min(100%,20rem)] sm:text-right">
                                            <p class="text-sm font-bold text-ink sm:text-base">
                                                <?= esc($price) ?><?php if ($showCurrency) { ?>
                                                    <span class="whitespace-nowrap font-bold text-brand-800"> <?= esc($currency) ?></span>
                                                <?php } ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            <?php } ?>
                            <?php if ($footnote !== '') { ?>
                                <p class="pt-2 text-xs italic text-slate-500"><?= esc($footnote) ?></p>
                            <?php } ?>
                        </div>
                        <?php
                        ++$pi;
                    }
                    ?>
                </div>

                <div class="hidden min-w-0 flex-1" id="catalog-search-results" hidden>
                    <p id="catalog-search-empty" class="hidden rounded-2xl border border-slate-200 bg-slate-50 px-5 py-8 text-center text-sm text-slate-600"></p>
                    <div id="catalog-search-list" class="space-y-8"></div>
                </div>
            </div>

            <script>
                (function () {
                    var root = document.getElementById('catalog-root');
                    var tabs = document.querySelectorAll('.catalog-tab');
                    var panelsWrap = document.getElementById('catalog-panels');
                    var tabsNav = document.getElementById('catalog-tabs');
                    var searchInput = document.getElementById('catalog-search');
                    var searchResults = document.getElementById('catalog-search-results');
                    var searchList = document.getElementById('catalog-search-list');
                    var searchEmpty = document.getElementById('catalog-search-empty');
                    var searchStatus = document.getElementById('catalog-search-status');
                    if (!root || !tabs.length || !searchInput) return;

                    var minLen = 2;
                    var activeIndex = '0';
                    var debounceTimer = null;
                    var msgNone = root.getAttribute('data-search-no-results') || '';
                    var msgOne = root.getAttribute('data-search-results-one') || '';
                    var msgMany = root.getAttribute('data-search-results-many') || '%d';

                    function normalize(value) {
                        return String(value || '').toLowerCase().trim();
                    }

                    function formatCount(n) {
                        if (n === 1) return msgOne;
                        return msgMany.replace('%d', String(n));
                    }

                    function activate(index) {
                        activeIndex = String(index);
                        var i, tab, panel;
                        for (i = 0; i < tabs.length; i++) {
                            tab = tabs[i];
                            panel = document.getElementById('catalog-panel-' + tab.getAttribute('data-catalog-index'));
                            var isOn = tab.getAttribute('data-catalog-index') === String(index);
                            tab.setAttribute('aria-selected', isOn ? 'true' : 'false');
                            tab.tabIndex = isOn ? 0 : -1;
                            tab.classList.toggle('catalog-tab-active', isOn);
                            tab.classList.toggle('text-slate-800', isOn);
                            tab.classList.toggle('text-brand-600/90', !isOn);
                            if (panel) {
                                if (isOn) {
                                    panel.removeAttribute('hidden');
                                    panel.classList.remove('hidden');
                                } else {
                                    panel.setAttribute('hidden', 'hidden');
                                    panel.classList.add('hidden');
                                }
                            }
                        }
                    }

                    function exitSearchMode() {
                        searchResults.classList.add('hidden');
                        searchResults.setAttribute('hidden', 'hidden');
                        panelsWrap.classList.remove('hidden');
                        panelsWrap.removeAttribute('hidden');
                        tabsNav.classList.remove('opacity-40', 'pointer-events-none');
                        searchStatus.classList.add('hidden');
                        searchEmpty.classList.add('hidden');
                        searchList.innerHTML = '';
                        activate(activeIndex);
                    }

                    function runSearch() {
                        var query = normalize(searchInput.value);
                        if (query.length < minLen) {
                            exitSearchMode();
                            return;
                        }

                        panelsWrap.classList.add('hidden');
                        panelsWrap.setAttribute('hidden', 'hidden');
                        searchResults.classList.remove('hidden');
                        searchResults.removeAttribute('hidden');
                        tabsNav.classList.add('opacity-40', 'pointer-events-none');
                        searchList.innerHTML = '';

                        var items = document.querySelectorAll('.catalog-item');
                        var groups = {};
                        var order = [];
                        var total = 0;

                        items.forEach(function (item) {
                            var hay = item.getAttribute('data-search') || '';
                            if (hay.indexOf(query) === -1) return;
                            var cat = item.getAttribute('data-category-name') || '';
                            if (!groups[cat]) {
                                groups[cat] = [];
                                order.push(cat);
                            }
                            groups[cat].push(item.cloneNode(true));
                            total++;
                        });

                        if (total === 0) {
                            searchEmpty.textContent = msgNone;
                            searchEmpty.classList.remove('hidden');
                            searchStatus.textContent = msgNone;
                            searchStatus.classList.remove('hidden');
                            return;
                        }

                        searchEmpty.classList.add('hidden');
                        searchStatus.textContent = formatCount(total);
                        searchStatus.classList.remove('hidden');

                        order.forEach(function (catName) {
                            var block = document.createElement('div');
                            block.className = 'space-y-3';
                            var heading = document.createElement('h3');
                            heading.className = 'font-display text-lg font-semibold text-ink';
                            heading.textContent = catName;
                            block.appendChild(heading);
                            groups[catName].forEach(function (clone) {
                                block.appendChild(clone);
                            });
                            searchList.appendChild(block);
                        });
                    }

                    tabs.forEach(function (tab) {
                        tab.addEventListener('click', function () {
                            if (normalize(searchInput.value).length >= minLen) {
                                searchInput.value = '';
                                exitSearchMode();
                            }
                            activate(tab.getAttribute('data-catalog-index'));
                        });
                        tab.addEventListener('keydown', function (e) {
                            var keys = { ArrowDown: 1, ArrowUp: -1, Home: -9999, End: 9999 };
                            if (!keys.hasOwnProperty(e.key)) return;
                            e.preventDefault();
                            var list = Array.prototype.slice.call(tabs);
                            var idx = list.indexOf(tab);
                            var next = idx;
                            if (e.key === 'Home') next = 0;
                            else if (e.key === 'End') next = list.length - 1;
                            else next = idx + keys[e.key];
                            if (next < 0) next = list.length - 1;
                            if (next >= list.length) next = 0;
                            list[next].focus();
                            if (normalize(searchInput.value).length >= minLen) {
                                searchInput.value = '';
                                exitSearchMode();
                            }
                            activate(list[next].getAttribute('data-catalog-index'));
                        });
                    });

                    searchInput.addEventListener('input', function () {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(runSearch, 150);
                    });

                    searchInput.addEventListener('search', function () {
                        if (searchInput.value === '') {
                            exitSearchMode();
                        }
                    });
                })();
            </script>
        <?php } ?>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php';
