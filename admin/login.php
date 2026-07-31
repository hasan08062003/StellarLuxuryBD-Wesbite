<?php
// এরর হাইড করার জন্য (যাতে পেজের ডিজাইন নষ্ট না হয়)
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
session_start();

// ডেটাবেস ফাইল চেক করা হচ্ছে
$db_path = '../db.php';
if (file_exists($db_path)) {
    include $db_path;
} else {
    die("<div style='background:red; color:white; padding:10px; text-align:center; font-weight:bold;'>Error: db.php file is missing! Please make sure db.php is outside the admin folder.</div>");
}

// যদি আগে থেকেই লগিন করা থাকে, তাহলে ড্যাশবোর্ডে পাঠিয়ে দেবে
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php"); 
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // ডেটাবেসে অ্যাডমিন চেক করা হচ্ছে
    $result = $conn->query("SELECT * FROM admins WHERE email='$email' AND password='$password'");

    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - StellarLuxury BD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-[#0B3022] h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Background Subtle Animations -->
    <div class="absolute inset-0 pointer-events-none opacity-20">
        <div class="absolute top-[10%] left-[10%] w-[300px] h-[300px] bg-[#facc15] rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[10%] right-[10%] w-[400px] h-[400px] bg-[#072117] rounded-full blur-[120px]"></div>
    </div>

    <!-- Login Box -->
    <div class="bg-white w-full max-w-md rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] p-8 relative overflow-hidden z-10 border border-gray-100">
        
        <!-- Decoration -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-[#facc15] rounded-full opacity-20"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-[#0B3022] rounded-full opacity-10"></div>

        <div class="relative z-10">
            <!-- Logo & Title Section -->
            <div class="text-center mb-8">
                
                <!-- 🚀 তোমার নিজের লোগো এখানে বসানো হলো -->
                <div class="w-24 h-24 mx-auto flex items-center justify-center mb-4 bg-gray-50 rounded-full border-[3px] border-[#0B3022]/10 p-2 shadow-sm">
                    <!-- যদি logo.png না পায়, তাহলে ফলব্যাক হিসেবে মুকুট আইকন দেখাবে -->
                    <img src="../images/logo.png" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" alt="Logo" class="w-full h-full object-contain drop-shadow-md">
                    <i class="fas fa-crown text-4xl text-[#0B3022] hidden"></i>
                </div>
                
                <h2 class="text-2xl font-extrabold text-gray-800">Admin Panel</h2>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">StellarLuxury BD</p>
            </div>

            <!-- Error Message Display -->
            <?php if($error): ?>
                <div class="bg-red-50 text-red-500 p-3 rounded-xl text-sm font-bold mb-5 flex items-center gap-2 border border-red-200 animate-pulse">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-2 focus:ring-[#0B3022] focus:border-[#0B3022] block pl-10 p-3.5 transition outline-none" placeholder="admin@stellarluxury.bd" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-2 focus:ring-[#0B3022] focus:border-[#0B3022] block pl-10 p-3.5 transition outline-none" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="w-full text-[#0B3022] bg-[#facc15] hover:bg-[#eab308] font-extrabold rounded-xl text-sm px-5 py-4 text-center flex items-center justify-center gap-2 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                </button>
            </form>
        </div>
    </div>

</body>
</html>