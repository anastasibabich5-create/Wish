<?php
// Задай СВОЙ пароль:
$ADMIN_PASS = 'Anastasi1';

$DATA_FILE = __DIR__ . '/wishlist.json';

header('Content-Type: application/json; charset=utf-8');

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!isset($data['pass']) || $data['pass'] !== $ADMIN_PASS) {
    echo json_encode(['ok' => false, 'error' => 'Неверный пароль']);
    exit;
}

$id  = $data['id'] ?? '';
$buy = !empty($data['buy']);

if (!$id) {
    echo json_encode(['ok' => false, 'error' => 'Нет id']);
    exit;
}

if (!file_exists($DATA_FILE)) {
    $state = ['items' => []];
} else {
    $state = json_decode(file_get_contents($DATA_FILE), true);
    if (!is_array($state)) {
        $state = ['items' => []];
    }
}

$items = $state['items'] ?? [];
$items = array_unique($items);

if ($buy) {
    if (!in_array($id, $items, true)) {
        $items[] = $id;
    }
} else {
    $items = array_values(array_filter($items, fn($v) => $v !== $id));
}

$state['items'] = $items;

file_put_contents($DATA_FILE, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['ok' => true, 'items' => $items]);