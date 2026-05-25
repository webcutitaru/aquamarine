<?php

declare(strict_types=1);

function leads_generate_uuid(): string
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * @param list<string>|null $attachments
 */
function leads_insert(
    PDO $pdo,
    string $name,
    string $phone,
    ?string $email,
    ?string $serviceInterest,
    ?string $preferredMag,
    ?string $message,
    ?array $attachments,
    ?string $ipHashed,
    string $lang = 'ro'
): string {
    $id = leads_generate_uuid();
    $attachmentsJson = null;
    if ($attachments !== null && $attachments !== []) {
        $attachmentsJson = json_encode($attachments, JSON_UNESCAPED_UNICODE);
    }

    $lang = $lang === 'ru' ? 'ru' : 'ro';

    $stmt = $pdo->prepare(
        'INSERT INTO leads (id, name, phone, email, service_interest, preferred_mag, message, attachments_json, ip_hashed, lang, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $id,
        $name,
        $phone,
        $email,
        $serviceInterest,
        $preferredMag,
        $message,
        $attachmentsJson,
        $ipHashed,
        $lang,
        'new',
    ]);

    return $id;
}

function leads_find(PDO $pdo, string $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM leads WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return is_array($row) ? $row : null;
}

/**
 * @return list<array<string, mixed>>
 */
function leads_list(PDO $pdo, ?string $status = null, ?string $mag = null, ?string $dateFrom = null, ?string $dateTo = null, int $limit = 200): array
{
    $sql = 'SELECT id, created_at, name, phone, email, service_interest, preferred_mag, status
            FROM leads WHERE 1=1';
    $params = [];

    if ($status !== null && $status !== '' && in_array($status, ['new', 'contacted', 'closed', 'spam'], true)) {
        $sql .= ' AND status = ?';
        $params[] = $status;
    }
    if ($mag !== null && $mag !== '') {
        $sql .= ' AND preferred_mag = ?';
        $params[] = $mag;
    }
    if ($dateFrom !== null && $dateFrom !== '') {
        $sql .= ' AND created_at >= ?';
        $params[] = $dateFrom . ' 00:00:00';
    }
    if ($dateTo !== null && $dateTo !== '') {
        $sql .= ' AND created_at <= ?';
        $params[] = $dateTo . ' 23:59:59';
    }

    $sql .= ' ORDER BY created_at DESC LIMIT ' . (int) $limit;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll() ?: [];
}

function leads_count_by_status(PDO $pdo, string $status): int
{
    $stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM leads WHERE status = ?');
    $stmt->execute([$status]);
    $row = $stmt->fetch();

    return is_array($row) ? (int) ($row['c'] ?? 0) : 0;
}

/**
 * @return list<array<string, mixed>>
 */
function leads_recent(PDO $pdo, int $limit = 5): array
{
    $stmt = $pdo->prepare(
        'SELECT id, created_at, name, phone, preferred_mag, status
         FROM leads ORDER BY created_at DESC LIMIT ?'
    );
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll() ?: [];
}

function leads_update_crm(PDO $pdo, string $id, string $status, ?string $adminNotes, bool $markContacted): void
{
    $allowed = ['new', 'contacted', 'closed', 'spam'];
    if (! in_array($status, $allowed, true)) {
        $status = 'new';
    }

    $contactedAt = null;
    if ($markContacted || $status === 'contacted') {
        $contactedAt = gmdate('Y-m-d H:i:s');
    }

    $stmt = $pdo->prepare(
        'UPDATE leads SET status = ?, admin_notes = ?, contacted_at = COALESCE(?, contacted_at) WHERE id = ?'
    );
    $stmt->execute([
        $status,
        $adminNotes !== '' ? $adminNotes : null,
        $contactedAt,
        $id,
    ]);
}

/**
 * @return list<string>
 */
function leads_parse_attachments(?array $lead): array
{
    if ($lead === null) {
        return [];
    }
    $raw = $lead['attachments_json'] ?? null;
    if ($raw === null || $raw === '') {
        return [];
    }
    if (is_string($raw)) {
        $decoded = json_decode($raw, true);
    } else {
        $decoded = $raw;
    }

    if (! is_array($decoded)) {
        return [];
    }

    $out = [];
    foreach ($decoded as $path) {
        if (is_string($path) && $path !== '') {
            $out[] = $path;
        }
    }

    return $out;
}

function leads_status_label(string $status): string
{
    return match ($status) {
        'contacted' => 'Contactat',
        'closed' => 'Închis',
        'spam' => 'Spam',
        default => 'Nou',
    };
}
