<?php

declare(strict_types=1);

/** Program standard recepție (filiale fără override + JSON-LD fallback). */
$defaultBranchHoursSpec = [
    [
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        'opens' => '09:00',
        'closes' => '18:00',
    ],
    [
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => 'Saturday',
        'opens' => '10:00',
        'closes' => '15:00',
    ],
    [
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => 'Sunday',
        'opens' => '10:00',
        'closes' => '14:00',
    ],
];

return [
    'site_name' => 'Aquamarine',
    /** URL producție (canonical, schema, sitemap logic) */
    'production_base_url' => 'https://aquamarine.md',
    /**
     * Dacă HTTP_HOST conține unul din șiruri → meta robots noindex,nofollow și canonical spre producție.
     *
     * @var list<string>
     */
    'staging_host_contains' => ['cutitaru.com'],
    /**
     * Fișă Google (recenzii) — folosit peste tot pentru linkuri „Recenzii” / badge (locația Bălți).
     */
    'google_business_reviews_url' => 'https://maps.app.goo.gl/Cgmnrm54NSbrPpQcA',
    /** Medie afișată pe site (trebuie să coincidă cu Google Business). */
    'google_maps_rating' => 4.8,
    /** Număr total de recenzii pe Google (pentru bară și schema.org). */
    'google_maps_review_count' => 110,
    /** Telefon principal în header/footer — aliniat la prima filială (Bălți). */
    'phone_display' => '+373 (78) 831 555',
    'phone_e164' => '+37378831555',
    'whatsapp_digits' => '37378831555',
    /** Lăsați gol dacă nu folosiți email; nu se va afișa în footer fără valoare */
    'email_contact' => '',
    /** Adresă principală (SEO / schema.org) — prima locație */
    'address_full' => 'Bălți, str. Decebal 130/A (mag. Kaufland)',
    /**
     * Magazine: câmpuri comune maps_url, phone_*; opening_hours_spec doar dacă diferă de opening_hours_spec global.
     *
     * @var list<array<string, mixed>>
     */
    'locations' => [
        [
            'city' => 'Bălți',
            'address' => 'str. Decebal 130/A, mag. Kaufland',
            'page' => 'curatatorie-profesionala-haine-balti.php',
            'maps_url' => 'https://maps.app.goo.gl/Cgmnrm54NSbrPpQcA',
            'phone_display' => '+373 (78) 831 555',
            'phone_e164' => '+37378831555',
            'opening_hours_spec' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                    'opens' => '08:30',
                    'closes' => '20:30',
                ],
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => 'Saturday',
                    'opens' => '08:30',
                    'closes' => '19:30',
                ],
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => 'Sunday',
                    'opens' => '08:30',
                    'closes' => '19:30',
                ],
            ],
        ],
        [
            'city' => 'Edineț',
            'address' => 'str. Ștefan Vodă 6',
            'page' => 'curatatorie-profesionala-haine-edinet.php',
            'maps_url' => 'https://maps.app.goo.gl/EbEqmBpkM5Vvmzz8A',
            'phone_display' => '+373 (79) 581 555',
            'phone_e164' => '+37379581555',
        ],
        [
            'city' => 'Briceni',
            'address' => 'str. Independenței 33',
            'page' => 'curatatorie-profesionala-haine-briceni.php',
            'maps_url' => 'https://maps.app.goo.gl/hBVUpr3EAu8hxK1j6',
            'phone_display' => '+373 (78) 484 544',
            'phone_e164' => '+37378484544',
        ],
        [
            'city' => 'Drochia',
            'address' => 'str. 31 August 27/6',
            'page' => 'curatatorie-profesionala-haine-drochia.php',
            'maps_url' => 'https://maps.app.goo.gl/6AzGe2ZGe7KHACtS9',
            'phone_display' => '+373 (78) 784 404',
            'phone_e164' => '+37378784404',
        ],
    ],
    /**
     * Program recepție implicit (JSON-LD + filiale fără opening_hours_spec propriu).
     *
     * @var list<array<string, mixed>>
     */
    'opening_hours_spec' => $defaultBranchHoursSpec,
    'facebook_url' => 'https://www.facebook.com/curatatoriehaineedinet/',
    'instagram_url' => 'https://www.instagram.com/aquamarine__md/',
    'google_maps_url' => '',
    'mail_enabled' => false,
    'contact_recipient_email' => 'contact@aquamarine.md',
    /** Discount pentru angajații partenerilor B2B (afișat pe business.php). */
    'b2b_employee_discount_percent' => 20,
    /** Text livrare/colectare B2B — condiții stabilite la telefon. */
    'b2b_delivery_note' => 'Colectare și livrare în Bălți, Edineț, Briceni și Drochia — condițiile și pragul minim se stabilesc la telefon, în funcție de volum și zonă.',
];
