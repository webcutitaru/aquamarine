<?php

declare(strict_types=1);

return [
    'privacy' => [
        'meta_title' => 'Politica privind protecția datelor',
        'meta_description' => 'Informări despre prelucrarea datelor personale în cadrul Aquamarine.',
        'eyebrow' => 'Protecție date',
        'h1' => 'Politica privind protecția datelor personale',
        'sections' => [
            ['title' => 'Operator', 'html' => '<p class="mt-3">Operator de date: <strong class="text-ink">:site</strong>, cu adresă la :address.</p><p class="mt-3">Pentru întrebări privind datele personale: telefon <a class="underline decoration-brand-500 hover:text-brand-800" href="tel::phone_e164">:phone</a>:email_block.</p>'],
            ['title' => 'Ce date colectăm', 'html' => '<p class="mt-3">Prin site și prin canalele afișate (telefon, WhatsApp), pot fi prelucrate:</p><ul class="mt-3 list-disc space-y-2 ps-6"><li>nume și date de contact (telefon, eventual email);</li><li>detaliile mesajului trimis prin formular (serviciu ales, text liber);</li><li>date tehnice limitate în scop de securitate (de exemplu adresă IP procesată minim pentru protecție anti-spam, dacă este aplicată).</li></ul>'],
            ['title' => 'Scopuri și temeiuri', 'html' => '<p class="mt-3">Folosim aceste date pentru a răspunde solicitărilor dumneavoastră, a programa predarea/preluarea articolelor și a comunica informații legate de serviciile solicitate. Temeiul legal poate fi execuția unei solicitări înainte de încheierea unui contract și/sau consimțământul exprimat la bifarea căsuței din formular, după caz.</p>'],
            ['title' => 'Durata păstrării', 'html' => '<p class="mt-3">Datele din solicitări sunt păstrate pe durata necesară procesării cererii și ulterior în limitele prevăzute de lege sau politici interne documentate (ex.: dovezi contabile / reclamații), dacă aplicabil.</p>'],
            ['title' => 'Destinatari', 'html' => '<p class="mt-3">Nu vindem datele personale. Poate fi necesar accesul furnizorilor tehnici (ex.: găzduire web, email) strict pentru funcționarea siteului și comunicării, în condițiile contractelor încheiate cu aceștia.</p>'],
            ['title' => 'Drepturile dumneavoastră', 'html' => '<p class="mt-3">În conformitate cu legislația aplicabilă privind protecția datelor (inclusiv GDPR, dacă este aplicabilă situației dumneavoastră), puteți solicita acces la date, rectificare, ștergere sau restricționarea prelucrării, precum și să vă opuneți anumitor prelucrări, în condițiile legii. Pentru exercitarea drepturilor, folosiți datele de contact de mai sus.</p>'],
            ['title' => 'Actualizări', 'html' => '<p class="mt-3">Această pagină poate fi actualizată. Versiunea afișată este cea publicată pe site la data vizitei dumneavoastră.</p>'],
        ],
        'email_block' => ', sau email <a class="underline decoration-brand-500 hover:text-brand-800" href="mailto::email">:email</a>',
    ],
    'terms' => [
        'meta_title' => 'Termeni și condiții',
        'meta_description' => 'Termenii de utilizare a siteului Aquamarine și informații generale despre servicii.',
        'eyebrow' => 'Informări legale',
        'h1' => 'Termeni și condiții',
        'sections' => [
            ['title' => 'Obiect', 'html' => '<p class="mt-3">Site-ul prezintă informații despre Aquamarine, rețea de curățătorii chimice în :cities, cu sediu de referință la :address. Utilizarea siteului implică acceptarea acestor termeni în versiunea publicată la momentul vizitei dumneavoastră.</p>'],
            ['title' => 'Informații și prețuri', 'html' => '<p class="mt-3">Textele și grilele de preț au caracter orientativ. Tariful final și termenul de execuție se stabilesc după inspectarea articolelor în punctul de lucru, în funcție de material, stare și cerințele tratamentului.</p>'],
            ['title' => 'Limitări tehnice', 'html' => '<p class="mt-3">Unele pete, degradări sau combinații de materiale pot fi ireversibile sau pot necesita refuzarea tratamentului pentru a proteja articolul. În aceste situații veți fi informați înainte de începerea procesului, acolo unde este posibil.</p>'],
            ['title' => 'Formularul de contact', 'html' => '<p class="mt-3">Mesajele trimise prin site sunt destinate exclusiv comunicării cu Aquamarine. Nu utilizați formularul pentru conținut ilegal, spam sau solicitări înșelătoare.</p>'],
            ['title' => 'Proprietate intelectuală', 'html' => '<p class="mt-3">Conținutul siteului (texte structurate, aranjament grafic) aparține Aquamarine sau este folosit cu drepturi corespunzătoare. Reproducerea fără acord poate încălca drepturile aplicabile.</p>'],
            ['title' => 'Contact', 'html' => '<p class="mt-3">Întrebări legate de acești termeni: <a class="underline decoration-brand-500 hover:text-brand-800" href="mailto::email">:email</a>.</p>'],
        ],
    ],
];
