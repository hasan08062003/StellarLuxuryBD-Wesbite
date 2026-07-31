<?php
// db.php ফাইলের ভেতরেই session_start() আছে, তাই এখানে ডাবল করে লেখার দরকার নেই।
include '../db.php'; 

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// অফার টেক্সট আপডেট করার লজিক
if (isset($_POST['update_offer'])) {
    $offer_text = $conn->real_escape_string($_POST['offer_text']);
    
    // চেক করা হচ্ছে যে ডেটাবেসে আগে থেকে offer_text আছে কিনা
    $check = $conn->query("SELECT * FROM settings WHERE setting_key='offer_text'");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE settings SET setting_value='$offer_text' WHERE setting_key='offer_text'");
    } else {
        $conn->query("INSERT INTO settings (setting_key, setting_value) VALUES ('offer_text', '$offer_text')");
    }
    
    $_SESSION['success'] = "Offer text updated successfully on the live website!";
    header("Location: settings.php");
    exit;
}

// ডেটাবেস থেকে বর্তমান অফার টেক্সটটি নিয়ে আসা হচ্ছে
$current_offer = "";
$get_offer = $conn->query("SELECT setting_value FROM settings WHERE setting_key='offer_text'");
if ($get_offer && $get_offer->num_rows > 0) {
    $current_offer = $get_offer->fetch_assoc()['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #0B3022; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col md:flex-row h-screen overflow-hidden text-sm font-sans">

    <!-- 📱 MOBILE HEADER -->
    <div class="md:hidden bg-[#0B3022] text-white px-5 py-3 flex justify-between items-center shadow-md z-30 relative">
        <div class="flex items-center gap-2">
            <i class="fas fa-crown text-[#facc15] text-xl"></i>
            <h1 class="font-extrabold uppercase tracking-widest text-sm">Admin Panel</h1>
        </div>
        <button onclick="toggleSidebar()" class="text-[#facc15] focus:outline-none"><i class="fas fa-bars text-2xl"></i></button>
    </div>

    <!-- 📱 SIDEBAR OVERLAY -->
    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-40 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

    <!-- 🚀 UNIFIED FIXED SIDEBAR -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#0B3022] text-white flex flex-col shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0 h-full">
        <button onclick="toggleSidebar()" class="absolute top-4 right-4 text-gray-400 hover:text-white md:hidden"><i class="fas fa-times text-xl"></i></button>
        
        <div class="p-6 border-b border-white/10 text-center mt-6 md:mt-0">
            <div class="w-16 h-16 mx-auto flex items-center justify-center mb-3 bg-white rounded-full border-2 border-[#facc15] shadow-lg p-1">
                <img src="../images/logo.png" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" alt="Logo" class="w-full h-full object-contain rounded-full">
                <i class="fas fa-crown text-2xl text-[#0B3022] hidden"></i>
            </div>
            <h2 class="text-lg font-extrabold tracking-widest uppercase">Admin Panel</h2>
            <p class="text-[10px] text-[#facc15] font-bold tracking-wider">StellarLuxury BD</p>
        </div>
        
        <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto custom-scrollbar">
            <?php $page = basename($_SERVER['PHP_SELF']); ?>
            
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo $page == 'index.php' ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-chart-pie w-5"></i> Dashboard</a>
            
            <a href="users.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo ($page == 'users.php' || $page == 'user_view.php') ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-users w-5"></i> User List</a>
            
            <a href="products.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo $page == 'products.php' ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-box-open w-5"></i> Products</a>
            
            <a href="orders.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo $page == 'orders.php' ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-shopping-cart w-5"></i> Orders</a>
            
            <a href="payment_requests.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo $page == 'payment_requests.php' ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-money-check-alt w-5"></i> Payments</a>
            
            <a href="settings.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo $page == 'settings.php' ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-cog w-5"></i> Settings</a>
        </nav>
        
        <div class="p-4 border-t border-white/10 bg-[#072117]">
            <a href="logout.php" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-red-500/10 border border-red-500 hover:bg-red-500 text-red-500 hover:text-white font-bold rounded-lg transition"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <header class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-4 flex justify-between items-center z-10 hidden md:flex">
            <h1 class="text-xl font-extrabold text-gray-800"><i class="fas fa-cog text-[#0B3022] mr-2"></i> Website Settings</h1>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 font-bold flex items-center justify-between shadow-sm">
                    <span><i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success']; ?></span>
                    <button onclick="this.parentElement.style.display='none'" class="hover:text-green-900"><i class="fas fa-times"></i></button>
                </div>
            <?php unset($_SESSION['success']); endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 max-w-3xl">
                <h3 class="text-sm md:text-lg font-extrabold text-gray-800 mb-2 border-b pb-3 flex items-center gap-2">
                    <i class="fas fa-bullhorn text-[#facc15]"></i> Top Banner Offer Text (News Ticker)
                </h3>
                <p class="text-[10px] md:text-xs text-gray-500 mb-5 leading-relaxed">
                    Write your promotional offers below. To show multiple offers dynamically (Typewriter effect), separate them with a <strong class="text-red-500 text-sm font-black mx-1">|</strong> (pipe) symbol. <br>
                    <span class="italic opacity-80">Example: 🔥 50% Off Today! | 🚚 Free Shipping all over Bangladesh!</span>
                </p>
                
                <form method="POST" action="">
                    <div class="mb-5">
                        <textarea name="offer_text" rows="4" required class="w-full border border-gray-200 rounded-xl p-4 focus:ring-2 focus:ring-[#0B3022] focus:border-transparent outline-none bg-gray-50 font-medium text-gray-700 shadow-inner resize-y"><?php echo htmlspecialchars($current_offer); ?></textarea>
                    </div>
                    <button type="submit" name="update_offer" class="w-full md:w-auto bg-[#0B3022] text-[#facc15] px-6 py-3 rounded-xl font-extrabold shadow-md hover:bg-[#072117] transition flex justify-center items-center gap-2">
                        <i class="fas fa-cloud-upload-alt"></i> Save Changes to Live Website
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.toggle('hidden');
        }
    </script>
</body>
</html>