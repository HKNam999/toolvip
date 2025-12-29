<?php
require_once '../core/functions.php';
require_once '../core/auth.php';
require_once '../core/icons.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $deposits = readJSON('deposits');
    $users = readJSON('users');
    $id = $_POST['id'];
    $action = $_POST['action'];
    
    foreach ($deposits as &$d) {
        if ($d['id'] == $id && $d['status'] === 'pending') {
            if ($action === 'approve') {
                $d['status'] = 'completed';
                // Update user balance
                foreach ($users as &$u) {
                    if ($u['id'] == $d['user_id'] || $u['username'] == ($d['username'] ?? '')) {
                        $u['balance'] = ($u['balance'] ?? 0) + $d['amount'];
                        break;
                    }
                }
                writeJSON('users', $users);
            } else {
                $d['status'] = 'cancelled';
            }
            break;
        }
    }
    writeJSON('deposits', $deposits);
    header('Location: deposits.php?status=success');
    exit;
}

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
        html { zoom: 0.8; }
        body { background-color: #0f172a; color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-black text-red-500 uppercase">Duyệt Nạp Tiền</h1>
            <a href="dashboard.php" class="px-4 py-2 glass rounded-lg hover:bg-white/10 transition-all text-sm font-bold">Quay lại</a>
        </div>
        
        <div class="glass rounded-2xl overflow-hidden border border-white/5">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-slate-400 uppercase text-[10px] font-black tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Mã GD</th>
                        <th class="px-6 py-4">Người nạp</th>
                        <th class="px-6 py-4">Số tiền</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($deposits)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 italic">Chưa có giao dịch nào</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach (array_reverse($deposits) as $d): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-mono text-[11px] text-slate-400"><?php echo htmlspecialchars($d['id'] ?? ''); ?></td>
                            <td class="px-6 py-4 font-bold text-sm"><?php echo htmlspecialchars($d['username'] ?? $d['user_id'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 text-green-400 font-black text-sm"><?php echo formatMoney($d['amount'] ?? 0); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-lg bg-<?php echo ($d['status'] === 'completed' ? 'green' : ($d['status'] === 'pending' ? 'yellow' : 'red')); ?>-500/10 text-<?php echo ($d['status'] === 'completed' ? 'green' : ($d['status'] === 'pending' ? 'yellow' : 'red')); ?>-500 text-[10px] font-black uppercase tracking-tighter">
                                    <?php echo $d['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($d['status'] === 'pending'): ?>
                                <form method="POST" class="inline-flex gap-2">
                                    <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                                    <button name="action" value="approve" class="px-3 py-1.5 bg-green-500/20 text-green-500 rounded-lg hover:bg-green-500 hover:text-black transition-all text-[11px] font-black uppercase tracking-tighter">Duyệt</button>
                                    <button name="action" value="reject" class="px-3 py-1.5 bg-red-500/20 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all text-[11px] font-black uppercase tracking-tighter">Hủy</button>
                                </form>
                                <?php else: ?>
                                <span class="text-slate-600 text-[10px] font-black uppercase tracking-tighter">Đã xử lý</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>