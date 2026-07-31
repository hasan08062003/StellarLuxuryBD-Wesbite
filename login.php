<?php
include 'db.php'; // Database and Session Connection

// যদি ইউজার আগে থেকেই লগিন থাকে, তবে তাকে সরাসরি হোমে পাঠিয়ে দাও
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error_msg = "";
$success_msg = "";
$active_tab = isset($_GET['action']) ? $_GET['action'] : 'login';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_type = $_POST['form_type'];
    $email = $conn->real_escape_string(trim($_POST['email'])); // Email থেকে স্পেস মুছে ফেলা হলো

    // ==========================================
    // 1. REGISTRATION LOGIC (রেজিস্ট্রেশন সিস্টেম)
    // ==========================================
    if ($form_type == "register") {
        $name = $conn->real_escape_string(trim($_POST['name']));
        $password = $_POST['password'];

        // চেক করা হচ্ছে ইমেইলটি আগে থেকেই ডেটাবেসে আছে কিনা
        $check_email = $conn->query("SELECT id FROM users WHERE email='$email'");
        
        if ($check_email->num_rows > 0) {
            $error_msg = "This email is already registered! Please Login.";
            $active_tab = 'login'; // ইমেইল থাকলে তাকে লগিন ট্যাবে পাঠিয়ে দেবে
        } else {
            // পাসওয়ার্ডকে হ্যাশ (Secure) করে ডেটাবেসে সেভ করা হচ্ছে
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            if ($conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')")) {
                $success_msg = "Registration successful! You can now login.";
                $active_tab = 'login'; // রেজিস্ট্রেশন সাকসেস হলে লগিন ট্যাবে পাঠাবে
            } else {
                $error_msg = "Database Error! Please try again.";
                $active_tab = 'register';
            }
        }
    }

    // ==========================================
    // 2. STRICT LOGIN LOGIC (কঠোর লগিন সিস্টেম)
    // ==========================================
    if ($form_type == "login") {
        $password = $_POST['password'];

        // প্রথমে ডেটাবেসে ইমেইলটি খোঁজা হচ্ছে
        $result = $conn->query("SELECT * FROM users WHERE email='$email'");
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // ইমেইল পাওয়া গেছে, এবার পাসওয়ার্ড ম্যাচ করানো হচ্ছে
            if (password_verify($password, $user['password'])) {
                
                // সবকিছু ১০০% ম্যাচ করলে তবেই Session তৈরি হবে
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // সাকসেসফুল লগিন শেষে ড্যাশবোর্ডে পাঠানো হলো
                header("Location: index.php");
                exit; // Code execution এখানেই বন্ধ হয়ে যাবে
                
            } else {
                // পাসওয়ার্ড ভুল হলে এই এরর দেখাবে
                $error_msg = "Incorrect Password! Please try again.";
                $active_tab = 'login';
            }
        } else {
            // ইমেইল ডেটাবেসে না থাকলে এই এরর দেখাবে এবং Register ট্যাবে পাঠাবে
            $error_msg = "Account not found! Please register first.";
            $active_tab = 'register';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration - StellarLuxury BD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .glass-panel { background: rgba(11, 48, 34, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.15); }
        .glass-input { background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: white; }
        .glass-input::placeholder { color: rgba(255, 255, 255, 0.6); }
        .glass-input:focus { background: rgba(255, 255, 255, 0.2); border-color: #facc15; outline: none; }
    </style>
</head>
<body class="relative min-h-screen overflow-hidden font-sans">

    <!-- Video Background -->
    <div class="fixed inset-0 z-0 bg-[#0B3022]">
        <video autoplay loop muted playsinline class="w-full h-full object-cover opacity-200">
            <source src="images/bg-video.mp4" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/60"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <a href="index.php" class="absolute top-6 left-6 text-white/80 hover:text-white transition flex items-center gap-2 text-sm font-bold bg-black/30 px-4 py-2 rounded-full backdrop-blur-md">
            <i class="fas fa-arrow-left"></i> Back to Shop
        </a>

        <div class="glass-panel w-full max-w-md rounded-2xl shadow-[0_0_40px_rgba(0,0,0,0.5)] p-8 text-white">
            
            <div class="text-center mb-6">
                <h2 class="text-2xl font-extrabold tracking-wide">
                    StellarLuxury<span class="text-[10px] font-bold text-[#facc15] ml-1 uppercase align-top">BD</span>
                </h2>
            </div>

            <!-- Error / Success Messages Display -->
            <?php if($error_msg != ""): ?>
                <div class="bg-red-500/90 text-white text-sm font-bold px-4 py-3 rounded-lg mb-4 text-center border border-red-400 backdrop-blur-md shadow-lg">
                    <i class="fas fa-shield-alt mr-1"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>
            
            <?php if($success_msg != ""): ?>
                <div class="bg-green-500/90 text-white text-sm font-bold px-4 py-3 rounded-lg mb-4 text-center border border-green-400 backdrop-blur-md shadow-lg">
                    <i class="fas fa-check-circle mr-1"></i> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="flex border-b border-white/20 mb-6 text-xs md:text-sm font-bold">
                <button id="tab-login" onclick="switchTab('login')" class="flex-1 pb-3 border-b-2 transition-all <?php echo ($active_tab == 'login') ? 'border-[#facc15] text-[#facc15]' : 'border-transparent text-gray-400 hover:text-white'; ?>">Login</button>
                <button id="tab-register" onclick="switchTab('register')" class="flex-1 pb-3 border-b-2 transition-all <?php echo ($active_tab == 'register') ? 'border-[#facc15] text-[#facc15]' : 'border-transparent text-gray-400 hover:text-white'; ?>">Register</button>
            </div>

            <!-- 1. LOGIN FORM -->
            <form id="form-login" action="login.php" method="POST" class="<?php echo ($active_tab == 'login') ? 'block' : 'hidden'; ?>">
                <input type="hidden" name="form_type" value="login">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Email</label>
                        <input type="email" name="email" placeholder="you@example.com" class="glass-input w-full px-4 py-3 rounded-xl transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Password</label>
                        <input type="password" name="password" placeholder="••••••••" class="glass-input w-full px-4 py-3 rounded-xl transition" required>
                    </div>
                    <button type="submit" class="w-full bg-[#facc15] text-[#0B3022] font-extrabold py-3.5 rounded-xl hover:bg-white transition-all mt-2 shadow-lg">Sign In</button>
                </div>
            </form>

            <!-- 2. REGISTRATION FORM -->
            <form id="form-register" action="login.php" method="POST" class="<?php echo ($active_tab == 'register') ? 'block' : 'hidden'; ?>">
                <input type="hidden" name="form_type" value="register">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Full Name</label>
                        <input type="text" name="name" placeholder="John Doe" class="glass-input w-full px-4 py-3 rounded-xl transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Email</label>
                        <input type="email" name="email" placeholder="you@example.com" class="glass-input w-full px-4 py-3 rounded-xl transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Create Password</label>
                        <input type="password" name="password" placeholder="Minimum 6 characters" class="glass-input w-full px-4 py-3 rounded-xl transition" required minlength="6">
                    </div>
                    <button type="submit" class="w-full bg-[#facc15] text-[#0B3022] font-extrabold py-3.5 rounded-xl hover:bg-white transition-all mt-2 shadow-lg">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            document.getElementById('form-login').classList.replace('block', 'hidden');
            document.getElementById('form-register').classList.replace('block', 'hidden');
            document.getElementById('tab-login').classList.replace('border-[#facc15]', 'border-transparent');
            document.getElementById('tab-login').classList.replace('text-[#facc15]', 'text-gray-400');
            document.getElementById('tab-register').classList.replace('border-[#facc15]', 'border-transparent');
            document.getElementById('tab-register').classList.replace('text-[#facc15]', 'text-gray-400');

            document.getElementById('form-' + tabName).classList.replace('hidden', 'block');
            document.getElementById('tab-' + tabName).classList.replace('border-transparent', 'border-[#facc15]');
            document.getElementById('tab-' + tabName).classList.replace('text-gray-400', 'text-[#facc15]');
        }
    </script>
</body>
</html>