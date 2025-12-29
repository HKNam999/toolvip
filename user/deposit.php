<?php
require_once '../core/functions.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$banks = readJSON('banks');
$error = '';
$success = false;
$order = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = (int)$_POST['amount'];
    $bank_id = $_POST['bank_id'];
    
    if ($amount < 10000) {
        $error = 'Số tiền nạp tối thiểu là 10.000 VND.';
    } else {
        $selectedBank = null;
        foreach ($banks as $b) {
            if ($b['id'] == $bank_id) {
                $selectedBank = $b;
                break;
            }
        }

        if ($selectedBank) {
            $order_id = 'DEP' . strtoupper(generateRandomString(8));
            $description = 'NAP' . strtoupper(generateRandomString(6));
            
            $order = [
                'order_id' => $order_id,
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'amount' => $amount,
                'bank_name' => $selectedBank['bank_name'],
                'account_no' => $selectedBank['account_no'],
                'account_name' => $selectedBank['account_name'],
                'description' => $description,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'expires_at' => date('Y-m-d H:i:s', strtotime('+20 minutes'))
            ];

            $deposits = readJSON('deposits');
            $deposits[] = $order;
            writeJSON('deposits', $deposits);
            $success = true;
        } else {
            $error = 'Vui lòng chọn ngân hàng hợp lệ.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nạp Tiền - TOOLTX2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #020617; 
            color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass { 
            background: rgba(255, 255, 255, 0.03); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.08); 
        }
        .text-gradient {
            background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn-primary {
            background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <nav class="p-4 glass border-b border-white/5 flex justify-between items-center px-6 md:px-12 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="p-2.5 bg-slate-800/50 backdrop-blur-md rounded-xl text-slate-400 hover:bg-slate-700/80 hover:text-white transition-all border border-white/10 shadow-lg group">
                <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <a href="dashboard.php" class="flex items-center gap-2">
                <div class="p-1 bg-gradient-to-br from-yellow-400 to-orange-600 rounded-lg">
                    <img src="../assets/images/logo-vip.png" alt="Logo" class="h-8 w-8 rounded-md bg-black">
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

    <main class="p-6 max-w-4xl mx-auto w-full mt-8">
        <div class="flex items-center gap-4 mb-8">
            <div class="p-3 bg-yellow-500/10 rounded-2xl text-yellow-500">
                <?php echo getIcon('wallet', 'w-8 h-8'); ?>
            </div>
            <div>
                <h2 class="text-3xl font-black">Nạp Tiền</h2>
                <p class="text-sm text-slate-400">Nạp tiền vào tài khoản để mua key kích hoạt tool</p>
            </div>
        </div>

        <?php if (!$success): ?>
            <div class="glass p-8 rounded-3xl">
                <?php if ($error): ?>
                    <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                        <?php echo getIcon('x', 'w-5 h-5'); ?>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-8">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-4">1. Chọn ngân hàng nạp</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <?php if (empty($banks)): ?>
                                <p class="col-span-full text-red-400 text-sm italic">Hệ thống chưa cấu hình ngân hàng. Vui lòng liên hệ Admin.</p>
                            <?php else: ?>
                                <?php foreach ($banks as $b): ?>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="bank_id" value="<?php echo $b['id']; ?>" required class="hidden peer">
                                        <div class="p-4 glass rounded-2xl border border-white/5 peer-checked:border-yellow-500 peer-checked:bg-yellow-500/10 group-hover:border-white/20 transition-all text-center">
                                            <div class="h-12 flex items-center justify-center mb-3">
                                                <img src="../assets/images/<?php echo $b['logo']; ?>" alt="<?php echo $b['bank_name']; ?>" class="max-h-full max-w-full object-contain filter brightness-110">
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-tighter text-slate-400 peer-checked:text-yellow-500"><?php echo $b['bank_name']; ?></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-4">2. Nhập số tiền (VND)</label>
                        <div class="relative">
                            <input type="number" name="amount" min="10000" step="1000" required placeholder="Tối thiểu 10.000" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-5 focus:outline-none focus:border-yellow-500 text-2xl font-black text-yellow-500 placeholder:text-slate-700">
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-500 font-bold">VND</div>
                        </div>
                    </div>

                    <button type="submit" class="w-full btn-primary text-black font-black py-5 rounded-2xl hover:scale-[1.02] transition-all text-lg flex items-center justify-center gap-3">
                        <?php echo getIcon('plus', 'w-6 h-6'); ?>
                        TẠO ĐƠN NẠP TIỀN
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="glass p-8 rounded-3xl text-center">
                <div class="w-20 h-20 bg-green-500/10 rounded-full flex items-center justify-center text-green-500 mx-auto mb-6">
                    <?php echo getIcon('check', 'w-10 h-10'); ?>
                </div>
                <h3 class="text-2xl font-black text-green-500 mb-2">Đơn nạp đã sẵn sàng!</h3>
                <p class="text-slate-400 mb-10">Vui lòng quét mã QR hoặc chuyển khoản theo thông tin bên dưới.</p>
                
                <div class="flex flex-col lg:flex-row gap-10 items-center justify-center">
                    <div class="p-6 bg-white rounded-3xl shadow-2xl shadow-yellow-500/10">
                        <?php 
                        $qr_url = "https://img.vietqr.io/image/{$order['bank_name']}-{$order['account_no']}-qr_only.png?amount={$order['amount']}&addInfo={$order['description']}&accountName=" . urlencode($order['account_name']);
                        ?>
                        <img src="<?php echo $qr_url; ?>" alt="VietQR" class="w-64 h-64">
                        <div class="mt-4 text-black font-black text-xs tracking-widest uppercase">Quét để thanh toán</div>
                    </div>
                    
                    <div class="text-left space-y-4 w-full max-w-md">
                        <div class="glass p-6 rounded-2xl border border-white/5 space-y-4">
                            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                                <span class="text-xs font-bold text-slate-500 uppercase">Ngân hàng</span>
                                <span class="font-black text-yellow-500"><?php echo $order['bank_name']; ?></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                                <span class="text-xs font-bold text-slate-500 uppercase">Số tài khoản</span>
                                <span class="font-black text-xl tracking-wider"><?php echo $order['account_no']; ?></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                                <span class="text-xs font-bold text-slate-500 uppercase">Chủ tài khoản</span>
                                <span class="font-black"><?php echo $order['account_name']; ?></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                                <span class="text-xs font-bold text-slate-500 uppercase">Số tiền</span>
                                <span class="font-black text-2xl text-green-500"><?php echo formatMoney($order['amount']); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-500 uppercase">Nội dung</span>
                                <span class="font-black text-xl text-red-500 px-3 py-1 bg-red-500/10 rounded-lg"><?php echo $order['description']; ?></span>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-2xl flex items-start gap-3">
                            <div class="text-yellow-500 mt-1"><?php echo getIcon('history', 'w-5 h-5'); ?></div>
                            <p class="text-[11px] text-yellow-500/80 font-bold leading-relaxed">
                                Đơn nạp sẽ tự động hết hạn sau 20 phút. Vui lòng chuyển đúng số tiền và nội dung để được xử lý nhanh nhất.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-12 flex justify-center gap-6">
                    <a href="history.php" class="text-sm font-bold text-slate-400 hover:text-white transition-colors flex items-center gap-2">
                        <?php echo getIcon('history', 'w-4 h-4'); ?>
                        Xem lịch sử nạp
                    </a>
                    <a href="dashboard.php" class="text-sm font-bold text-yellow-500 hover:underline">Quay lại trang chủ</a>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
