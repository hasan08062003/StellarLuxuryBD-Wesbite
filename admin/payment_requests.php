<?php
include '../db.php'; 
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

// Status Update & Order Sync Logic
if(isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $req = $conn->query("SELECT order_id FROM payment_requests WHERE id=$id")->fetch_assoc();
    if($req && !empty($req['order_id'])) {
        $order_id = $req['order_id'];
        $conn->query("UPDATE orders SET order_status = 'to_ship', payment_status = 'Partial Paid' WHERE order_id = '$order_id'");
    }
    $conn->query("UPDATE payment_requests SET status = 'Approved' WHERE id = $id");
    $_SESSION['success'] = "Payment Approved! Order moved to 'To Ship'.";
    header("Location: payment_requests.php"); exit;
}

if(isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $conn->query("UPDATE payment_requests SET status = 'Rejected' WHERE id = $id");
    $_SESSION['error'] = "Payment Rejected!";
    header("Location: payment_requests.php"); exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Requests - Admin</title>
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

    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <header class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-4 flex justify-between items-center z-10 hidden md:flex">
            <h1 class="text-xl font-extrabold text-gray-800"><i class="fas fa-money-check-alt text-[#0B3022] mr-2"></i> Pending Payments</h1>
        </header>
        
        <div class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar pb-20 md:pb-6">
            
            <?php if(isset($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 font-bold flex justify-between shadow-sm">
                    <span><i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success']; ?></span>
                    <button onclick="this.parentElement.style.display='none'"><i class="fas fa-times"></i></button>
                </div>
            <?php unset($_SESSION['success']); endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[700px] whitespace-nowrap">
                        <thead class="bg-gray-100 text-gray-500 uppercase text-[10px] font-extrabold">
                            <tr>
                                <th class="p-4 border-b">User & Order ID</th>
                                <th class="p-4 border-b">Amount</th>
                                <th class="p-4 border-b">Method</th>
                                <th class="p-4 border-b w-1/3">Screenshot & Note</th>
                                <th class="p-4 border-b text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php
                            $reqs = $conn->query("SELECT p.*, u.name FROM payment_requests p JOIN users u ON p.user_id = u.id WHERE p.status = 'Pending' ORDER BY p.id DESC");
                            if($reqs->num_rows > 0): while($row = $reqs->fetch_assoc()):
                                
                                // Dynamic Badge Color
                                $method = strtolower($row['payment_method']);
                                $badge = "bg-gray-100 text-gray-600";
                                if($method == 'bkash') $badge = "bg-pink-100 text-pink-600";
                                if($method == 'nagad') $badge = "bg-orange-100 text-orange-600";
                                if($method == 'bank') $badge = "bg-blue-100 text-blue-600";
                            ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <p class="font-extrabold text-[#0B3022] text-sm"><?php echo htmlspecialchars($row['name']); ?></p>
                                    <p class="text-[10px] font-bold text-gray-500 mt-1 uppercase tracking-widest"><i class="fas fa-hashtag text-gray-400"></i> <?php echo $row['order_id']; ?></p>
                                </td>
                                
                                <!-- 🚀 Highlighted Amount -->
                                <td class="p-4">
                                    <p class="font-black text-gray-800 text-xl">৳ <?php echo number_format($row['amount']); ?></p>
                                </td>
                                
                                <!-- 🚀 Method Badge -->
                                <td class="p-4">
                                    <span class="<?php echo $badge; ?> px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                        <?php echo htmlspecialchars($row['payment_method']); ?>
                                    </span>
                                </td>
                                
                                <td class="p-4">
                                    <a href="../<?php echo $row['screenshot']; ?>" target="_blank" class="inline-flex items-center bg-blue-50 border border-blue-200 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-bold mb-2 hover:bg-blue-600 hover:text-white transition shadow-sm"><i class="fas fa-image mr-1.5"></i> View Proof</a>
                                    <?php if(!empty($row['note'])): ?>
                                        <p class="text-[10px] bg-yellow-50 border border-yellow-200 p-2 rounded text-gray-700 leading-tight w-48 truncate" title="<?php echo htmlspecialchars($row['note']); ?>"><strong>Note:</strong> <?php echo htmlspecialchars($row['note']); ?></p>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="p-4 text-right">
                                    <a href="?approve=<?php echo $row['id']; ?>" class="inline-block bg-[#0B3022] text-[#facc15] px-4 py-2 rounded-lg text-xs font-bold mr-2 hover:bg-[#072117] transition shadow-md"><i class="fas fa-check mr-1"></i> Approve</a>
                                    <a href="?reject=<?php echo $row['id']; ?>" class="inline-block bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-lg text-xs font-bold hover:bg-red-600 hover:text-white transition shadow-sm"><i class="fas fa-times mr-1"></i> Reject</a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5" class="p-10 text-center text-gray-400 font-bold"><i class="fas fa-inbox text-3xl mb-3 block opacity-50"></i> No pending payment requests.</td></tr>
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