<?php
include '../db.php'; 

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// 🚀 1. DELETE PRODUCT LOGIC
if(isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM product_color_variants WHERE product_id = $del_id");
    $conn->query("DELETE FROM products WHERE id = $del_id");
    $_SESSION['success'] = "Product and its variants deleted successfully!";
    header("Location: products.php"); 
    exit;
}

// 🚀 2. AJAX / DIRECT STATUS UPDATE LOGIC
if(isset($_POST['update_status']) && isset($_POST['product_id'])) {
    $p_id = (int)$_POST['product_id'];
    $new_status = $conn->real_escape_string($_POST['stock_status']);
    
    $conn->query("UPDATE products SET stock_status = '$new_status' WHERE id = $p_id");
    $_SESSION['success'] = "Product stock status updated successfully!";
    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Manager - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
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
            <a href="products.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo ($page == 'products.php' || $page == 'add_variant_product.php') ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-box-open w-5"></i> Products</a>
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
        <header class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-4 flex justify-between items-center z-10">
            <h1 class="text-lg md:text-xl font-extrabold text-gray-800">
                <i class="fas fa-box-open text-[#0B3022] mr-2"></i> Product Inventory
            </h1>
            <a href="add_variant_product.php" class="bg-[#0B3022] text-[#facc15] px-4 py-2 rounded-lg font-extrabold hover:bg-[#072117] transition shadow-md flex items-center gap-2 text-xs md:text-sm">
                <i class="fas fa-plus-circle"></i> Add New Product
            </a>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar pb-24 md:pb-6">
            
            <?php if(isset($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 font-bold flex justify-between shadow-sm">
                    <span><i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success']; ?></span>
                    <button onclick="this.parentElement.style.display='none'"><i class="fas fa-times"></i></button>
                </div>
            <?php unset($_SESSION['success']); endif; ?>

            <div class="md:hidden mb-4">
                <a href="add_variant_product.php" class="w-full bg-[#0B3022] text-[#facc15] px-5 py-3 rounded-xl font-extrabold shadow-md flex justify-center items-center gap-2 text-sm">
                    <i class="fas fa-plus-circle"></i> Add New Product
                </a>
            </div>

            <!-- ============================================== -->
            <!-- 📦 PRODUCTS LIST VIEW MODE                      -->
            <!-- ============================================== -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[850px] whitespace-nowrap">
                        <thead class="bg-gray-100 text-gray-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <tr>
                                <th class="p-4 border-b">ID & Image</th>
                                <th class="p-4 border-b">Product Name & Cat.</th>
                                <th class="p-4 border-b">Price Info</th>
                                <th class="p-4 border-b">Variants</th>
                                <th class="p-4 border-b">Stock Status (Update)</th>
                                <th class="p-4 border-b text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php
                            $products_query = $conn->query("SELECT * FROM products ORDER BY id DESC");
                            if($products_query->num_rows > 0):
                                while($row = $products_query->fetch_assoc()):
                                    $p_id = $row['id'];
                                    $var_count = $conn->query("SELECT COUNT(*) as cnt FROM product_color_variants WHERE product_id = $p_id")->fetch_assoc()['cnt'];
                                    $current_status = strtolower(trim($row['stock_status']));
                            ?>
                            <tr class="hover:bg-gray-50 border-b border-gray-100 transition">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-black text-gray-400">#<?php echo $p_id; ?></span>
                                        <img src="../<?php echo !empty($row['image']) ? $row['image'] : 'images/placeholder.jpg'; ?>" class="w-12 h-12 rounded object-cover border shadow-sm">
                                    </div>
                                </td>
                                <td class="p-4">
                                    <p class="font-extrabold text-[#0B3022] text-sm truncate w-48"><?php echo htmlspecialchars($row['name']); ?></p>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase mt-0.5"><?php echo htmlspecialchars($row['category']); ?> <?php echo !empty($row['material']) ? '• '.$row['material'] : ''; ?></p>
                                </td>
                                <td class="p-4">
                                    <p class="font-black text-gray-800 text-base">৳ <?php echo number_format($row['price']); ?></p>
                                    <?php if($row['old_price'] > 0): ?>
                                    <p class="text-[10px] text-red-400 font-bold line-through">৳ <?php echo number_format($row['old_price']); ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <?php if($var_count > 0): ?>
                                        <span class="bg-purple-50 text-purple-700 border border-purple-200 px-2.5 py-1 rounded-md text-[10px] font-extrabold"><i class="fas fa-palette mr-1"></i> <?php echo $var_count; ?> Colors</span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs italic">No Variants</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <!-- 🚀 Live Status Dropdown Update Form -->
                                    <form action="products.php" method="POST" class="inline-flex items-center">
                                        <input type="hidden" name="product_id" value="<?php echo $p_id; ?>">
                                        <select name="stock_status" onchange="this.form.submit()" class="bg-white border-2 <?php echo in_array($current_status, ['out of stock', 'not available']) ? 'border-red-300 text-red-600 bg-red-50' : (in_array($current_status, ['pre-order', 'preorder']) ? 'border-orange-300 text-orange-600 bg-orange-50' : 'border-green-300 text-green-700 bg-green-50'); ?> font-bold text-xs rounded-xl px-3 py-1.5 focus:outline-none shadow-xs cursor-pointer transition">
                                            <option value="In Stock" <?php echo !in_array($current_status, ['out of stock', 'not available', 'pre-order', 'preorder']) ? 'selected' : ''; ?>>In Stock</option>
                                            <option value="Out of Stock" <?php echo in_array($current_status, ['out of stock', 'not available']) ? 'selected' : ''; ?>>Out of Stock</option>
                                            <option value="Pre-order" <?php echo in_array($current_status, ['pre-order', 'preorder']) ? 'selected' : ''; ?>>Pre-order</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="add_variant_product.php?edit=<?php echo $p_id; ?>" class="inline-block bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-600 hover:text-white transition shadow-sm mr-1">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo $p_id; ?>" onclick="return confirm('Are you sure you want to delete this product?');" class="inline-block bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-600 hover:text-white transition shadow-sm">
                                        <i class="fas fa-trash-alt mr-1"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="6" class="p-10 text-center text-gray-400 font-bold"><i class="fas fa-box-open text-3xl mb-3 block opacity-50"></i> No products found.</td></tr>
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