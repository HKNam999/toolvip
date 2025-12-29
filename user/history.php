<?php
require_once '../core/functions.php';

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
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        html {
            zoom: 0.9;
        }
        body { 
            background-color: #0f172a; 
            color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: 
                radial-gradient(at 0% 0%, rgba(234, 179, 8, 0.04) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(249, 115, 22, 0.04) 0px, transparent 50%);
        }
        .glass { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(16px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
        }
        .icon-box {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.2) 0%, rgba(249, 115, 22, 0.2) 100%);
            border: 1px solid rgba(251, 191, 36, 0.3);
        }
        .text-gradient {
            background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <nav class="p-4 glass border-b border-white/5 flex justify-between items-center px-6 md:px-12 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-br from-yellow-400 to-orange-600 rounded-xl shadow-lg shadow-orange-500/20">
                    <img src="../assets/images/logo-vip.png" alt="Logo" class="h-8 w-8 rounded-lg bg-black">
                </div>
                <span class="text-xl font-black tracking-tighter text-gradient">TOOLTX2026</span>
            </a>
        </div>

        <div class="flex items-center gap-4" x-data="{ open: false }">
            <button @click="open = !open" class="p-2.5 bg-slate-800/80 backdrop-blur-md rounded-xl text-slate-400 hover:bg-slate-700/80 hover:text-white transition-all border border-white/10 shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
                 @click.away="open = false" 
                 class="absolute right-6 top-20 w-64 bg-slate-900/95 backdrop-blur-xl rounded-[1.5rem] border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.5)] py-3 overflow-hidden z-[60]" 
                 style="display: none;">
                <div class="px-4 py-3 border-b border-white/5 mb-2">
                    <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest">Tài khoản</p>
                    <p class="text-sm font-bold text-slate-200 truncate"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                </div>
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 text-sm font-semibold transition-all">
                    <?php echo getIcon('home', 'w-5 h-5 text-yellow-500'); ?>
                    Trang chủ
                </a>
                <a href="deposit.php" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 text-sm font-semibold transition-all">
                    <?php echo getIcon('wallet', 'w-5 h-5 text-orange-500'); ?>
                    Nạp tiền
                </a>
                <a href="buy-key.php" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 text-sm font-semibold transition-all">
                    <?php echo getIcon('key', 'w-5 h-5 text-blue-500'); ?>
                    Mua Key
                </a>
                <a href="history.php" class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 text-sm font-semibold transition-all border-b border-white/5">
                    <?php echo getIcon('history', 'w-5 h-5 text-purple-500'); ?>
                    Lịch sử
                </a>
                <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 hover:bg-red-500/10 text-red-400 text-sm font-bold transition-all">
                    <?php echo getIcon('logout', 'w-5 h-5'); ?>
                    Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <main class="p-6 max-w-7xl mx-auto w-full mt-6">
        <div class="glass p-8 rounded-[2.5rem] border border-white/5 mb-10 relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-purple-500/5 rounded-full blur-3xl"></div>
            
            <div class="flex items-center gap-4 relative z-10">
                <a href="dashboard.php" class="p-2.5 bg-slate-800/80 backdrop-blur-md rounded-xl text-slate-400 hover:bg-slate-700/80 hover:text-white transition-all border border-white/10 shadow-lg group">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-yellow-500/10 rounded-xl text-yellow-500">
                        <?php echo getIcon('history', 'w-6 h-6'); ?>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black">Lịch Sử</h2>
                        <p class="text-xs text-slate-400">Theo dõi giao dịch</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Lịch sử nạp tiền -->
            <div class="glass p-8 rounded-[2.5rem] border border-white/5">
                <h3 class="text-xl font-black mb-6 flex items-center gap-3">
                    <div class="p-3 icon-box rounded-2xl text-yellow-500">
                        <?php echo getIcon('wallet', 'w-6 h-6'); ?>
                    </div>
                    Lịch Sử Nạp Tiền
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
            <div class="glass p-8 rounded-[2.5rem] border border-white/5">
                <h3 class="text-xl font-black mb-6 flex items-center gap-3">
                    <div class="p-3 icon-box rounded-2xl text-orange-500">
                        <?php echo getIcon('key', 'w-6 h-6'); ?>
                    </div>
                    Lịch Sử Mua Key
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
