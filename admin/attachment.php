<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/admin/bootstrap.php';
admin_require_auth();

$leadId = trim((string) ($_GET['lead'] ?? ''));
$fileIndex = isset($_GET['file']) ? (int) $_GET['file'] : -1;

$lead = $leadId !== '' ? leads_find($pdo, $leadId) : null;
if ($lead === null || $fileIndex < 0) {
    http_response_code(404);
    exit;
}

$attachments = leads_parse_attachments($lead);
if (! isset($attachments[$fileIndex])) {
    http_response_code(404);
    exit;
}

$relative = $attachments[$fileIndex];
if (str_contains($relative, '..') || str_starts_with($relative, '/')) {
    http_response_code(403);
    exit;
}

$fullPath = dirname(__DIR__) . '/data/' . $relative;
if (! is_readable($fullPath) || ! is_file($fullPath)) {
    http_response_code(404);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($fullPath);
$allowed = ['image/jpeg', 'image/png', 'image/webp'];
if (! is_string($mime) || ! in_array($mime, $allowed, true)) {
    http_response_code(403);
    exit;
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . (string) filesize($fullPath));
header('Content-Disposition: inline; filename="attachment-' . $fileIndex . '"');
header('X-Content-Type-Options: nosniff');
readfile($fullPath);
exit;
