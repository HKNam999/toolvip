<?php
require_once '../../core/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$id = $_GET['id'] ?? '';
$deposits = readJSON('deposits');
$users = readJSON('users');

foreach ($deposits as $d) {
    if ((string)($d['id'] ?? '') === (string)$id) {
        $status = $d['status'] ?? 'pending';
        
        $new_balance = '0';
        foreach ($users as $u) {
            if ($u['id'] === $_SESSION['user_id']) {
                $new_balance = formatMoney($u['balance'] ?? 0);
                break;
            }
        }
        
        echo json_encode([
            'status' => $status,
            'new_balance' => $new_balance
        ]);
        exit;
    }
}

echo json_encode(['status' => 'not_found']);
