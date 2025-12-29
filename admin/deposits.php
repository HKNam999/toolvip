<?php
require_once '../core/functions.php';
require_once '../core/auth.php';
require_once '../core/icons.php';

requireAdmin();

$deposits = readJSON('deposits');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Nạp tiền - TOOLTX2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0f172a; color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-black text-red-500">QUẢN LÝ NẠP TIỀN</h1>
            <a href="dashboard.php" class="px-4 py-2 glass rounded-lg hover:bg-white/10 transition-all">Quay lại</a>
        </div>
        
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-slate-400 uppercase text-xs font-black">
                    <tr>
                        <th class="px-6 py-4">ID Giao dịch</th>
                        <th class="px-6 py-4">Người nạp</th>
                        <th class="px-6 py-4">Số tiền</th>
                        <th class="px-6 py-4">Thời gian</th>
                        <th class="px-6 py-4">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($deposits as $d): ?>
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 font-mono"><?php echo htmlspecialchars($d['id'] ?? ''); ?></td>
                        <td class="px-6 py-4 font-bold"><?php echo htmlspecialchars($d['username'] ?? $d['user_id'] ?? ''); ?></td>
                        <td class="px-6 py-4 text-green-400 font-bold"><?php echo formatMoney($d['amount'] ?? 0); ?></td>
                        <td class="px-6 py-4 text-slate-400"><?php echo htmlspecialchars($d['created_at'] ?? ''); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded bg-<?php echo ($d['status'] === 'completed' ? 'green' : 'yellow'); ?>-500/10 text-<?php echo ($d['status'] === 'completed' ? 'green' : 'yellow'); ?>-500 text-[10px] font-black uppercase">
                                <?php echo $d['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>