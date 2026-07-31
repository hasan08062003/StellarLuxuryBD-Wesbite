<?php
include 'db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// ==========================================
// 🛠 PAYMENT SETTINGS 
// ==========================================
$bkash_number = "01711-XXXXXX (Personal)"; 
$nagad_number = "01911-XXXXXX (Personal)"; 
$bank_details = "Dutch-Bangla Bank (DBBL)<br>A/C: 123.456.7890<br>Name: StellarLuxury BD"; 
// ==========================================

$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

$phone = !empty($user['phone']) ? $user['phone'] : '';
$shipping_address = !empty($user['shipping_address']) ? $user['shipping_address'] : (!empty($user['address']) ? $user['address'] : '');
$is_profile_complete = (!empty($phone) && !empty($shipping_address)) ? true : false;

$user_points = isset($user['points']) ? (int)$user['points'] : 0;
$error_msg = "";

// ==========================================
// ORDER PROCESSING LOGIC
// ==========================================
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    
    if(empty($phone) || empty($shipping_address)) {
        $error_msg = "Your profile is incomplete! You must provide a Mobile Number and Shipping Address.";
    } else {
        $pay_method = $_POST['payment_method']; 
        $payment_note = isset($_POST['payment_note']) ? $conn->real_escape_string($_POST['payment_note']) : '';
        $total_amount = (float)$_POST['total_amount'];
        
        if($pay_method == 'points' && $user_points < $total_amount) {
            $error_msg = "Insufficient coins! You don't have enough points to complete this purchase.";
        }
        
        if(empty($error_msg)) {
            $order_id_string = "ORD-" . strtoupper(uniqid()); 
            $payment_status = 'Pending';
            $order_status = 'to_pay'; 
            
            if($pay_method == 'points') {
                $payment_status = 'Paid';
                $order_status = 'to_ship'; 
                $conn->query("UPDATE users SET points = points - $total_amount WHERE id = $user_id");
            }

            $screenshot_path = "";
            if(in_array($pay_method, ['bkash', 'nagad', 'bank']) && isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] == 0) {
                $ext = pathinfo($_FILES['payment_screenshot']['name'], PATHINFO_EXTENSION);
                $image_name = "pay_" . $order_id_string . "_" . time() . "." . $ext;
                $upload_path = "useruploads/" . $image_name;
                if(move_uploaded_file($_FILES['payment_screenshot']['tmp_name'], $upload_path)) {
                    $screenshot_path = $upload_path;
                }
            }

            // Insert into Orders Table
            $insert_order = "INSERT INTO orders (order_id, user_id, total_amount, payment_method, payment_screenshot, payment_status, order_status, shipping_address) 
                             VALUES ('$order_id_string', $user_id, '$total_amount', '$pay_method', '$screenshot_path', '$payment_status', '$order_status', '$shipping_address')";
            
            if($conn->query($insert_order)) {
                $new_order_id = $conn->insert_id;
                
                if(isset($_SESSION['direct_checkout_items']) && !empty($_SESSION['direct_checkout_items'])) {
                    foreach($_SESSION['direct_checkout_items'] as $d_item) {
                        $c_p_id = $d_item['product_id'];
                        $c_var_id = isset($d_item['variant_id']) ? $d_item['variant_id'] : 0;
                        $c_qty = $d_item['checkout_qty'];
                        $c_price = $d_item['price'];
                        $conn->query("INSERT INTO order_items (order_id, product_id, variant_id, quantity, price) VALUES ($new_order_id, $c_p_id, $c_var_id, $c_qty, '$c_price')");
                    }
                    unset($_SESSION['direct_checkout_items']); 
                } else {
                    $cart_res = $conn->query("SELECT cart.*, products.price FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = $user_id");
                    while($c_row = $cart_res->fetch_assoc()) {
                        $c_p_id = $c_row['product_id'];
                        $c_var_id = isset($c_row['variant_id']) ? $c_row['variant_id'] : 0;
                        $c_qty = $c_row['quantity'];
                        $c_price = $c_row['price'];
                        $conn->query("INSERT INTO order_items (order_id, product_id, variant_id, quantity, price) VALUES ($new_order_id, $c_p_id, $c_var_id, $c_qty, '$c_price')");
                    }
                    $conn->query("DELETE FROM cart WHERE user_id = $user_id");
                }

                if(in_array($pay_method, ['bkash', 'nagad', 'bank'])) {
                    $conn->query("INSERT INTO payment_requests (user_id, order_id, amount, payment_method, screenshot, note, status) 
                                  VALUES ($user_id, '$order_id_string', '$total_amount', '$pay_method', '$screenshot_path', '$payment_note', 'Pending')");
                }

                header("Location: orders.php?status=" . $order_status . "&success=1");
                exit;
            }
        }
    }
}

// ==========================================
// CHECKOUT DISPLAY LOGIC
// ==========================================
$subtotal = 0;
$items = [];
$shipping_fee = 120;

if(isset($_GET['direct_buy']) && isset($_SESSION['direct_checkout_items'])) {
    $items = $_SESSION['direct_checkout_items'];
    foreach($items as $item) {
        $subtotal += ($item['price'] * $item['checkout_qty']);
    }
} else {
    $res = $conn->query("SELECT cart.*, products.name, products.price, products.image, 
                         COALESCE(product_color_variants.color_name, '') as color_name,
                         COALESCE(product_color_variants.variant_image, '') as variant_image
                         FROM cart 
                         JOIN products ON cart.product_id = products.id 
                         LEFT JOIN product_color_variants ON cart.variant_id = product_color_variants.id
                         WHERE cart.user_id = $user_id");

    while($row = $res->fetch_assoc()) {
        $row['checkout_qty'] = $row['quantity'];
        $items[] = $row;
        $subtotal += ($row['price'] * $row['checkout_qty']);
    }
}

$grand_total = $subtotal + $shipping_fee;
if(empty($items)) { header("Location: index.php"); exit; }

include 'header.php';
?>

<main class="container mx-auto px-3 md:px-6 py-6 md:py-10 flex-grow min-h-[70vh] bg-gray-50 pb-28">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-3">
            <h1 class="text-xl md:text-2xl font-black text-[#0B3022] flex items-center gap-2">
                <i class="fas fa-shield-alt text-[#facc15]"></i> Secure Checkout
            </h1>
            <a href="cart.php" class="text-xs font-bold text-gray-500 hover:text-[#0B3022] transition"><i class="fas fa-arrow-left mr-1"></i> Back to Cart</a>
        </div>
        
        <?php if(!empty($error_msg)): ?>
            <div class="bg-red-50 text-red-600 p-3.5 rounded-xl font-bold mb-6 border border-red-200 shadow-sm flex items-center text-xs md:text-sm">
                <i class="fas fa-exclamation-circle text-lg mr-2"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <?php if(!$is_profile_complete): ?>
            <div id="profileWarningBox" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm mb-6 transition-all relative overflow-hidden">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 text-red-500 font-bold">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <h3 class="text-xs md:text-sm font-black text-red-700">Complete Your Profile!</h3>
                            <p class="text-[11px] text-gray-600 font-medium">Mobile number & shipping address are required to order.</p>
                        </div>
                    </div>
                    <a href="profile.php" class="bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-extrabold shadow hover:bg-red-700 transition">Update Now</a>
                </div>
            </div>
        <?php endif; ?>

        <form action="checkout.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <input type="hidden" name="total_amount" value="<?php echo $grand_total; ?>">

            <!-- Left Side: Shipping & Payment (7 Cols) -->
            <div class="lg:col-span-7 space-y-5">
                
                <!-- Contact & Shipping Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-[#0B3022]"></i> Shipping Details
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <span class="text-[10px] text-gray-400 font-bold uppercase block mb-0.5">Phone</span>
                            <span class="font-black text-gray-800"><?php echo !empty($phone) ? htmlspecialchars($phone) : '<span class="text-red-500">Missing</span>'; ?></span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <span class="text-[10px] text-gray-400 font-bold uppercase block mb-0.5">Address</span>
                            <span class="font-bold text-gray-800 truncate block"><?php echo !empty($shipping_address) ? htmlspecialchars($shipping_address) : '<span class="text-red-500">Missing</span>'; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative">
                    <?php if(!$is_profile_complete): ?><div class="absolute inset-0 bg-white/70 z-10 rounded-2xl cursor-not-allowed"></div><?php endif; ?>
                    
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-wallet text-[#0B3022]"></i> Payment Method
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-2.5 mb-4">
                        <!-- bKash -->
                        <label class="border-2 border-gray-200 rounded-xl p-3 cursor-pointer hover:border-pink-500 transition has-[:checked]:border-pink-600 has-[:checked]:bg-pink-50/40 flex items-center gap-2.5">
                            <input type="radio" name="payment_method" value="bkash" class="w-4 h-4 text-pink-600" checked onchange="togglePaymentBox()">
                            <img src="https://download.logo.wine/logo/BKash/BKash-Icon-Logo.wine.png" class="h-6 object-contain" alt="bKash">
                            <span class="font-extrabold text-xs text-gray-800">bKash</span>
                        </label>
                        <!-- Nagad -->
                        <label class="border-2 border-gray-200 rounded-xl p-3 cursor-pointer hover:border-orange-500 transition has-[:checked]:border-orange-600 has-[:checked]:bg-orange-50/40 flex items-center gap-2.5">
                            <input type="radio" name="payment_method" value="nagad" class="w-4 h-4 text-orange-600" onchange="togglePaymentBox()">
                            <img src="https://download.logo.wine/logo/Nagad/Nagad-Logo.wine.png" class="h-6 object-contain" alt="Nagad">
                            <span class="font-extrabold text-xs text-gray-800">Nagad</span>
                        </label>
                        <!-- Bank -->
                        <label class="border-2 border-gray-200 rounded-xl p-3 cursor-pointer hover:border-blue-500 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/40 flex items-center gap-2.5">
                            <input type="radio" name="payment_method" value="bank" class="w-4 h-4 text-blue-600" onchange="togglePaymentBox()">
                            <i class="fas fa-university text-blue-600 text-base"></i>
                            <span class="font-extrabold text-xs text-gray-800">Bank</span>
                        </label>
                        <!-- Coins / Points -->
                        <label class="border-2 border-gray-200 rounded-xl p-3 cursor-pointer hover:border-[#0B3022] transition has-[:checked]:border-[#0B3022] has-[:checked]:bg-[#0B3022]/5 flex items-center gap-2.5 relative">
                            <input type="radio" name="payment_method" value="points" class="w-4 h-4 text-[#0B3022]" onchange="togglePaymentBox()">
                            <i class="fas fa-coins text-[#facc15] text-base"></i>
                            <div class="overflow-hidden">
                                <span class="font-extrabold text-xs text-gray-800 block truncate">Coins</span>
                                <span class="text-[9px] font-bold text-gray-500 block"><?php echo number_format($user_points); ?></span>
                            </div>
                        </label>
                    </div>

                    <!-- Manual Payment Box (Screenshot & Note Upload) -->
                    <div id="manualPaymentBox" class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-center shadow-xs">
                            <p class="text-[10px] text-gray-400 font-bold uppercase" id="paymentMethodName">Send Money To (bKash)</p>
                            <h3 class="text-sm md:text-base font-black text-gray-800 mt-0.5" id="paymentMethodNumber"><?php echo $bkash_number; ?></h3>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Payment Screenshot *</label>
                            <input type="file" name="payment_screenshot" id="screenshotInput" accept="image/*" class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-xs font-semibold outline-none shadow-xs cursor-pointer" required>
                        </div>
                    </div>

                    <!-- 🚀 Payment Note Field Added Back -->
                    <div class="mt-3">
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Payment Note (Optional)</label>
                        <textarea name="payment_note" rows="2" placeholder="e.g. Sent from 017XXXXX or transaction ID" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium focus:border-[#0B3022] outline-none shadow-xs"></textarea>
                    </div>

                    <!-- Insufficient Coins Warning -->
                    <div id="pointsError" class="hidden mt-3 bg-red-50 text-red-600 p-3 rounded-xl border border-red-200 text-xs font-bold">
                        <i class="fas fa-exclamation-circle mr-1"></i> Insufficient coins! You need <?php echo number_format($grand_total); ?> coins to pay.
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary (5 Cols - Compact & Modern) -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-24">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-3 border-b pb-2">Order Summary</h3>
                    
                    <!-- Compact Scrollable Items List -->
                    <div class="space-y-2 mb-4 max-h-[220px] overflow-y-auto custom-scrollbar border-b border-gray-100 pb-3 pr-1">
                        <?php foreach($items as $item): 
                            $item_img = !empty($item['variant_image']) ? $item['variant_image'] : $item['image'];
                        ?>
                        <div class="flex items-center gap-2.5 bg-gray-50 p-2 rounded-xl border border-gray-100">
                            <img src="<?php echo $item_img; ?>" class="w-9 h-9 object-cover rounded-lg bg-white p-0.5 border border-gray-200 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-gray-800 truncate"><?php echo htmlspecialchars($item['name']); ?></h4>
                                <?php if(!empty($item['color_name'])): ?>
                                    <p class="text-[9px] font-black text-purple-700">Color: <?php echo htmlspecialchars($item['color_name']); ?></p>
                                <?php endif; ?>
                                <p class="text-[10px] text-gray-500 font-bold"><?php echo $item['checkout_qty']; ?>x ৳<?php echo number_format($item['price']); ?></p>
                            </div>
                            <span class="text-xs font-black text-[#0B3022] flex-shrink-0">৳<?php echo number_format($item['price'] * $item['checkout_qty']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="space-y-1.5 text-xs font-medium">
                        <div class="flex justify-between text-gray-500"><span>Subtotal</span><span class="font-bold text-gray-800">৳ <?php echo number_format($subtotal); ?></span></div>
                        <div class="flex justify-between text-gray-500"><span>Shipping Fee</span><span class="font-bold text-gray-800">৳ <?php echo number_format($shipping_fee); ?></span></div>
                    </div>
                    
                    <div class="flex justify-between my-4 text-base font-black text-gray-800 bg-[#facc15]/20 p-3 rounded-xl border border-[#facc15]/40">
                        <span>Total Bill</span>
                        <span class="text-[#0B3022]" id="totalBillDisplay">৳ <?php echo number_format($grand_total); ?></span>
                    </div>
                    
                    <button <?php echo !$is_profile_complete ? 'type="button" onclick="showProfileWarning()"' : 'type="submit" name="place_order"'; ?> id="confirmOrderBtn" class="w-full text-center bg-[#0B3022] text-[#facc15] py-3.5 rounded-xl font-extrabold hover:bg-[#072117] transition shadow-md text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fas <?php echo !$is_profile_complete ? 'fa-lock' : 'fa-check-circle'; ?>"></i> 
                        <?php echo !$is_profile_complete ? 'Complete Profile First' : 'Confirm Order Now'; ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
</style>

<script>
    function togglePaymentBox() {
        const manualBox = document.getElementById('manualPaymentBox');
        const scInput = document.getElementById('screenshotInput');
        const pointsError = document.getElementById('pointsError');
        const confirmBtn = document.getElementById('confirmOrderBtn');
        const totalBillDisplay = document.getElementById('totalBillDisplay');
        const methodTitle = document.getElementById('paymentMethodName');
        const methodNum = document.getElementById('paymentMethodNumber');
        
        const bkashNum = "<?php echo $bkash_number; ?>";
        const nagadNum = "<?php echo $nagad_number; ?>";
        const bankDetails = "<?php echo $bank_details; ?>";
        
        const checkedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const userPoints = <?php echo $user_points; ?>;
        const grandTotal = <?php echo $grand_total; ?>;
        const isProfileComplete = <?php echo $is_profile_complete ? 'true' : 'false'; ?>;

        if(checkedMethod === 'bkash') {
            methodTitle.innerHTML = 'Send Money To (bKash)'; methodNum.innerHTML = bkashNum;
            manualBox.style.display = 'block'; scInput.setAttribute('required', 'required');
            pointsError.classList.add('hidden');
            totalBillDisplay.innerHTML = '৳ ' + grandTotal.toLocaleString();
            if(isProfileComplete) { confirmBtn.disabled = false; }
        } 
        else if(checkedMethod === 'nagad') {
            methodTitle.innerHTML = 'Send Money To (Nagad)'; methodNum.innerHTML = nagadNum;
            manualBox.style.display = 'block'; scInput.setAttribute('required', 'required');
            pointsError.classList.add('hidden');
            totalBillDisplay.innerHTML = '৳ ' + grandTotal.toLocaleString();
            if(isProfileComplete) { confirmBtn.disabled = false; }
        } 
        else if(checkedMethod === 'bank') {
            methodTitle.innerHTML = 'Bank Transfer Details'; methodNum.innerHTML = bankDetails;
            manualBox.style.display = 'block'; scInput.setAttribute('required', 'required');
            pointsError.classList.add('hidden');
            totalBillDisplay.innerHTML = '৳ ' + grandTotal.toLocaleString();
            if(isProfileComplete) { confirmBtn.disabled = false; }
        } 
        else if(checkedMethod === 'points') {
            manualBox.style.display = 'none'; scInput.removeAttribute('required');
            totalBillDisplay.innerHTML = '<i class="fas fa-coins text-[#facc15]"></i> ' + grandTotal.toLocaleString() + ' Coins';
            
            if (userPoints < grandTotal) {
                pointsError.classList.remove('hidden'); 
                confirmBtn.disabled = true;
            } else {
                pointsError.classList.add('hidden'); 
                if(isProfileComplete) { confirmBtn.disabled = false; }
            }
        }
    }
</script>

<?php include 'footer.php'; ?>