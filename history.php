<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$deposits = readJSON('deposits');
$userDeposits = array_filter($deposits, function($d) {
    return $d['user_id'] === $_SESSION['user_id'];
});
usort($userDeposits, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

$keys = readJSON('keys');
$userKeys = array_filter($keys, function($k) {
    return $k['user_id'] === $_SESSION['user_id'];
});
usort($userKeys, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Giao Dịch - TOOLTX2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #0f172a; color: white; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <nav class="p-4 glass flex justify-between items-center">
        <div class="flex items-center gap-2">
            <a href="dashboard.php" class="flex items-center gap-2">
                <img src="../assets/images/logo-vip.png" alt="Logo" class="h-10 w-10 rounded-full border-2 border-yellow-500">
                <span class="text-xl font-bold text-yellow-500">TOOLTX2026</span>
            </a>
        </div>
        <a href="dashboard.php" class="text-sm text-gray-400 hover:text-white">Quay lại</a>
    </nav>

    <main class="p-6 max-w-6xl mx-auto w-full">
        <h2 class="text-2xl font-bold mb-6 text-yellow-500">Lịch Sử Giao Dịch</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Lịch sử nạp tiền -->
            <div class="glass p-6 rounded-xl">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <span>💰</span> Lịch Sử Nạp Tiền
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="pb-2">Mã đơn</th>
                                <th class="pb-2">Số tiền</th>
                                <th class="pb-2">Trạng thái</th>
                                <th class="pb-2">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($userDeposits)): ?>
                                <tr><td colspan="4" class="py-4 text-center text-gray-500">Chưa có giao dịch nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($userDeposits as $d): ?>
                                    <tr class="border-b border-white/5">
                                        <td class="py-3"><?php echo $d['order_id']; ?></td>
                                        <td class="py-3 font-bold text-green-400"><?php echo formatMoney($d['amount']); ?></td>
                                        <td class="py-3">
                                            <?php if ($d['status'] === 'pending'): ?>
                                                <span class="text-yellow-500">Chờ duyệt</span>
                                            <?php elseif ($d['status'] === 'completed'): ?>
                                                <span class="text-green-500">Thành công</span>
                                            <?php else: ?>
                                                <span class="text-red-500">Đã hủy</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 text-gray-400"><?php echo $d['created_at']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Lịch sử mua key -->
            <div class="glass p-6 rounded-xl">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <span>🔑</span> Lịch Sử Mua Key
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="pb-2">Mã Key</th>
                                <th class="pb-2">Gói</th>
                                <th class="pb-2">SL</th>
                                <th class="pb-2">Tổng tiền</th>
                                <th class="pb-2">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($userKeys)): ?>
                                <tr><td colspan="5" class="py-4 text-center text-gray-500">Chưa có giao dịch nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($userKeys as $k): ?>
                                    <tr class="border-b border-white/5">
                                        <td class="py-3 font-mono text-yellow-500"><?php echo $k['key_code']; ?></td>
                                        <td class="py-3"><?php echo $k['package_name']; ?></td>
                                        <td class="py-3"><?php echo $k['quantity']; ?></td>
                                        <td class="py-3 font-bold"><?php echo formatMoney($k['total_price']); ?></td>
                                        <td class="py-3 text-gray-400"><?php echo $k['created_at']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
