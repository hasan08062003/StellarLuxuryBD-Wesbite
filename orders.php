<?php
include 'db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// ==========================================
// 🛠 REVIEW SUBMIT & POINT COLLECTION LOGIC
// ==========================================
$review_msg = "";
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $r_order_id = (int)$_POST['order_id'];
    $r_product_id = (int)$_POST['product_id'];
    $r_rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
    $r_text = $conn->real_escape_string($_POST['review_text']);
    
    $review_image_path = "";
    if(isset($_FILES['review_image']) && $_FILES['review_image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['review_image']['name'], PATHINFO_EXTENSION));
        $image_name = "review_" . $user_id . "_" . time() . "." . $ext;
        $upload_path = "useruploads/" . $image_name;
        if(move_uploaded_file($_FILES['review_image']['tmp_name'], $upload_path)) {
            $review_image_path = $upload_path;
        }
    }

    $insert_review = "INSERT INTO reviews (user_id, order_id, product_id, rating, review_text, review_image) 
                      VALUES ($user_id, $r_order_id, $r_product_id, $r_rating, '$r_text', '$review_image_path')";
    
    if($conn->query($insert_review)) {
        $conn->query("UPDATE users SET points = points + 10 WHERE id = $user_id");
        $conn->query("UPDATE orders SET order_status = 'completed' WHERE id = $r_order_id");
        $review_msg = "Thank you! 10 Points have been successfully added to your wallet.";
    }
}
// ==========================================

$current_status = isset($_GET['status']) ? $_GET['status'] : 'all';

// ডাইনামিক অর্ডার কাউন্ট
$counts = ['all' => 0, 'to_pay' => 0, 'to_ship' => 0, 'to_receive' => 0, 'to_review' => 0, 'completed' => 0];
$count_sql = "SELECT order_status, COUNT(*) as count FROM orders WHERE user_id = $user_id GROUP BY order_status";
$count_res = $conn->query($count_sql);

if($count_res && $count_res->num_rows > 0) {
    while($row = $count_res->fetch_assoc()) {
        $status = $row['order_status'];
        if(array_key_exists($status, $counts)) {
            $counts[$status] = $row['count'];
        }
        $counts['all'] += $row['count'];
    }
}

$status_filter = "";
if($current_status != 'all') {
    $escaped_status = $conn->real_escape_string($current_status);
    $status_filter = "AND order_status = '$escaped_status'";
}

$orders_sql = "SELECT * FROM orders WHERE user_id = $user_id $status_filter ORDER BY id DESC";
$orders_result = $conn->query($orders_sql);

include 'header.php';
?>

<main class="container mx-auto px-3 md:px-6 py-4 md:py-8 flex-grow min-h-[70vh] pb-24 md:pb-12 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl md:text-2xl font-black text-gray-800 flex items-center gap-2">
                <i class="fas fa-box-open text-[#0B3022]"></i> My Orders
            </h1>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-50 text-green-700 p-3 rounded-xl font-bold mb-4 border border-green-200 shadow-xs flex items-center text-xs md:text-sm">
                <i class="fas fa-check-circle text-base mr-2 flex-shrink-0"></i> Your order has been placed successfully! It is now pending payment approval.
            </div>
        <?php endif; ?>
        
        <?php if(!empty($review_msg)): ?>
            <div class="bg-yellow-50 text-[#0B3022] p-3 rounded-xl font-extrabold mb-4 border border-yellow-200 shadow-xs flex items-center text-xs md:text-sm">
                <i class="fas fa-coins text-lg mr-2 text-[#facc15] flex-shrink-0"></i> <?php echo $review_msg; ?>
            </div>
        <?php endif; ?>

        <!-- Modern Compact Scrollable Tabs -->
        <div class="bg-white rounded-xl shadow-xs border border-gray-100 mb-4 overflow-hidden">
            <div class="flex overflow-x-auto no-scrollbar border-b border-gray-100 text-xs font-bold">
                <?php 
                $tabs = [
                    'all' => 'All',
                    'to_pay' => 'To Pay',
                    'to_ship' => 'To Ship',
                    'to_receive' => 'To Receive',
                    'to_review' => 'To Review',
                    'completed' => 'Completed'
                ];
                foreach($tabs as $key => $label):
                    $active = ($current_status == $key);
                ?>
                <a href="orders.php?status=<?php echo $key; ?>" class="flex flex-shrink-0 items-center px-4 py-3 transition-all border-b-2 <?php echo $active ? 'text-[#0B3022] border-[#facc15] bg-[#0B3022]/5 font-black' : 'text-gray-500 border-transparent hover:text-[#0B3022]'; ?>">
                    <?php echo $label; ?> 
                    <span class="ml-1.5 <?php echo $active ? 'bg-[#0B3022] text-[#facc15]' : 'bg-gray-100 text-gray-600'; ?> px-1.5 py-0.2 rounded-full text-[9px]"><?php echo $counts[$key]; ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Orders List (Optimized to Prevent Overflow / Overlapping) -->
        <div class="space-y-3.5">
            <?php if($orders_result && $orders_result->num_rows > 0): ?>
                <?php while($order = $orders_result->fetch_assoc()): 
                    $order_id = $order['id'];
                    $shipping_fee = 120; 
                    
                    $pay_stat = strtolower($order['payment_status']);
                    $ord_stat = $order['order_status'];
                    $status_badge = "";

                    if($ord_stat == 'to_pay') {
                        if($pay_stat == 'paid' || $pay_stat == 'received' || $pay_stat == 'approved') {
                            $status_badge = "<span class='bg-blue-50 text-blue-700 px-2.5 py-0.5 rounded-full text-[9px] font-black border border-blue-200 whitespace-nowrap'>Payment Approved</span>";
                        } else {
                            $status_badge = "<span class='bg-orange-50 text-orange-600 px-2.5 py-0.5 rounded-full text-[9px] font-black border border-orange-200 whitespace-nowrap'>Pending Payment</span>";
                        }
                    } elseif($ord_stat == 'to_ship') {
                        $status_badge = "<span class='bg-[#0B3022]/10 text-[#0B3022] px-2.5 py-0.5 rounded-full text-[9px] font-black border border-[#0B3022]/20 whitespace-nowrap'>To Ship</span>";
                    } elseif($ord_stat == 'to_receive') {
                        $status_badge = "<span class='bg-yellow-50 text-yellow-800 px-2.5 py-0.5 rounded-full text-[9px] font-black border border-yellow-200 whitespace-nowrap'>Shipped (To Receive)</span>";
                    } elseif($ord_stat == 'to_review') {
                        $status_badge = "<span class='bg-purple-50 text-purple-700 px-2.5 py-0.5 rounded-full text-[9px] font-black border border-purple-200 whitespace-nowrap'>Review Needed</span>";
                    } elseif($ord_stat == 'completed') {
                        $status_badge = "<span class='bg-green-50 text-green-700 px-2.5 py-0.5 rounded-full text-[9px] font-black border border-green-200 whitespace-nowrap'>Completed</span>";
                    }
                ?>
                
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden text-xs">
                    
                    <!-- Header (Wrapping allowed for long Order IDs) -->
                    <div class="bg-gray-50/90 border-b border-gray-100 px-3.5 py-2.5 flex flex-wrap justify-between items-center gap-2">
                        <div class="flex items-center gap-2 text-[10px] text-gray-500 font-bold break-all">
                            <span><i class="fas fa-hashtag text-gray-400 mr-0.5"></i><?php echo htmlspecialchars($order['order_id']); ?></span>
                            <span class="text-gray-300">•</span>
                            <span><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="flex-shrink-0"><?php echo $status_badge; ?></div>
                    </div>

                    <!-- Items Display -->
                    <div class="px-3.5 py-3 space-y-2.5">
                        <?php 
                        $items_sql = "SELECT order_items.*, products.name, products.image, 
                                     COALESCE(product_color_variants.variant_image, '') as variant_image,
                                     COALESCE(product_color_variants.color_name, '') as color_name
                                     FROM order_items 
                                     JOIN products ON order_items.product_id = products.id 
                                     LEFT JOIN product_color_variants ON order_items.variant_id = product_color_variants.id
                                     WHERE order_items.order_id = $order_id";
                        $items_res = $conn->query($items_sql);
                        
                        $product_subtotal = 0;
                        $first_product_id = 0; 
                        
                        if($items_res && $items_res->num_rows > 0):
                            $count = 0;
                            while($item = $items_res->fetch_assoc()):
                                $product_subtotal += ($item['price'] * $item['quantity']);
                                if($count == 0) $first_product_id = $item['product_id'];
                                $count++;
                                $display_img = !empty($item['variant_image']) ? $item['variant_image'] : $item['image'];
                        ?>
                        <div class="flex items-start sm:items-center gap-3">
                            <img src="<?php echo $display_img; ?>" class="w-12 h-12 object-cover rounded-lg border border-gray-200 bg-white p-0.5 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <!-- Multiline support so long titles never break layout -->
                                <h4 class="font-bold text-gray-800 text-xs md:text-sm leading-snug break-words"><?php echo htmlspecialchars($item['name']); ?></h4>
                                <?php if(!empty($item['color_name'])): ?>
                                    <p class="text-[10px] font-extrabold text-purple-700 mt-0.5">Color: <?php echo htmlspecialchars($item['color_name']); ?></p>
                                <?php endif; ?>
                                <p class="text-[11px] text-gray-500 font-bold mt-0.5">Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                            <span class="font-black text-[#0B3022] text-xs sm:text-sm flex-shrink-0 self-center">৳ <?php echo number_format($item['price'] * $item['quantity']); ?></span>
                        </div>
                        <?php 
                            endwhile;
                        endif; 
                        ?>
                    </div>

                    <!-- Footer & Actions -->
                    <div class="border-t border-gray-100 bg-white px-3.5 py-3 flex flex-wrap justify-between items-center gap-3">
                        <div class="flex items-center gap-2">
                            <button onclick="toggleSummary(<?php echo $order_id; ?>)" class="text-xs font-bold text-gray-500 hover:text-[#0B3022] transition flex items-center gap-1">
                                Summary <i id="icon_summary_<?php echo $order_id; ?>" class="fas fa-chevron-down text-[9px]"></i>
                            </button>
                            <span class="text-gray-300">|</span>
                            <span class="font-bold text-gray-700 text-xs">Total: <span class="font-black text-[#0B3022] text-sm">৳ <?php echo number_format($order['total_amount']); ?></span></span>
                        </div>

                        <div class="flex gap-2">
                            <?php if($ord_stat == 'completed'): ?>
                                <a href="product_variant.php?id=<?php echo $first_product_id; ?>" class="bg-[#0B3022] text-[#facc15] text-[11px] font-extrabold px-3.5 py-1.5 rounded-lg hover:bg-[#072117] transition shadow-xs">Buy Again</a>
                            <?php elseif($ord_stat == 'to_review'): ?>
                                <button onclick="toggleReviewForm(<?php echo $order_id; ?>)" class="bg-[#facc15] text-[#0B3022] text-[11px] font-black px-4 py-1.5 rounded-lg hover:bg-yellow-500 transition shadow-xs flex items-center gap-1.5 animate-pulse">
                                    <i class="fas fa-star"></i> Collect 10 Points
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Expandable Summary -->
                    <div id="summary_<?php echo $order_id; ?>" class="hidden px-3.5 py-2.5 bg-gray-50 border-t border-gray-100 text-xs font-semibold text-gray-600 space-y-1">
                        <div class="flex justify-between"><span>Subtotal:</span> <span>৳ <?php echo number_format($product_subtotal); ?></span></div>
                        <div class="flex justify-between"><span>Shipping Fee:</span> <span>৳ <?php echo number_format($shipping_fee); ?></span></div>
                        <div class="flex justify-between pt-1.5 border-t border-gray-200 font-black text-gray-800 text-sm"><span>Grand Total:</span> <span class="text-[#0B3022]">৳ <?php echo number_format($order['total_amount']); ?></span></div>
                    </div>

                    <!-- Expandable Review Form -->
                    <?php if($ord_stat == 'to_review'): ?>
                    <div id="review_form_<?php echo $order_id; ?>" class="hidden border-t border-gray-100 bg-[#0B3022]/5 px-3.5 py-3">
                        <div class="bg-white rounded-xl p-3.5 shadow-xs border border-[#facc15]/40 space-y-2.5">
                            <h4 class="font-black text-[#0B3022] text-xs">⭐ Write Review & Claim 10 Points</h4>
                            <form action="orders.php" method="POST" enctype="multipart/form-data" class="space-y-2.5">
                                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                <input type="hidden" name="product_id" value="<?php echo $first_product_id; ?>">
                                
                                <!-- Star Rating -->
                                <div class="flex text-[#facc15] text-base cursor-pointer" id="star_rating_<?php echo $order_id; ?>">
                                    <i class="fas fa-star" onclick="setRating(<?php echo $order_id; ?>, 1)"></i>
                                    <i class="fas fa-star" onclick="setRating(<?php echo $order_id; ?>, 2)"></i>
                                    <i class="fas fa-star" onclick="setRating(<?php echo $order_id; ?>, 3)"></i>
                                    <i class="fas fa-star" onclick="setRating(<?php echo $order_id; ?>, 4)"></i>
                                    <i class="fas fa-star" onclick="setRating(<?php echo $order_id; ?>, 5)"></i>
                                </div>
                                <input type="hidden" name="rating" id="rating_val_<?php echo $order_id; ?>" value="5">

                                <textarea name="review_text" rows="2" placeholder="Write your review..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-gray-700 text-xs font-semibold focus:outline-none focus:border-[#0B3022] resize-none" required></textarea>
                                
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <input type="file" name="review_image" accept="image/*" class="w-full sm:w-1/2 bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-[10px] font-bold text-gray-600 cursor-pointer" required>
                                    <button type="submit" name="submit_review" class="w-full sm:w-1/2 bg-[#0B3022] text-[#facc15] font-extrabold py-2 rounded-lg hover:bg-[#072117] transition text-xs flex items-center justify-center gap-1 shadow-xs">
                                        <i class="fas fa-coins"></i> Submit & Collect
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
                <?php endwhile; ?>
                
            <?php else: ?>
                <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-8 text-center">
                    <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                        <i class="fas fa-box-open text-xl text-gray-300"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-700 mb-1">No orders found</h3>
                    <p class="text-xs text-gray-500 mb-3 font-semibold">You don't have any orders in this section.</p>
                    <a href="index.php" class="inline-block bg-[#0B3022] text-[#facc15] text-xs font-extrabold px-5 py-2.5 rounded-xl hover:bg-[#072117] transition">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
function toggleSummary(orderId) {
    const summaryDiv = document.getElementById('summary_' + orderId);
    const icon = document.getElementById('icon_summary_' + orderId);
    if(summaryDiv.classList.contains('hidden')) {
        summaryDiv.classList.remove('hidden');
        icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
    } else {
        summaryDiv.classList.add('hidden');
        icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
    }
}

function toggleReviewForm(orderId) {
    const reviewForm = document.getElementById('review_form_' + orderId);
    if(reviewForm.classList.contains('hidden')) {
        reviewForm.classList.remove('hidden');
    } else {
        reviewForm.classList.add('hidden');
    }
}

function setRating(orderId, rating) {
    document.getElementById('rating_val_' + orderId).value = rating;
    const stars = document.getElementById('star_rating_' + orderId).children;
    for(let i=0; i<5; i++) {
        if(i < rating) {
            stars[i].classList.remove('far');
            stars[i].classList.add('fas');
        } else {
            stars[i].classList.remove('fas');
            stars[i].classList.add('far');
        }
    }
}
</script>

<?php include 'footer.php'; ?>