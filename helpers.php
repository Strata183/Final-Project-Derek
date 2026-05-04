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

function stmt_fetch_all(mysqli_stmt $stmt): array {
    if (method_exists($stmt, 'get_result')) {
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    $meta = $stmt->result_metadata();
    if (!$meta) {
        return [];
    }

    $row = [];
    $bind = [];
    while ($field = $meta->fetch_field()) {
        $bind[] = &$row[$field->name];
    }
    call_user_func_array([$stmt, 'bind_result'], $bind);

    $rows = [];
    while ($stmt->fetch()) {
        $copy = [];
        foreach ($row as $key => $value) {
            $copy[$key] = $value;
        }
        $rows[] = $copy;
    }

    return $rows;
}

function stmt_fetch_one(mysqli_stmt $stmt): ?array {
    $rows = stmt_fetch_all($stmt);
    return $rows[0] ?? null;
}
