<?php
include '../db.php'; 
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$total_products = $total_orders = $pending_orders = $total_revenue = $total_users = 0;
$recent_products = [];
try {
    $prod_res = $conn->query("SELECT COUNT(*) AS count FROM products");
    if($prod_res) { $total_products = $prod_res->fetch_assoc()['count']; }
    $ord_res = $conn->query("SELECT COUNT(*) AS count FROM orders");
    if($ord_res) { $total_orders = $ord_res->fetch_assoc()['count']; }
    $pend_res = $conn->query("SELECT COUNT(*) AS count FROM orders WHERE status='Pending'");
    if($pend_res) { $pending_orders = $pend_res->fetch_assoc()['count']; }
    $rev_res = $conn->query("SELECT SUM(total_amount) AS revenue FROM orders WHERE status='Delivered'");
    if($rev_res) { $rev = $rev_res->fetch_assoc()['revenue']; $total_revenue = $rev ? $rev : 0; }
    $user_res = $conn->query("SELECT COUNT(*) AS count FROM users");
    if($user_res) { $total_users = $user_res->fetch_assoc()['count']; }
    $recent_res = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 5");
    if($recent_res) { while($row = $recent_res->fetch_assoc()) { $recent_products[] = $row; } }
} catch(Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>.custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; } .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }</style>
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
            <h1 class="text-xl font-extrabold text-gray-800"><i class="fas fa-home text-[#0B3022] mr-2"></i> Dashboard Overview</h1>
            <span class="text-gray-500 font-bold">Welcome, <span class="text-[#0B3022]"><?php echo $_SESSION['admin_name']; ?></span>!</span>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-6 mb-6">
                <!-- Mobile Grid optimized -->
                <div class="bg-white p-3 md:p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center gap-3 text-center md:text-left">
                    <div class="w-10 h-10 md:w-14 md:h-14 rounded-full md:rounded-xl bg-green-100 text-green-600 flex items-center justify-center text-lg md:text-2xl"><i class="fas fa-money-bill-wave"></i></div>
                    <div><p class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase">Revenue</p><h3 class="text-sm md:text-xl font-black text-gray-800">৳<?php echo number_format($total_revenue); ?></h3></div>
                </div>
                <div class="bg-white p-3 md:p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center gap-3 text-center md:text-left">
                    <div class="w-10 h-10 md:w-14 md:h-14 rounded-full md:rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg md:text-2xl"><i class="fas fa-shopping-bag"></i></div>
                    <div><p class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase">Orders</p><h3 class="text-sm md:text-xl font-black text-gray-800"><?php echo number_format($total_orders); ?></h3></div>
                </div>
                <div class="bg-white p-3 md:p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center gap-3 text-center md:text-left">
                    <div class="w-10 h-10 md:w-14 md:h-14 rounded-full md:rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg md:text-2xl"><i class="fas fa-clock"></i></div>
                    <div><p class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase">Pending</p><h3 class="text-sm md:text-xl font-black text-gray-800"><?php echo number_format($pending_orders); ?></h3></div>
                </div>
                <div class="bg-white p-3 md:p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center gap-3 text-center md:text-left">
                    <div class="w-10 h-10 md:w-14 md:h-14 rounded-full md:rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg md:text-2xl"><i class="fas fa-gem"></i></div>
                    <div><p class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase">Products</p><h3 class="text-sm md:text-xl font-black text-gray-800"><?php echo number_format($total_products); ?></h3></div>
                </div>
                <div class="bg-white p-3 md:p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center gap-3 text-center md:text-left col-span-2 md:col-span-1 lg:col-span-1">
                    <div class="w-10 h-10 md:w-14 md:h-14 rounded-full md:rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center text-lg md:text-2xl"><i class="fas fa-users"></i></div>
                    <div><p class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase">Total Users</p><h3 class="text-sm md:text-xl font-black text-gray-800"><?php echo number_format($total_users); ?></h3></div>
                </div>
            </div>

            <!-- Recent Products Table (Mobile swipeable) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-sm md:text-lg font-extrabold text-gray-800">Recent Products</h3>
                    <a href="products.php" class="text-xs font-bold text-blue-500">View All</a>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-500 text-[10px] uppercase font-extrabold">
                            <tr><th class="p-4">Item</th><th class="p-4">Price</th><th class="p-4">Status</th></tr>
                        </thead>
                        <tbody class="text-xs text-gray-700">
                            <?php if(count($recent_products) > 0): foreach($recent_products as $p): ?>
                                <tr class="border-b border-gray-50">
                                    <td class="p-3 flex items-center gap-2"><img src="../<?php echo !empty($p['image']) ? $p['image'] : 'images/placeholder.jpg'; ?>" class="w-8 h-8 object-cover rounded"><span class="font-bold truncate w-24 sm:w-auto"><?php echo htmlspecialchars($p['name']); ?></span></td>
                                    <td class="p-3 font-black text-[#0B3022]">৳<?php echo number_format($p['price']); ?></td>
                                    <td class="p-3 font-bold"><?php echo htmlspecialchars($p['stock_status']); ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="3" class="p-6 text-center text-gray-400">No products yet.</td></tr>
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