<?php
include 'db.php';
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

$profile_pic = !empty($user['profile_image']) ? $user['profile_image'] : "https://ui-avatars.com/api/?name=".urlencode($user['name'])."&background=0B3022&color=facc15&size=128";
$user_points = isset($user['points']) ? $user['points'] : 0;
$tier = !empty($user['membership_tier']) ? $user['membership_tier'] : 'New Member';

// ==========================================
// 🛠 ডাইনামিক অর্ডার কাউন্ট লজিক
// ==========================================
$counts = ['to_pay' => 0, 'to_ship' => 0, 'to_receive' => 0, 'to_review' => 0, 'refund' => 0];
$count_sql = "SELECT order_status, COUNT(*) as count FROM orders WHERE user_id = $user_id GROUP BY order_status";
$count_res = $conn->query($count_sql);

if($count_res && $count_res->num_rows > 0) {
    while($row = $count_res->fetch_assoc()) {
        $status = $row['order_status'];
        if(array_key_exists($status, $counts)) {
            $counts[$status] = $row['count'];
        }
    }
}
// ==========================================

include 'header.php';
?>

<div class="container mx-auto px-4 py-6 md:py-8 min-h-screen bg-gray-50 pb-24 md:pb-10">
    
    <!-- User Top Info & Points -->
    <div class="bg-gradient-to-r from-[#0B3022] to-[#072117] rounded-2xl p-6 md:p-8 text-white shadow-lg mb-6 relative overflow-hidden">
        <i class="fas fa-crown absolute -right-4 -bottom-4 text-8xl text-white opacity-5"></i>
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-16 h-16 md:w-20 md:h-20 bg-white rounded-full flex items-center justify-center border-4 border-[#facc15] shadow-md overflow-hidden p-0.5">
                <img src="<?php echo $profile_pic; ?>" alt="Profile" class="w-full h-full object-cover rounded-full">
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-extrabold tracking-wide uppercase"><?php echo htmlspecialchars($user['name']); ?></h2>
                <div class="flex items-center gap-3 mt-2">
                    <span class="bg-[#facc15] text-[#0B3022] text-[10px] md:text-xs font-extrabold px-3 py-1.5 rounded-full shadow-sm uppercase tracking-wide"><i class="fas fa-crown mr-1"></i> <?php echo htmlspecialchars($tier); ?></span>
                    <span class="text-sm font-bold text-gray-200"><i class="fas fa-coins text-[#facc15] mr-1"></i> <?php echo number_format($user_points); ?> Points</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Perfected Order Status Board -->
    <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-gray-100 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-extrabold text-gray-800">My Orders</h3>
            <a href="orders.php?status=all" class="text-sm font-bold text-blue-600 hover:underline">View All <i class="fas fa-angle-right ml-1"></i></a>
        </div>
        
        <!-- Updated Dynamic Status Icons -->
        <div class="grid grid-cols-5 gap-2 text-center">
            
            <a href="orders.php?status=to_pay" class="group flex flex-col items-center relative">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-50 group-hover:bg-blue-50 rounded-full flex items-center justify-center mb-2 transition relative">
                    <i class="fas fa-wallet text-gray-500 group-hover:text-blue-600 text-lg md:text-xl"></i>
                    <?php if($counts['to_pay'] > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white shadow-sm"><?php echo $counts['to_pay']; ?></span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] md:text-xs font-bold text-gray-600 group-hover:text-blue-600">To Pay</span>
            </a>
            
            <a href="orders.php?status=to_ship" class="group flex flex-col items-center relative">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-50 group-hover:bg-orange-50 rounded-full flex items-center justify-center mb-2 transition relative">
                    <i class="fas fa-box-open text-gray-500 group-hover:text-orange-600 text-lg md:text-xl"></i>
                    <?php if($counts['to_ship'] > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white shadow-sm"><?php echo $counts['to_ship']; ?></span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] md:text-xs font-bold text-gray-600 group-hover:text-orange-600">To Ship</span>
            </a>
            
            <a href="orders.php?status=to_receive" class="group flex flex-col items-center relative">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-50 group-hover:bg-[#0B3022]/10 rounded-full flex items-center justify-center mb-2 transition relative">
                    <i class="fas fa-truck-fast text-gray-500 group-hover:text-[#0B3022] text-lg md:text-xl"></i>
                    <?php if($counts['to_receive'] > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white shadow-sm"><?php echo $counts['to_receive']; ?></span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] md:text-xs font-bold text-gray-600 group-hover:text-[#0B3022]">To Receive</span>
            </a>
            
            <a href="orders.php?status=to_review" class="group flex flex-col items-center relative">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-50 group-hover:bg-[#facc15]/20 rounded-full flex items-center justify-center mb-2 transition relative">
                    <i class="far fa-star text-gray-500 group-hover:text-[#0B3022] text-lg md:text-xl"></i>
                    <?php if($counts['to_review'] > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white shadow-sm"><?php echo $counts['to_review']; ?></span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] md:text-xs font-bold text-gray-600 group-hover:text-[#0B3022]">To Review</span>
            </a>
            
            <a href="orders.php?status=refund" class="group flex flex-col items-center relative">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-50 group-hover:bg-red-50 rounded-full flex items-center justify-center mb-2 transition relative">
                    <i class="fas fa-undo-alt text-gray-500 group-hover:text-red-600 text-lg md:text-xl"></i>
                    <?php if($counts['refund'] > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white shadow-sm"><?php echo $counts['refund']; ?></span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] md:text-xs font-bold text-gray-600 group-hover:text-red-600">Refund</span>
            </a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>