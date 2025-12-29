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
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Lỗi xác thực CSRF. Vui lòng thử lại.';
    } else {
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
                    'id' => $order_id,
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
    <link rel="stylesheet" href="../assets/css/transitions.css">
    <style>
        html { zoom: 0.9; }
        body { 
            background-color: #0f172a; 
            color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: 
                radial-gradient(at 0% 0%, rgba(234, 179, 8, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(249, 115, 22, 0.05) 0px, transparent 50%);
        }
        .glass { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(16px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
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
        .bank-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .bank-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
        }
        .qr-container {
            background: white;
            padding: 20px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
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

    <main class="p-6 max-w-5xl mx-auto w-full mt-8">
        <div class="glass p-8 rounded-[2.5rem] border border-white/5 mb-10 relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-yellow-500/5 rounded-full blur-3xl"></div>
            
            <div class="flex items-center gap-4 relative z-10">
                <a href="dashboard.php" class="p-2.5 bg-slate-800/80 backdrop-blur-md rounded-xl text-slate-400 hover:bg-slate-700/80 hover:text-white transition-all border border-white/10 shadow-lg group">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-yellow-500/10 rounded-xl text-yellow-500">
                        <?php echo getIcon('wallet', 'w-6 h-6'); ?>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black">Nạp Tiền</h2>
                        <p class="text-xs text-slate-400 uppercase tracking-widest font-bold">Hệ thống nạp tự động 24/7</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$success): ?>
            <div class="glass p-8 rounded-[2.5rem] border border-white/5">
                <?php if ($error): ?>
                    <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-2xl mb-8 text-sm flex items-center gap-3">
                        <?php echo getIcon('x', 'w-5 h-5'); ?>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-10">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 mb-6 ml-1">1. Chọn phương thức thanh toán</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php if (empty($banks)): ?>
                                <p class="col-span-full text-red-400 text-sm italic">Hệ thống chưa cấu hình ngân hàng. Vui lòng liên hệ Admin.</p>
                            <?php else: ?>
                                <?php foreach ($banks as $b): ?>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="bank_id" value="<?php echo $b['id']; ?>" required class="hidden peer">
                                        <div class="p-6 glass rounded-3xl border border-white/5 peer-checked:border-yellow-500 peer-checked:bg-yellow-500/10 group-hover:border-white/20 transition-all text-center bank-card">
                                            <div class="h-12 flex items-center justify-center mb-4">
                                                <img src="../assets/images/<?php echo $b['logo']; ?>" alt="<?php echo $b['bank_name']; ?>" class="max-h-full max-w-full object-contain filter brightness-110">
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 peer-checked:text-yellow-500"><?php echo $b['bank_name']; ?></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 mb-6 ml-1">2. Nhập số tiền cần nạp</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-yellow-500">
                                <?php echo getIcon('wallet', 'w-6 h-6'); ?>
                            </div>
                            <input type="number" name="amount" min="10000" step="1000" required placeholder="Tối thiểu 10.000 VND" 
                                class="w-full bg-white/5 border border-white/10 rounded-[2rem] pl-16 pr-20 py-6 focus:outline-none focus:border-yellow-500/50 focus:bg-white/10 text-2xl font-black text-yellow-500 placeholder:text-slate-700 transition-all">
                            <div class="absolute right-8 top-1/2 -translate-y-1/2 text-slate-500 font-black tracking-widest">VND</div>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-4 ml-1">
                            <?php foreach([20000, 50000, 100000, 200000, 500000] as $val): ?>
                                <button type="button" onclick="document.getElementsByName('amount')[0].value='<?php echo $val; ?>'" class="px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-xs font-bold transition-all">
                                    +<?php echo number_format($val/1000); ?>K
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="w-full btn-primary text-black font-black py-6 rounded-[2rem] hover:scale-[1.02] active:scale-[0.98] transition-all text-xl flex items-center justify-center gap-3">
                        TIẾP TỤC THANH TOÁN
                        <?php echo getIcon('rocket', 'w-6 h-6'); ?>
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left: Status & QR -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="glass p-8 rounded-[3rem] border border-white/5 text-center relative overflow-hidden">
                        <div class="absolute -left-24 -top-24 w-64 h-64 bg-yellow-500/5 rounded-full blur-3xl"></div>
                        
                        <div class="flex items-center justify-between mb-8">
                            <div class="text-left">
                                <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-1">Số dư hiện tại</p>
                                <p class="text-2xl font-black text-gradient" id="current-balance"><?php echo formatMoney($currentUser['balance'] ?? 0); ?></p>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1 bg-yellow-500/10 rounded-full border border-yellow-500/20">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                                <span class="text-[10px] font-black text-yellow-500 uppercase tracking-widest">Đang chờ thanh toán</span>
                            </div>
                        </div>

                        <div class="qr-container group relative mx-auto inline-block mb-6">
                            <?php 
                            $qr_url = "https://img.vietqr.io/image/{$order['bank_name']}-{$order['account_no']}-qr_only.png?amount={$order['amount']}&addInfo={$order['description']}&accountName=" . urlencode($order['account_name']);
                            ?>
                            <img src="<?php echo $qr_url; ?>" alt="VietQR" class="w-64 h-64 rounded-xl relative z-10">
                            <div class="absolute inset-0 border-4 border-yellow-500/20 rounded-[24px] pointer-events-none group-hover:border-yellow-500/50 transition-all z-20"></div>
                            
                            <!-- Scanner Effect -->
                            <div class="absolute inset-x-4 top-4 h-0.5 bg-yellow-500/50 shadow-[0_0_15px_rgba(234,179,8,0.5)] animate-scan z-30"></div>
                        </div>
                        
                        <div class="space-y-2 mb-8">
                            <h3 class="text-2xl font-black text-slate-100">Quét mã để nạp tiền</h3>
                            <p class="text-sm text-slate-400">Hệ thống sẽ tự động cập nhật sau khi nhận được tiền</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="glass p-4 rounded-2xl border border-white/5">
                                <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-1">Trạng thái</p>
                                <p class="text-sm font-bold text-yellow-500 flex items-center justify-center gap-2" id="order-status">
                                    <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Kiểm tra...
                                </p>
                            </div>
                            <div class="glass p-4 rounded-2xl border border-white/5">
                                <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-1">Thời gian còn lại</p>
                                <p class="text-sm font-bold text-slate-200" id="expiry-timer">19:59</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Bank Info -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="glass p-6 rounded-[2.5rem] border border-white/5">
                        <h4 class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] mb-6 ml-1">Thông tin chuyển khoản</h4>
                        
                        <div class="space-y-3">
                            <div class="glass p-4 rounded-2xl border border-white/10 flex justify-between items-center group hover:bg-white/5 transition-all">
                                <div class="text-left">
                                    <p class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-0.5">Mã giao dịch</p>
                                    <p class="font-black text-slate-200 text-base tracking-wider"><?php echo $order['id']; ?></p>
                                </div>
                                <button onclick="copyText('<?php echo $order['id']; ?>', this)" class="p-2 hover:text-yellow-500 transition-colors">
                                    <?php echo getIcon('copy', 'w-4 h-4'); ?>
                                </button>
                            </div>

                            <div class="glass p-4 rounded-2xl border border-white/10 flex justify-between items-center group hover:bg-white/5 transition-all">
                                <div class="text-left">
                                    <p class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-0.5">Ngân hàng</p>
                                    <p class="font-black text-slate-200"><?php echo $order['bank_name']; ?></p>
                                </div>
                                <button onclick="copyText('<?php echo $order['bank_name']; ?>', this)" class="p-2 hover:text-yellow-500 transition-colors">
                                    <?php echo getIcon('copy', 'w-4 h-4'); ?>
                                </button>
                            </div>

                            <div class="glass p-4 rounded-2xl border border-white/10 flex justify-between items-center group hover:bg-white/5 transition-all">
                                <div class="text-left">
                                    <p class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-0.5">Số tài khoản</p>
                                    <p class="font-black text-slate-200 text-lg tracking-wider"><?php echo $order['account_no']; ?></p>
                                </div>
                                <button onclick="copyText('<?php echo $order['account_no']; ?>', this)" class="p-2 hover:text-yellow-500 transition-colors">
                                    <?php echo getIcon('copy', 'w-4 h-4'); ?>
                                </button>
                            </div>

                            <div class="glass p-4 rounded-2xl border-2 border-yellow-500/30 bg-yellow-500/5 flex justify-between items-center group hover:bg-yellow-500/10 transition-all">
                                <div class="text-left">
                                    <p class="text-[9px] text-yellow-500 font-black uppercase tracking-widest mb-0.5">Nội dung chuyển khoản</p>
                                    <p class="font-black text-yellow-500 text-xl tracking-widest"><?php echo $order['description']; ?></p>
                                </div>
                                <button onclick="copyText('<?php echo $order['description']; ?>', this)" class="p-3 bg-yellow-500 text-black rounded-xl hover:scale-105 transition-all">
                                    <?php echo getIcon('copy', 'w-4 h-4'); ?>
                                </button>
                            </div>

                            <div class="glass p-4 rounded-2xl border border-white/10 flex justify-between items-center group hover:bg-white/5 transition-all">
                                <div class="text-left">
                                    <p class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-0.5">Số tiền</p>
                                    <p class="font-black text-slate-200 text-lg"><?php echo formatMoney($order['amount']); ?></p>
                                </div>
                                <button onclick="copyText('<?php echo $order['amount']; ?>', this)" class="p-2 hover:text-yellow-500 transition-colors">
                                    <?php echo getIcon('copy', 'w-4 h-4'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="glass p-6 rounded-[2.5rem] border border-yellow-500/20 bg-yellow-500/5">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-yellow-500/20 rounded-lg text-yellow-500">
                                <?php echo getIcon('shield', 'w-4 h-4'); ?>
                            </div>
                            <p class="text-[11px] text-slate-400 leading-relaxed">
                                <strong class="text-yellow-500 uppercase tracking-wider block mb-1">Lưu ý:</strong>
                                Chuyển đúng <strong>nội dung</strong> và <strong>số tiền</strong>. Tiền sẽ vào tài khoản sau 1-3 phút.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                @keyframes scan {
                    0%, 100% { top: 16px; opacity: 0; }
                    10% { opacity: 1; }
                    90% { opacity: 1; }
                    100% { top: calc(100% - 16px); opacity: 0; }
                }
                .animate-scan {
                    animation: scan 3s linear infinite;
                }
            </style>

            <script>
                function copyText(text, btn) {
                    navigator.clipboard.writeText(text);
                    const original = btn.innerHTML;
                    btn.innerHTML = '<?php echo getIcon("check", "w-4 h-4 text-green-500"); ?>';
                    setTimeout(() => btn.innerHTML = original, 2000);
                }

                // Status Polling
                let orderId = '<?php echo $order['id']; ?>';
                let checkInterval = setInterval(async () => {
                    try {
                        const response = await fetch(`api/check-status.php?id=${orderId}`);
                        const data = await response.json();
                        
                        if (data.status === 'completed') {
                            document.getElementById('order-status').innerHTML = '<?php echo getIcon("check", "w-4 h-4 text-green-500"); ?> Thành công';
                            document.getElementById('current-balance').innerHTML = data.new_balance;
                            clearInterval(checkInterval);
                            setTimeout(() => window.location.href = 'dashboard.php', 3000);
                        } else if (data.status === 'cancelled') {
                            document.getElementById('order-status').innerHTML = '<?php echo getIcon("x", "w-4 h-4 text-red-500"); ?> Đã hủy';
                            clearInterval(checkInterval);
                        } else {
                            document.getElementById('order-status').innerHTML = '<svg class="animate-spin h-3 w-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang chờ...';
                        }
                    } catch (e) {
                        console.error('Status check failed');
                    }
                }, 1000);

                // Expiry Timer
                let timeLeft = 20 * 60;
                let timerInterval = setInterval(() => {
                    timeLeft--;
                    let mins = Math.floor(timeLeft / 60);
                    let secs = timeLeft % 60;
                    document.getElementById('expiry-timer').innerText = `${mins}:${secs < 10 ? '0' : ''}${secs}`;
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        document.getElementById('expiry-timer').innerText = 'Hết hạn';
                    }
                }, 1000);
            </script>
        <?php endif; ?>
    </main>
    <script src="../assets/js/transitions.js"></script>
    <script src="../assets/js/security.js"></script>
</body>
</html>
