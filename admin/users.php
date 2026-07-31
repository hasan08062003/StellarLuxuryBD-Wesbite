<?php
include '../db.php'; 

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// 🚀 Admin Coin Control Logic
if(isset($_POST['update_coins'])) {
    $u_id = $_POST['user_id'];
    $new_coins = $_POST['points'];
    $conn->query("UPDATE users SET points = '$new_coins' WHERE id = $u_id");
    $_SESSION['success'] = "User coins updated successfully!";
    header("Location: users.php");
    exit;
}

// 🚀 Admin User Tier Control Logic
if(isset($_POST['update_tier'])) {
    $u_id = $_POST['user_id'];
    $new_tier = $conn->real_escape_string($_POST['tier']);
    $conn->query("UPDATE users SET membership_tier = '$new_tier' WHERE id = $u_id");
    $_SESSION['success'] = "User Membership Tier updated to '{$new_tier}'!";
    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #0B3022; }
        select.custom-select { -webkit-appearance: none; -moz-appearance: none; appearance: none; }
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

    <!-- 🚀 MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <header class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-4 flex justify-between items-center z-10 hidden md:flex">
            <h1 class="text-xl font-extrabold text-gray-800"><i class="fas fa-users text-[#0B3022] mr-2"></i> Registered Users List</h1>
            <span class="text-gray-500 font-bold">Welcome, <span class="text-[#0B3022]"><?php echo $_SESSION['admin_name']; ?></span>!</span>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar pb-20 md:pb-6">
            
            <?php if(isset($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 font-bold flex justify-between items-center shadow-sm">
                    <span><i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success']; ?></span>
                    <button onclick="this.parentElement.style.display='none'" class="hover:text-green-900"><i class="fas fa-times"></i></button>
                </div>
            <?php unset($_SESSION['success']); endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 md:p-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm md:text-lg font-extrabold text-gray-800">Customer Database</h3>
                </div>
                
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[950px] whitespace-nowrap">
                        <thead class="bg-gray-100 text-gray-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <tr>
                                <th class="p-4 border-b">Customer Info & Role</th>
                                <th class="p-4 border-b text-center">Shipping Mark</th>
                                <th class="p-4 border-b">Contact & Address</th>
                                <th class="p-4 border-b">Coins Control</th>
                                <th class="p-4 border-b text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php
                            $users_query = $conn->query("SELECT * FROM users ORDER BY id DESC");
                            if($users_query->num_rows > 0):
                                while($user = $users_query->fetch_assoc()):
                                    $first_name = strtok($user['name'], " ");
                                    $shipping_mark = sprintf("%02d-%s", $user['id'], $first_name);
                                    
                                    $shipping = !empty($user['shipping_address']) ? $user['shipping_address'] : (!empty($user['address']) ? $user['address'] : 'No Address Provided');
                                    $points = isset($user['points']) ? $user['points'] : 0;
                                    $current_tier = !empty($user['membership_tier']) ? $user['membership_tier'] : 'New Member';
                            ?>
                            <tr class="hover:bg-gray-50 border-b border-gray-100 transition">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-[#0B3022] text-[#facc15] flex items-center justify-center font-bold text-lg shadow-sm">
                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-gray-800 text-sm mb-0.5"><?php echo htmlspecialchars($user['name']); ?></p>
                                            <p class="text-[11px] text-blue-600 font-bold mb-1"><?php echo htmlspecialchars($user['email']); ?></p>
                                            
                                            <!-- SMART DROPDOWN FOR USER TIER -->
                                            <form method="POST" action="">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="update_tier" value="1">
                                                <div class="relative inline-block w-40">
                                                    <i class="fas fa-crown absolute left-2 top-1/2 transform -translate-y-1/2 text-[#eab308] text-[10px]"></i>
                                                    <select name="tier" onchange="this.form.submit()" class="custom-select w-full bg-[#facc15]/20 text-[#0B3022] pl-6 pr-4 py-1 rounded-md font-extrabold uppercase border border-[#facc15]/50 text-[9px] md:text-[10px] outline-none cursor-pointer shadow-sm hover:bg-[#facc15]/40 transition focus:ring-1 focus:ring-[#0B3022]">
                                                        <option value="New Member" <?php if($current_tier == 'New Member' || $current_tier == 'None') echo 'selected'; ?>>New Member</option>
                                                        <option value="Regular Customer" <?php if($current_tier == 'Regular Customer') echo 'selected'; ?>>Regular Customer</option>
                                                        <option value="Reseller / Merchant" <?php if($current_tier == 'Reseller / Merchant' || $current_tier == 'Merchant') echo 'selected'; ?>>Reseller / Merchant</option>
                                                        <option value="VIP Customer" <?php if($current_tier == 'VIP Customer') echo 'selected'; ?>>VIP Customer</option>
                                                    </select>
                                                    <i class="fas fa-chevron-down absolute right-2 top-1/2 transform -translate-y-1/2 text-[#0B3022] text-[8px] pointer-events-none"></i>
                                                </div>
                                            </form>
                                            
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="p-4 text-center">
                                    <span class="bg-[#0B3022] text-[#facc15] px-3 py-1.5 rounded-lg font-black tracking-widest text-xs border border-[#facc15]/50 shadow-sm uppercase">
                                        <?php echo $shipping_mark; ?>
                                    </span>
                                </td>
                                
                                <td class="p-4">
                                    <p class="text-xs font-bold text-gray-700 mb-1"><i class="fas fa-phone-alt text-gray-400 mr-1"></i> <?php echo !empty($user['phone']) ? $user['phone'] : 'N/A'; ?></p>
                                    <p class="text-[11px] text-gray-500 w-48 truncate" title="<?php echo htmlspecialchars($shipping); ?>"><i class="fas fa-truck text-gray-400 mr-1"></i> <?php echo htmlspecialchars($shipping); ?></p>
                                </td>
                                
                                <td class="p-4">
                                    <form method="POST" action="" class="flex items-center gap-2">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <div class="relative w-28">
                                            <i class="fas fa-coins absolute left-2.5 top-1/2 transform -translate-y-1/2 text-yellow-500 text-xs"></i>
                                            <input type="number" name="points" value="<?php echo $points; ?>" class="w-full border border-gray-300 rounded-lg py-2 pl-7 pr-2 text-xs font-bold focus:border-[#0B3022] outline-none shadow-inner bg-gray-50">
                                        </div>
                                        <button type="submit" name="update_coins" class="bg-[#0B3022] text-[#facc15] px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#072117] transition shadow-md whitespace-nowrap"><i class="fas fa-save mr-1"></i> Save</button>
                                    </form>
                                </td>
                                
                                <td class="p-4 text-right">
                                    <a href="user_view.php?id=<?php echo $user['id']; ?>" class="inline-flex items-center bg-blue-50 text-blue-600 border border-blue-200 px-4 py-2 rounded-lg font-bold text-xs hover:bg-blue-600 hover:text-white transition shadow-sm">
                                        <i class="fas fa-eye mr-1.5"></i> Inspect
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5" class="p-8 text-center text-gray-400 font-bold"><i class="fas fa-users-slash text-3xl mb-3 block"></i> No users found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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