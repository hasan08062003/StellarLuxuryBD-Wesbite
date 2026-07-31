<?php
include '../db.php'; 
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
if (!isset($_GET['id'])) { header("Location: users.php"); exit; }

$user_id = (int)$_GET['id'];
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
if ($user_query->num_rows == 0) { header("Location: users.php"); exit; }
$user = $user_query->fetch_assoc();

// 🚀 Shipping Mark Logic
$first_name = strtok($user['name'], " ");
$shipping_mark = sprintf("%02d-%s", $user['id'], $first_name);

$orders_query = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC");
$payments_query = $conn->query("SELECT * FROM payment_requests WHERE user_id = $user_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspect User - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>.tab-content { display: none; } .tab-content.active { display: block; } .no-scrollbar::-webkit-scrollbar { display: none; }</style>
</head>
<body class="bg-gray-50 h-screen flex flex-col text-sm font-sans">

    <!-- 📱 Top Header (Mobile & Desktop) -->
    <header class="bg-[#0B3022] text-white px-4 py-3 flex justify-between items-center shadow-md z-30">
        <a href="users.php" class="text-white hover:text-[#facc15] flex items-center gap-2 font-bold text-xs md:text-sm"><i class="fas fa-arrow-left"></i> Back to Users</a>
        <h1 class="font-extrabold uppercase tracking-widest text-xs md:text-sm">User Inspect</h1>
        <!-- 🚀 Shipping Mark in Header -->
        <span class="bg-white/10 px-2 py-1.5 rounded font-black tracking-widest text-[#facc15] text-[10px] md:text-xs border border-[#facc15]/30 shadow-sm uppercase">
            <?php echo $shipping_mark; ?>
        </span>
    </header>

    <main class="flex-1 overflow-y-auto p-3 md:p-6 pb-20">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-6xl mx-auto">
            
            <!-- User Title Area -->
            <div class="p-4 md:p-6 flex items-center gap-3 md:gap-4 border-b border-gray-100 bg-gray-50/50">
                <div class="w-14 h-14 md:w-20 md:h-20 rounded-full bg-[#0B3022] text-[#facc15] flex items-center justify-center font-bold text-2xl md:text-4xl shadow-sm border-2 border-[#facc15]/30">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <div>
                    <h2 class="text-base md:text-2xl font-extrabold text-gray-800"><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p class="text-[11px] md:text-sm text-blue-600 font-bold mb-1"><?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="text-[10px]"><span class="bg-[#facc15]/20 text-[#0B3022] px-2 py-0.5 rounded font-extrabold uppercase border border-[#facc15]/50"><i class="fas fa-crown text-[#eab308] mr-1"></i> <?php echo !empty($user['membership_tier']) ? $user['membership_tier'] : 'New Member'; ?></span></p>
                </div>
            </div>
            
            <!-- 🚀 TABS BUTTONS -->
            <div class="flex border-b border-gray-200 bg-white px-2 pt-2 gap-2 md:gap-4 overflow-x-auto no-scrollbar whitespace-nowrap">
                <button onclick="switchTab('account')" id="btn-account" class="tab-btn px-4 py-3 text-xs md:text-sm font-extrabold text-[#0B3022] border-b-4 border-[#0B3022] transition"><i class="fas fa-id-card mr-1.5"></i> Account Info</button>
                <button onclick="switchTab('orders')" id="btn-orders" class="tab-btn px-4 py-3 text-xs md:text-sm font-bold text-gray-500 border-b-4 border-transparent transition"><i class="fas fa-shopping-bag mr-1.5"></i> Orders</button>
                <button onclick="switchTab('payments')" id="btn-payments" class="tab-btn px-4 py-3 text-xs md:text-sm font-bold text-gray-500 border-b-4 border-transparent transition"><i class="fas fa-money-check-alt mr-1.5"></i> Payments</button>
            </div>

            <!-- 📑 TAB 1: ACCOUNT INFO -->
            <div id="tab-account" class="tab-content active p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                    
                    <!-- Basic Info -->
                    <div>
                        <h3 class="text-sm md:text-base font-extrabold text-gray-800 border-b pb-2 mb-4"><i class="fas fa-user-circle text-gray-400 mr-2"></i> Basic Details</h3>
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center gap-3">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-gray-400 shadow-sm"><i class="fas fa-phone-alt"></i></div>
                                <div><p class="text-[10px] text-gray-400 font-bold uppercase">Phone Number</p><p class="font-extrabold text-gray-800"><?php echo !empty($user['phone']) ? $user['phone'] : 'Not Provided'; ?></p></div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center gap-3">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-yellow-500 shadow-sm"><i class="fas fa-coins"></i></div>
                                <div><p class="text-[10px] text-gray-400 font-bold uppercase">Total Points</p><p class="font-extrabold text-[#0B3022] text-lg"><?php echo isset($user['points']) ? $user['points'] : 0; ?> <span class="text-xs text-gray-500 font-medium">Pts</span></p></div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center gap-3">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-gray-400 shadow-sm"><i class="fas fa-calendar-alt"></i></div>
                                <div><p class="text-[10px] text-gray-400 font-bold uppercase">Joined Date</p><p class="font-bold text-gray-700"><?php echo isset($user['created_at']) ? date('d M Y, h:i A', strtotime($user['created_at'])) : 'Unknown'; ?></p></div>
                            </div>
                        </div>
                    </div>

                    <!-- 🚀 All Addresses Section -->
                    <div>
                        <h3 class="text-sm md:text-base font-extrabold text-gray-800 border-b pb-2 mb-4"><i class="fas fa-map-marked-alt text-gray-400 mr-2"></i> Address Book</h3>
                        <div class="space-y-4 text-sm">
                            <!-- Primary Address -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-sm">
                                <p class="text-[10px] md:text-xs font-extrabold text-gray-500 uppercase tracking-wider mb-1"><i class="fas fa-home mr-1"></i> Primary Address</p>
                                <p class="text-gray-700 font-medium leading-relaxed"><?php echo !empty($user['address']) ? nl2br(htmlspecialchars($user['address'])) : '<span class="italic text-gray-400">Not Provided</span>'; ?></p>
                            </div>
                            
                            <!-- Secondary Address -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-sm">
                                <p class="text-[10px] md:text-xs font-extrabold text-gray-500 uppercase tracking-wider mb-1"><i class="fas fa-building mr-1"></i> Secondary Address</p>
                                <p class="text-gray-700 font-medium leading-relaxed"><?php echo !empty($user['secondary_address']) ? nl2br(htmlspecialchars($user['secondary_address'])) : '<span class="italic text-gray-400">Not Provided</span>'; ?></p>
                            </div>
                            
                            <!-- Default Shipping Address -->
                            <div class="bg-blue-50 p-4 rounded-xl border-2 border-blue-200 shadow-sm relative overflow-hidden">
                                <div class="absolute -right-4 -top-4 w-16 h-16 bg-blue-500 rounded-full opacity-10"></div>
                                <p class="text-[10px] md:text-xs font-black text-blue-700 uppercase tracking-wider mb-1"><i class="fas fa-truck-fast mr-1"></i> Default Shipping Address</p>
                                <p class="text-gray-800 font-extrabold leading-relaxed text-sm md:text-base">
                                    <?php 
                                        if(!empty($user['shipping_address'])) {
                                            echo nl2br(htmlspecialchars($user['shipping_address']));
                                        } elseif(!empty($user['address'])) {
                                            echo nl2br(htmlspecialchars($user['address'])) . ' <span class="text-[10px] font-normal text-gray-500">(Used Primary)</span>';
                                        } else {
                                            echo '<span class="italic text-gray-400 font-medium">Not Provided</span>';
                                        }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- 📑 TAB 2: ORDERS -->
            <div id="tab-orders" class="tab-content p-4 md:p-6 overflow-x-auto no-scrollbar">
                <table class="w-full text-left whitespace-nowrap min-w-[500px]">
                    <thead class="bg-gray-100 text-gray-500 text-[10px] uppercase font-extrabold tracking-wider">
                        <tr><th class="p-4 rounded-l-lg">Order ID</th><th class="p-4">Amount</th><th class="p-4">Status</th><th class="p-4 rounded-r-lg">Date</th></tr>
                    </thead>
                    <tbody class="text-xs font-bold text-gray-700">
                        <?php if($orders_query->num_rows > 0): while($ord = $orders_query->fetch_assoc()): ?>
                        <tr class="border-b border-gray-50">
                            <td class="p-4 text-[#0B3022]">#ORD-<?php echo $ord['id']; ?></td>
                            <td class="p-4 text-base font-black">৳<?php echo number_format($ord['total_amount']); ?></td>
                            <td class="p-4 text-orange-500"><?php echo $ord['status']; ?></td>
                            <td class="p-4 text-gray-400 font-medium"><?php echo date('d M Y', strtotime($ord['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; else: ?><tr><td colspan="4" class="p-8 text-center text-gray-400">No orders placed yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- 📑 TAB 3: PAYMENTS -->
            <div id="tab-payments" class="tab-content p-4 md:p-6 overflow-x-auto no-scrollbar">
                <table class="w-full text-left whitespace-nowrap min-w-[650px]">
                    <thead class="bg-gray-100 text-gray-500 text-[10px] uppercase font-extrabold tracking-wider">
                        <tr>
                            <th class="p-4 rounded-l-lg">Amount</th>
                            <th class="p-4">Method</th>
                            <th class="p-4">Screenshot & Note</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 rounded-r-lg">Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs font-bold text-gray-700">
                        <?php if($payments_query->num_rows > 0): while($pay = $payments_query->fetch_assoc()): 
                            $method = strtolower($pay['payment_method']);
                            $badge_class = "bg-gray-100 text-gray-600";
                            if($method == 'bkash') $badge_class = "bg-pink-100 text-pink-600";
                            if($method == 'nagad') $badge_class = "bg-orange-100 text-orange-600";
                            if($method == 'bank') $badge_class = "bg-blue-100 text-blue-600";
                        ?>
                        <tr class="border-b border-gray-50">
                            <td class="p-4 text-lg font-black text-[#0B3022]">৳<?php echo number_format($pay['amount']); ?></td>
                            <td class="p-4">
                                <span class="<?php echo $badge_class; ?> px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                    <?php echo htmlspecialchars($pay['payment_method']); ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <a href="../<?php echo $pay['screenshot']; ?>" target="_blank" class="inline-block bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 transition shadow-sm mb-1 text-blue-600"><i class="fas fa-image mr-1"></i> View</a>
                                <?php if(!empty($pay['note'])): ?>
                                    <p class="text-[10px] text-gray-500 font-medium truncate w-48" title="<?php echo htmlspecialchars($pay['note']); ?>">Note: <?php echo htmlspecialchars($pay['note']); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 <?php echo $pay['status'] == 'Approved' ? 'text-green-600' : 'text-orange-500'; ?>"><?php echo $pay['status']; ?></td>
                            <td class="p-4 text-gray-400 font-medium"><?php echo date('d M y', strtotime($pay['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; else: ?><tr><td colspan="5" class="p-8 text-center text-gray-400">No payment history found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('text-[#0B3022]', 'border-[#0B3022]', 'font-extrabold');
                el.classList.add('text-gray-500', 'border-transparent', 'font-bold');
            });
            document.getElementById('tab-' + tabId).classList.add('active');
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.remove('text-gray-500', 'border-transparent', 'font-bold');
            activeBtn.classList.add('text-[#0B3022]', 'border-[#0B3022]', 'font-extrabold');
        }
    </script>
</body>
</html>