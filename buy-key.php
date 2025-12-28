<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$packages = [
    ['id' => 1, 'name' => '1 Giờ', 'price' => 5000, 'icon' => 'rocket'],
    ['id' => 2, 'name' => '10 Giờ', 'price' => 10000, 'icon' => 'rocket'],
    ['id' => 3, 'name' => '1 Ngày', 'price' => 20000, 'icon' => 'rocket'],
    ['id' => 4, 'name' => '3 Ngày', 'price' => 45000, 'icon' => 'rocket'],
    ['id' => 5, 'name' => '7 Ngày', 'price' => 80000, 'icon' => 'rocket'],
    ['id' => 6, 'name' => '1 Tháng', 'price' => 120000, 'icon' => 'rocket'],
    ['id' => 7, 'name' => 'Vĩnh Viễn', 'price' => 250000, 'icon' => 'rocket'],
];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $package_id = (int)$_POST['package_id'];
    $quantity = (int)$_POST['quantity'];
    
    $selectedPackage = null;
    foreach ($packages as $p) {
        if ($p['id'] === $package_id) {
            $selectedPackage = $p;
            break;
        }
    }

    if (!$selectedPackage) {
        $error = 'Gói key không hợp lệ.';
    } elseif ($quantity < 1) {
        $error = 'Số lượng phải lớn hơn 0.';
    } else {
        $total_price = $selectedPackage['price'] * $quantity;
        $discount = 0;
        if ($quantity >= 10) {
            $discount = 0.35;
        } elseif ($quantity >= 6) {
            $discount = 0.25;
        } elseif ($quantity >= 3) {
            $discount = 0.15;
        }
        
        $final_price = $total_price * (1 - $discount);
        
        $users = readJSON('users');
        $userIndex = -1;
        foreach ($users as $index => $user) {
            if ($user['id'] === $_SESSION['user_id']) {
                $userIndex = $index;
                break;
            }
        }

        if ($userIndex !== -1) {
            if ($users[$userIndex]['balance'] < $final_price) {
                $error = 'Số dư không đủ. Vui lòng nạp thêm tiền.';
            } else {
                $users[$userIndex]['balance'] -= $final_price;
                writeJSON('users', $users);
                
                $keys = readJSON('keys');
                $newKey = [
                    'id' => generateID('KEY'),
                    'user_id' => $_SESSION['user_id'],
                    'package_name' => $selectedPackage['name'],
                    'quantity' => $quantity,
                    'total_price' => $final_price,
                    'key_code' => strtoupper(generateRandomString(12)),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $keys[] = $newKey;
                writeJSON('keys', $keys);
                
                $success = "Mua thành công {$quantity} key gói {$selectedPackage['name']}.";
                $newKeyCode = $newKey['key_code'];
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
    <title>Mua Key - TOOLTX2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <nav class="p-4 glass border-b border-white/5 flex justify-between items-center px-6">
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="flex items-center gap-2">
                <div class="p-1 bg-gradient-to-br from-yellow-400 to-orange-600 rounded-lg">
                    <img src="../assets/images/logo-vip.png" alt="Logo" class="h-8 w-8 rounded-md bg-black">
                </div>
                <span class="text-xl font-black tracking-tighter text-gradient">TOOLTX2026</span>
            </a>
        </div>
        <a href="dashboard.php" class="text-sm font-bold text-slate-400 hover:text-white flex items-center gap-2 transition-colors">
            <?php echo getIcon('home', 'w-5 h-5'); ?>
            Quay lại
        </a>
    </nav>

    <main class="p-6 max-w-6xl mx-auto w-full mt-8">
        <div class="flex items-center gap-4 mb-8">
            <div class="p-3 bg-orange-500/10 rounded-2xl text-orange-500">
                <?php echo getIcon('key', 'w-8 h-8'); ?>
            </div>
            <div>
                <h2 class="text-3xl font-black">Mua Key Kích Hoạt</h2>
                <p class="text-sm text-slate-400">Chọn gói thời gian phù hợp để bắt đầu sử dụng tool</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-2xl mb-6 text-sm flex items-center gap-3">
                <?php echo getIcon('x', 'w-5 h-5'); ?>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="glass p-8 rounded-3xl border-green-500/30 mb-8 text-center">
                <div class="w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center text-green-500 mx-auto mb-4">
                    <?php echo getIcon('check', 'w-8 h-8'); ?>
                </div>
                <h3 class="text-xl font-black text-green-500 mb-2"><?php echo $success; ?></h3>
                <p class="text-slate-400 text-sm mb-6">Mã key của bạn đã được tạo thành công:</p>
                <div class="bg-white/5 border border-white/10 p-4 rounded-2xl font-mono text-2xl text-yellow-500 tracking-widest mb-6">
                    <?php echo $newKeyCode; ?>
                </div>
                <p class="text-xs text-slate-500 italic">Vui lòng sao chép và lưu lại mã key này.</p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($packages as $p): ?>
                    <div class="glass p-6 rounded-3xl border border-white/5 hover:border-yellow-500/30 transition-all group relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <?php echo getIcon($p['icon'], 'w-24 h-24'); ?>
                        </div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="p-3 bg-white/5 rounded-2xl text-yellow-500 group-hover:bg-yellow-500 group-hover:text-black transition-all">
                                <?php echo getIcon($p['icon'], 'w-6 h-6'); ?>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Gói Tool</span>
                        </div>
                        <h3 class="text-xl font-black mb-1"><?php echo $p['name']; ?></h3>
                        <div class="text-2xl font-black text-gradient mb-6"><?php echo formatMoney($p['price']); ?></div>
                        
                        <form method="POST" class="flex gap-2">
                            <input type="hidden" name="package_id" value="<?php echo $p['id']; ?>">
                            <div class="flex-1 relative">
                                <input type="number" name="quantity" value="1" min="1" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-yellow-500">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-600">SL</span>
                            </div>
                            <button type="submit" class="bg-white/10 hover:bg-yellow-500 hover:text-black px-4 py-2 rounded-xl text-xs font-black transition-all">MUA</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="space-y-6">
                <div class="glass p-8 rounded-3xl border-l-4 border-blue-500">
                    <h3 class="text-lg font-black mb-4 flex items-center gap-2">
                        <span class="text-blue-500"><?php echo getIcon('check', 'w-5 h-5'); ?></span>
                        Ưu Đãi Giảm Giá
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-2xl">
                            <span class="text-sm font-bold text-slate-400">Mua từ 3 key</span>
                            <span class="text-sm font-black text-green-500">-15%</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-2xl">
                            <span class="text-sm font-bold text-slate-400">Mua từ 6 key</span>
                            <span class="text-sm font-black text-green-500">-25%</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-2xl">
                            <span class="text-sm font-bold text-slate-400">Mua từ 10 key</span>
                            <span class="text-sm font-black text-green-500">-35%</span>
                        </div>
                    </div>
                    <p class="mt-6 text-[11px] text-slate-500 leading-relaxed italic">
                        * Hệ thống tự động áp dụng giảm giá khi bạn thay đổi số lượng mua.
                    </p>
                </div>

                <div class="glass p-8 rounded-3xl">
                    <h3 class="text-lg font-black mb-4 flex items-center gap-2">
                        <span class="text-yellow-500"><?php echo getIcon('history', 'w-5 h-5'); ?></span>
                        Lưu Ý
                    </h3>
                    <ul class="space-y-3">
                        <li class="text-xs text-slate-400 flex gap-2">
                            <span class="text-yellow-500">•</span>
                            Key có hiệu lực ngay sau khi mua.
                        </li>
                        <li class="text-xs text-slate-400 flex gap-2">
                            <span class="text-yellow-500">•</span>
                            Mỗi key chỉ sử dụng cho 1 tài khoản.
                        </li>
                        <li class="text-xs text-slate-400 flex gap-2">
                            <span class="text-yellow-500">•</span>
                            Không hoàn tiền sau khi đã tạo key.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
