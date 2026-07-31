<?php
include '../db.php'; 

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// ==========================================
// 🛠 ADMIN ACTION LOGIC (PAYMENT APPROVAL / STATUS UPDATE)
// ==========================================
if(isset($_POST['update_order_status'])) {
    $order_db_id = (int)$_POST['order_db_id'];
    $new_ord_status = $conn->real_escape_string($_POST['order_status']);
    $new_pay_status = $conn->real_escape_string($_POST['payment_status']);
    
    $conn->query("UPDATE orders SET order_status = '$new_ord_status', payment_status = '$new_pay_status' WHERE id = $order_db_id");
    $_SESSION['success'] = "Order updated successfully!";
    header("Location: orders.php");
    exit;
}

// ==========================================
// 📄 PAGINATION & SEARCH LOGIC (50 Items Per Page)
// ==========================================
$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$search_query = "";
$search_sql = "";
if(isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = trim($_GET['search']);
    $escaped_search = $conn->real_escape_string($search_query);
    $search_sql = "WHERE orders.order_id LIKE '%$escaped_search%' OR users.name LIKE '%$escaped_search%' OR users.phone LIKE '%$escaped_search%'";
}

// Total records for pagination
$total_sql = "SELECT COUNT(orders.id) as total FROM orders JOIN users ON orders.user_id = users.id $search_sql";
$total_res = $conn->query($total_sql);
$total_row = $total_res->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);
if($total_pages < 1) $total_pages = 1;

// If requested page exceeds total pages, reset to max page
if($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

// Fetch orders with user details
$orders_sql = "SELECT orders.*, users.name as user_name, users.phone as user_phone, users.email as user_email 
               FROM orders 
               JOIN users ON orders.user_id = users.id 
               $search_sql 
               ORDER BY orders.id DESC 
               LIMIT $limit OFFSET $offset";
$orders_res = $conn->query($orders_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Manager - Admin Panel</title>
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

    <!-- 🚀 FIXED SIDEBAR -->
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
            <?php $page_current = basename($_SERVER['PHP_SELF']); ?>
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo $page_current == 'index.php' ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-chart-pie w-5"></i> Dashboard</a>
            <a href="users.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo $page_current == 'users.php' ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-users w-5"></i> User List</a>
            <a href="products.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo $page_current == 'products.php' ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-box-open w-5"></i> Products</a>
            <a href="orders.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo $page_current == 'orders.php' ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-shopping-cart w-5"></i> Orders</a>
            <a href="payment_requests.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo $page_current == 'payment_requests.php' ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-money-check-alt w-5"></i> Payments</a>
            <a href="settings.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium <?php echo $page_current == 'settings.php' ? 'bg-[#facc15] text-[#0B3022] font-bold shadow-md transform scale-105' : 'text-gray-300 hover:bg-white/10 hover:text-white'; ?>"><i class="fas fa-cog w-5"></i> Settings</a>
        </nav>
        
        <div class="p-4 border-t border-white/10 bg-[#072117]">
            <a href="logout.php" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-red-500/10 border border-red-500 hover:bg-red-500 text-red-500 hover:text-white font-bold rounded-lg transition"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <!-- 🚀 MAIN CONTENT -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <header class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3 z-10">
            <h1 class="text-lg md:text-xl font-extrabold text-gray-800">
                <i class="fas fa-shopping-cart text-[#0B3022] mr-2"></i> Orders Manager (Total: <?php echo $total_records; ?>)
            </h1>
            
            <!-- 🔍 Search Order ID / User System -->
            <form action="orders.php" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search Order ID, Name, Phone..." class="bg-gray-50 border border-gray-300 rounded-lg px-3 py-1.5 text-xs font-semibold focus:outline-none focus:border-[#0B3022] w-full sm:w-64">
                <button type="submit" class="bg-[#0B3022] text-[#facc15] px-4 py-1.5 rounded-lg font-bold text-xs hover:bg-[#072117] transition shadow-xs flex-shrink-0">
                    <i class="fas fa-search"></i>
                </button>
                <?php if(!empty($search_query)): ?>
                    <a href="orders.php" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg font-bold text-xs hover:bg-gray-300 transition">Reset</a>
                <?php endif; ?>
            </form>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar pb-24 md:pb-6">
            
            <?php if(isset($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 font-bold flex justify-between shadow-sm text-xs">
                    <span><i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success']; ?></span>
                    <button onclick="this.parentElement.style.display='none'"><i class="fas fa-times"></i></button>
                </div>
            <?php unset($_SESSION['success']); endif; ?>

            <!-- Orders Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[950px] whitespace-nowrap">
                        <thead class="bg-gray-100 text-gray-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <tr>
                                <th class="p-4 border-b">Date & Order ID</th>
                                <th class="p-4 border-b">User Info</th>
                                <th class="p-4 border-b">Total Cost</th>
                                <th class="p-4 border-b">Payment Status</th>
                                <th class="p-4 border-b">Order Details</th>
                                <th class="p-4 border-b">Invoice</th>
                                <th class="p-4 border-b text-right">Update</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-xs font-medium">
                            <?php 
                            if($orders_res && $orders_res->num_rows > 0):
                                while($ord = $orders_res->fetch_assoc()):
                                    $db_id = $ord['id'];
                            ?>
                            <tr class="hover:bg-gray-50 border-b border-gray-100 transition">
                                <td class="p-4">
                                    <p class="font-extrabold text-[#0B3022]"><?php echo htmlspecialchars($ord['order_id']); ?></p>
                                    <p class="text-[10px] text-gray-400 font-bold mt-0.5"><?php echo date('d M Y, h:i A', strtotime($ord['created_at'])); ?></p>
                                </td>
                                <td class="p-4">
                                    <p class="font-black text-gray-800"><?php echo htmlspecialchars($ord['user_name']); ?></p>
                                    <p class="text-[10px] text-gray-500 font-bold"><i class="fas fa-phone-alt mr-1"></i><?php echo htmlspecialchars($ord['user_phone'] ? $ord['user_phone'] : 'N/A'); ?></p>
                                    <p class="text-[10px] text-gray-400 truncate w-48" title="<?php echo htmlspecialchars($ord['shipping_address']); ?>"><i class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($ord['shipping_address']); ?></p>
                                </td>
                                <td class="p-4">
                                    <p class="font-black text-gray-800 text-sm">৳ <?php echo number_format($ord['total_amount']); ?></p>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase">(Incl. Shipping)</span>
                                </td>
                                <td class="p-4">
                                    <?php 
                                        $p_stat = strtolower($ord['payment_status']);
                                        if($p_stat == 'approved' || $p_stat == 'paid' || $p_stat == 'received') {
                                            echo '<span class="bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-md text-[10px] font-black uppercase"><i class="fas fa-check-circle mr-1"></i> Approved</span>';
                                        } else {
                                            echo '<span class="bg-orange-50 text-orange-600 border border-orange-200 px-2.5 py-1 rounded-md text-[10px] font-black uppercase"><i class="fas fa-clock mr-1"></i> Pending Payment</span>';
                                        }
                                    ?>
                                </td>
                                <td class="p-4">
                                    <button onclick="openDetailsModal(<?php echo $db_id; ?>)" class="bg-[#0B3022] text-[#facc15] px-3 py-1.5 rounded-lg font-extrabold hover:bg-[#072117] transition shadow-xs text-[11px]">
                                        <i class="fas fa-eye mr-1"></i> Show
                                    </button>
                                </td>
                                <td class="p-4">
                                    <a href="invoice.php?id=<?php echo $db_id; ?>" target="_blank" class="bg-gray-100 text-gray-700 border border-gray-300 px-3 py-1.5 rounded-lg font-bold hover:bg-gray-200 transition shadow-xs text-[11px] inline-flex items-center gap-1">
                                        <i class="fas fa-file-invoice"></i> View
                                    </a>
                                </td>
                                <td class="p-4 text-right">
                                    <button onclick="openEditModal(<?php echo $db_id; ?>, '<?php echo $ord['order_status']; ?>', '<?php echo $ord['payment_status']; ?>')" class="bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded-lg font-bold hover:bg-blue-600 hover:text-white transition shadow-xs text-[11px]">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <tr><td colspan="7" class="p-12 text-center text-gray-400 font-bold"><i class="fas fa-shopping-cart text-3xl mb-3 block opacity-50"></i> No orders found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 📄 ADVANCED PAGINATION CONTROLS           -->
            <!-- ========================================== -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-gray-500 font-bold">
                    Showing Page <span class="text-[#0B3022] font-black"><?php echo $page; ?></span> of <span class="text-[#0B3022] font-black"><?php echo $total_pages; ?></span> (Total Orders: <?php echo $total_records; ?>)
                </p>

                <div class="flex items-center gap-2 flex-wrap">
                    <?php if($page > 1): ?>
                        <a href="orders.php?page=<?php echo ($page - 1); ?><?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>" class="bg-gray-100 hover:bg-[#0B3022] hover:text-[#facc15] text-gray-700 font-bold px-3 py-1.5 rounded-lg text-xs transition border border-gray-200"><i class="fas fa-angle-left mr-1"></i> Previous</a>
                    <?php endif; ?>

                    <!-- Direct Page Jump Input -->
                    <form action="orders.php" method="GET" class="flex items-center gap-1">
                        <?php if(!empty($search_query)): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                        <?php endif; ?>
                        <span class="text-xs text-gray-500 font-bold">Go to:</span>
                        <input type="number" name="page" min="1" max="<?php echo $total_pages; ?>" value="<?php echo $page; ?>" class="w-14 bg-gray-50 border border-gray-300 rounded text-center text-xs font-bold py-1 focus:outline-none focus:border-[#0B3022]">
                        <button type="submit" class="bg-[#0B3022] text-[#facc15] px-2.5 py-1 rounded text-xs font-extrabold">Go</button>
                    </form>

                    <?php if($page < $total_pages): ?>
                        <a href="orders.php?page=<?php echo ($page + 1); ?><?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>" class="bg-gray-100 hover:bg-[#0B3022] hover:text-[#facc15] text-gray-700 font-bold px-3 py-1.5 rounded-lg text-xs transition border border-gray-200">Next <i class="fas fa-angle-right ml-1"></i></a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

    <!-- ================================================= -->
    <!-- 📦 ORDER DETAILS POPUP MODAL                      -->
    <!-- ================================================= -->
    <div id="detailsModal" class="fixed inset-0 bg-black/70 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col border border-gray-200">
            <div class="bg-[#0B3022] text-[#facc15] px-5 py-4 flex justify-between items-center">
                <h3 class="font-black text-base flex items-center gap-2"><i class="fas fa-box"></i> Order Details: <span id="modalOrderId" class="text-white"></span></h3>
                <button onclick="closeDetailsModal()" class="text-white hover:text-[#facc15] text-xl font-bold focus:outline-none"><i class="fas fa-times"></i></button>
            </div>
            
            <div id="modalBodyContent" class="p-6 overflow-y-auto custom-scrollbar space-y-4 text-xs">
                <div class="text-center py-10 text-gray-400 font-bold"><i class="fas fa-spinner fa-spin text-2xl mb-2"></i> Loading details...</div>
            </div>

            <div class="bg-gray-50 border-t border-gray-200 px-5 py-3 text-right">
                <button onclick="closeDetailsModal()" class="bg-gray-800 text-white font-bold px-5 py-2 rounded-xl text-xs hover:bg-gray-900 transition">Close</button>
            </div>
        </div>
    </div>

    <!-- ================================================= -->
    <!-- 🛠 UPDATE / EDIT POPUP MODAL                      -->
    <!-- ================================================= -->
    <div id="editModal" class="fixed inset-0 bg-black/70 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden flex flex-col border border-gray-200">
            <div class="bg-[#0B3022] text-[#facc15] px-5 py-4 flex justify-between items-center">
                <h3 class="font-black text-base flex items-center gap-2"><i class="fas fa-edit"></i> Update Order Status</h3>
                <button onclick="closeEditModal()" class="text-white hover:text-[#facc15] text-xl font-bold focus:outline-none"><i class="fas fa-times"></i></button>
            </div>
            
            <form action="orders.php" method="POST" class="p-6 space-y-4 text-xs">
                <input type="hidden" name="order_db_id" id="editOrderDbId">
                
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Order Status</label>
                    <select name="order_status" id="editOrderStatus" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 font-bold text-gray-800 focus:outline-none focus:border-[#0B3022]">
                        <option value="to_pay">To Pay</option>
                        <option value="to_ship">To Ship</option>
                        <option value="to_receive">To Receive</option>
                        <option value="to_review">To Review</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Payment Status</label>
                    <select name="payment_status" id="editPaymentStatus" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 font-bold text-gray-800 focus:outline-none focus:border-[#0B3022]">
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-200 text-gray-700 font-bold px-4 py-2 rounded-xl">Cancel</button>
                    <button type="submit" name="update_order_status" class="bg-[#0B3022] text-[#facc15] font-extrabold px-6 py-2 rounded-xl shadow-md hover:bg-[#072117]">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- AJAX Script for Modals -->
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.toggle('hidden');
        }

        function openDetailsModal(orderDbId) {
            document.getElementById('detailsModal').classList.remove('hidden');
            document.getElementById('modalOrderId').innerText = '#' + orderDbId;
            document.getElementById('modalBodyContent').innerHTML = '<div class="text-center py-10 text-gray-400 font-bold"><i class="fas fa-spinner fa-spin text-2xl mb-2"></i> Loading details...</div>';

            fetch('get_order_details.php?id=' + orderDbId)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalBodyContent').innerHTML = html;
                })
                .catch(err => {
                    document.getElementById('modalBodyContent').innerHTML = '<p class="text-red-500 font-bold text-center">Failed to load order details.</p>';
                });
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }

        function openEditModal(dbId, ordStatus, payStatus) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editOrderDbId').value = dbId;
            document.getElementById('editOrderStatus').value = ordStatus;
            document.getElementById('editPaymentStatus').value = payStatus;
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>