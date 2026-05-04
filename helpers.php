<?php
function e(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $to): void {
    header("Location: $to");
    exit;
}

function excerpt(?string $s, int $limit = 300): string {
    $s = $s ?? '';

    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($s, 0, $limit, '...');
    }

    return strlen($s) > $limit ? substr($s, 0, $limit - 3) . '...' : $s;
}
