<?php
require_once '../core/functions.php';
require_once '../core/auth.php';

// Kiểm tra quyền admin
requireAdmin();

$admin = getCurrentAdmin();
$users = readJSON('users');
$banks = readJSON('banks');
$deposits = readJSON('deposits');
$keys = readJSON('keys');

// Tính toán thống kê
$totalUsers = count($users);
$totalBalance = 0;
foreach ($users as $user) {
    $totalBalance += (int)($user['balance'] ?? 0);
}
$totalBanks = count($banks);
$totalDeposits = count($deposits);
$totalKeys = count($keys);

// Lấy 5 người dùng mới nhất
$recentUsers = array_slice(array_reverse($users), 0, 5);
// Lấy 5 giao dịch nạp tiền mới nhất
$recentDeposits = array_slice(array_reverse($deposits), 0, 5);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TOOLTX2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/transitions.css">
    <style>
        html {
            zoom: 0.9;
        }
        body { 
            background-color: #0f172a; 
            color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
        }
        .admin-card {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(249, 115, 22, 0.1) 100%);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <nav class="sticky top-0 z-50 p-4 glass border-b border-white/5 flex justify-between items-center px-6 md:px-12">
        <div class="flex items-center gap-3">
            <div class="p-1 bg-gradient-to-br from-red-500 to-orange-600 rounded-xl">
                <div class="h-10 w-10 rounded-lg bg-black flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <span class="text-2xl font-extrabold tracking-tighter text-red-500">ADMIN PANEL</span>
        </div>
        <div class="flex gap-6 items-center">
            <div class="hidden sm:flex flex-col items-end">
                <span class="text-[10px] text-slate-500 uppercase font-black tracking-[0.2em]">Quản trị viên</span>
                <span class="text-sm font-bold text-slate-200"><?php echo htmlspecialchars($admin['username']); ?></span>
            </div>
            <a href="../logout.php" class="text-red-400 hover:text-red-300 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
                Đăng xuất
            </a>
        </div>
    </nav>

    <main class="flex-grow p-6 max-w-7xl mx-auto w-full">
        <!-- Thống kê tổng quan -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="admin-card p-6 rounded-2xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-xs font-black uppercase tracking-widest mb-2">Tổng người dùng</p>
                        <p class="text-3xl font-black text-red-400"><?php echo $totalUsers; ?></p>
                    </div>
                    <svg class="w-8 h-8 text-red-500/50" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM14.305 12.795a6.002 6.002 0 01-5.622 3.205H7c-.956 0-1.864-.119-2.75-.354M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>

            <div class="admin-card p-6 rounded-2xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-xs font-black uppercase tracking-widest mb-2">Tổng số dư</p>
                        <p class="text-2xl font-black text-orange-400"><?php echo formatMoney($totalBalance); ?></p>
                    </div>
                    <svg class="w-8 h-8 text-orange-500/50" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.16 5.314l4.897-1.596A1 1 0 0114.82 4.75l1.357 8.143a1 1 0 01-.97 1.152H9.25a1 1 0 01-.148-1.98L9.униц 7.5H5.5A1.5 1.5 0 004 6v-1a1.5 1.5 0 011.5-1.5h2.66z" />
                    </svg>
                </div>
            </div>

            <div class="admin-card p-6 rounded-2xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-xs font-black uppercase tracking-widest mb-2">Ngân hàng</p>
                        <p class="text-3xl font-black text-yellow-400"><?php echo $totalBanks; ?></p>
                    </div>
                    <svg class="w-8 h-8 text-yellow-500/50" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
                    </svg>
                </div>
            </div>

            <div class="admin-card p-6 rounded-2xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-xs font-black uppercase tracking-widest mb-2">Key đã bán</p>
                        <p class="text-3xl font-black text-blue-400"><?php echo $totalKeys; ?></p>
                    </div>
                    <svg class="w-8 h-8 text-blue-500/50" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quản lý chức năng -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <!-- Recent Users Table -->
            <div class="glass p-6 rounded-2xl border border-white/5">
                <h3 class="text-lg font-black mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Người dùng mới nhất
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase font-black border-b border-white/5">
                            <tr>
                                <th class="px-4 py-3">Username</th>
                                <th class="px-4 py-3">Số dư</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php foreach ($recentUsers as $u): ?>
                            <tr>
                                <td class="px-4 py-3 font-bold"><?php echo htmlspecialchars($u['username']); ?></td>
                                <td class="px-4 py-3 text-orange-400 font-bold"><?php echo formatMoney($u['balance'] ?? 0); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Deposits Table -->
            <div class="glass p-6 rounded-2xl border border-white/5">
                <h3 class="text-lg font-black mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Giao dịch gần đây
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase font-black border-b border-white/5">
                            <tr>
                                <th class="px-4 py-3">User ID</th>
                                <th class="px-4 py-3">Số tiền</th>
                                <th class="px-4 py-3">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php foreach ($recentDeposits as $d): ?>
                            <tr>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($d['user_id']); ?></td>
                                <td class="px-4 py-3 text-green-400 font-bold"><?php echo formatMoney($d['amount'] ?? 0); ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-lg bg-<?php echo ($d['status'] === 'completed' ? 'green' : 'yellow'); ?>-500/10 text-<?php echo ($d['status'] === 'completed' ? 'green' : 'yellow'); ?>-500 text-[10px] font-black uppercase">
                                        <?php echo $d['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quản lý chức năng -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <a href="banks.php" class="glass p-8 rounded-2xl border border-white/5 hover:border-yellow-500/20 transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-4 bg-yellow-500/10 rounded-2xl text-yellow-500 group-hover:bg-yellow-500 group-hover:text-black transition-all">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]"><?php echo $totalBanks; ?> ngân hàng</span>
                </div>
                <h4 class="text-xl font-black mb-2">Quản lý Ngân hàng</h4>
                <p class="text-sm text-slate-400 mb-6">Thêm, sửa, xóa thông tin tài khoản ngân hàng nhận tiền nạp.</p>
                <button class="w-full py-3 glass rounded-xl text-sm font-black hover:bg-yellow-500 hover:text-black transition-all border border-white/5">QUẢN LÝ NGAY →</button>
            </a>

            <a href="users.php" class="glass p-8 rounded-2xl border border-white/5 hover:border-blue-500/20 transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-4 bg-blue-500/10 rounded-2xl text-blue-500 group-hover:bg-blue-500 group-hover:text-black transition-all">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM14.305 12.795a6.002 6.002 0 01-5.622 3.205H7c-.956 0-1.864-.119-2.75-.354M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]"><?php echo $totalUsers; ?> người dùng</span>
                </div>
                <h4 class="text-xl font-black mb-2">Quản lý Người dùng</h4>
                <p class="text-sm text-slate-400 mb-6">Xem, chỉnh sửa, khóa tài khoản người dùng và quản lý quyền hạn.</p>
                <button class="w-full py-3 glass rounded-xl text-sm font-black hover:bg-blue-500 hover:text-black transition-all border border-white/5">QUẢN LÝ NGAY →</button>
            </a>

            <a href="keys.php" class="glass p-8 rounded-2xl border border-white/5 hover:border-purple-500/20 transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-4 bg-purple-500/10 rounded-2xl text-purple-500 group-hover:bg-purple-500 group-hover:text-black transition-all">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]"><?php echo $totalKeys; ?> key</span>
                </div>
                <h4 class="text-xl font-black mb-2">Quản lý Key</h4>
                <p class="text-sm text-slate-400 mb-6">Tạo, kích hoạt, vô hiệu hóa key sử dụng cho các công cụ.</p>
                <button class="w-full py-3 glass rounded-xl text-sm font-black hover:bg-purple-500 hover:text-black transition-all border border-white/5">QUẢN LÝ NGAY →</button>
            </a>

            <a href="deposits.php" class="glass p-8 rounded-2xl border border-white/5 hover:border-green-500/20 transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-4 bg-green-500/10 rounded-2xl text-green-500 group-hover:bg-green-500 group-hover:text-black transition-all">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h.01a1 1 0 110 2H12zm-2 2a1 1 0 100-2 1 1 0 000 2zm4 0a1 1 0 100-2 1 1 0 000 2zm2-4a1 1 0 110-2h.01a1 1 0 110 2H16zM4 9a1 1 0 100-2 1 1 0 000 2zm2 0a1 1 0 110-2h.01a1 1 0 110 2H6zm10 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]"><?php echo $totalDeposits; ?> giao dịch</span>
                </div>
                <h4 class="text-xl font-black mb-2">Quản lý Nạp tiền</h4>
                <p class="text-sm text-slate-400 mb-6">Xem lịch sử nạp tiền, xác nhận giao dịch và quản lý số dư.</p>
                <button class="w-full py-3 glass rounded-xl text-sm font-black hover:bg-green-500 hover:text-black transition-all border border-white/5">QUẢN LÝ NGAY →</button>
            </a>
        </div>

        <!-- Thông tin hệ thống -->
        <div class="glass p-8 rounded-2xl border border-white/5">
            <h3 class="text-lg font-black mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 7a1 1 0 100-2 1 1 0 000 2zm5-1a1 1 0 11-2 0 1 1 0 012 0zM14 7a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                Thông tin hệ thống
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-slate-500 text-sm mb-2">Phiên bản</p>
                    <p class="text-lg font-bold">TOOLTX2026 v2.0</p>
                </div>
                <div>
                    <p class="text-slate-500 text-sm mb-2">Trạng thái hệ thống</p>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-lg font-bold text-green-400">Ổn định</span>
                    </div>
                </div>
                <div>
                    <p class="text-slate-500 text-sm mb-2">Thời gian cập nhật</p>
                    <p class="text-lg font-bold"><?php echo date('d/m/Y H:i'); ?></p>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/transitions.js"></script>
</body>
</html>
