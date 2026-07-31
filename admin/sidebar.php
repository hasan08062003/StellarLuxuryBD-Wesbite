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