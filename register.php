<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: user/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin.';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        $users = readJSON('users');
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $error = 'Tên đăng nhập đã tồn tại.';
                break;
            }
        }

        if (empty($error)) {
            $newUser = [
                'id' => generateID(),
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'balance' => 0,
                'role' => 'user',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $users[] = $newUser;
            writeJSON('users', $users);
            $_SESSION['user_id'] = $newUser['id'];
            $_SESSION['username'] = $newUser['username'];
            $_SESSION['role'] = $newUser['role'];
            header('Location: user/dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - TOOLTX2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0f172a] text-white min-h-screen flex items-center justify-center p-4">
    <div class="bg-white/5 backdrop-blur-lg p-8 rounded-2xl border border-white/10 w-full max-w-md">
        <div class="text-center mb-8">
            <img src="assets/images/logo-vip.png" alt="Logo" class="h-16 w-16 mx-auto mb-4 rounded-full border-2 border-yellow-500">
            <h2 class="text-3xl font-bold text-yellow-500">Đăng Ký</h2>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-200 p-3 rounded mb-4 text-sm">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Tên đăng nhập</label>
                <input type="text" name="username" required class="w-full bg-white/10 border border-white/20 rounded px-4 py-2 focus:outline-none focus:border-yellow-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Mật khẩu</label>
                <input type="password" name="password" required class="w-full bg-white/10 border border-white/20 rounded px-4 py-2 focus:outline-none focus:border-yellow-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" required class="w-full bg-white/10 border border-white/20 rounded px-4 py-2 focus:outline-none focus:border-yellow-500">
            </div>
            <button type="submit" class="w-full bg-yellow-500 text-black font-bold py-2 rounded hover:bg-yellow-400 transition-colors">ĐĂNG KÝ</button>
        </form>

        <p class="mt-6 text-center text-gray-400 text-sm">
            Đã có tài khoản? <a href="login.php" class="text-yellow-500 hover:underline">Đăng nhập ngay</a>
        </p>
    </div>
</body>
</html>
