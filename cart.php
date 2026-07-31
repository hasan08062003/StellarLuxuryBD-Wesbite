<?php
include 'db.php';

// ইউজার লগিন চেক
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// ==========================================
// 🛠 CART ACTION LOGIC (UPDATE & REMOVE)
// ==========================================

// আইটেম রিমুভ করার লজিক
if(isset($_GET['remove'])) {
    $cart_item_id = (int)$_GET['remove'];
    $conn->query("DELETE FROM cart WHERE id = $cart_item_id AND user_id = $user_id");
    header("Location: cart.php?msg=removed");
    exit;
}

// কোয়ান্টিটি আপডেট করার লজিক
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    if(isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach($_POST['qty'] as $cart_id => $qty) {
            $cart_id = (int)$cart_id;
            $qty = (int)$qty;
            if($qty > 0) {
                $conn->query("UPDATE cart SET quantity = $qty WHERE id = $cart_id AND user_id = $user_id");
            }
        }
    }
    header("Location: cart.php?msg=updated");
    exit;
}

// ==========================================
// FETCH CART ITEMS 
// ==========================================
$cart_sql = "SELECT cart.*, products.name, products.image, products.price, products.stock_status, products.category, 
             COALESCE(product_color_variants.color_name, '') as color_name,
             COALESCE(product_color_variants.variant_image, '') as variant_image
             FROM cart 
             JOIN products ON cart.product_id = products.id 
             LEFT JOIN product_color_variants ON cart.variant_id = product_color_variants.id
             WHERE cart.user_id = $user_id 
             ORDER BY cart.id DESC";
$cart_res = $conn->query($cart_sql);

$subtotal = 0;
$total_items = 0;

include 'header.php';
?>

<main class="container mx-auto px-3 md:px-6 py-6 md:py-10 flex-grow min-h-[70vh] bg-gray-50 pb-28">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-3">
            <h1 class="text-xl md:text-2xl font-black text-[#0B3022] flex items-center gap-2">
                <i class="fas fa-shopping-bag text-[#facc15]"></i> Shopping Cart
            </h1>
            <a href="index.php" class="text-xs font-bold text-gray-500 hover:text-[#0B3022] transition"><i class="fas fa-arrow-left mr-1"></i> Continue Shopping</a>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'removed'): ?>
            <div class="bg-red-50 text-red-600 p-3.5 rounded-xl font-bold mb-6 border border-red-200 shadow-sm flex items-center text-xs md:text-sm">
                <i class="fas fa-trash-alt mr-2"></i> Item removed from cart!
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="bg-green-50 text-green-600 p-3.5 rounded-xl font-bold mb-6 border border-green-200 shadow-sm flex items-center text-xs md:text-sm">
                <i class="fas fa-check-circle mr-2"></i> Cart updated successfully!
            </div>
        <?php endif; ?>

        <?php if($cart_res && $cart_res->num_rows > 0): ?>
            
            <form action="cart.php" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left Side: Cart Items List (7 Cols) -->
                <div class="lg:col-span-7 space-y-3">
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 space-y-3">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Cart Items (<?php echo $cart_res->num_rows; ?>)</h3>
                        
                        <?php while($item = $cart_res->fetch_assoc()): 
                            $item_total = $item['price'] * $item['quantity'];
                            $subtotal += $item_total;
                            $total_items += $item['quantity'];
                            $display_image = !empty($item['variant_image']) ? $item['variant_image'] : $item['image'];
                        ?>
                        
                        <div class="bg-gray-50/70 rounded-xl p-3 border border-gray-100 flex items-center gap-3 relative group hover:border-[#0B3022]/30 transition">
                            
                            <!-- Product Image -->
                            <a href="product_variant.php?id=<?php echo $item['product_id']; ?>" class="w-14 h-14 flex-shrink-0 bg-white rounded-lg overflow-hidden border border-gray-200 p-0.5 block">
                                <img src="<?php echo $display_image; ?>" onerror="this.src='https://placehold.co/200x200?text=No+Image'" class="w-full h-full object-cover rounded">
                            </a>
                            
                            <div class="flex-1 min-w-0">
                                <a href="product_variant.php?id=<?php echo $item['product_id']; ?>" class="block">
                                    <h3 class="text-xs md:text-sm font-extrabold text-gray-800 truncate mb-0.5 hover:text-[#0B3022] transition">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </h3>
                                </a>
                                
                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                    <?php if(!empty($item['color_name'])): ?>
                                        <span class="text-[9px] font-black text-purple-700 bg-purple-50 border border-purple-200 px-1.5 py-0.2 rounded">Color: <?php echo htmlspecialchars($item['color_name']); ?></span>
                                    <?php endif; ?>
                                    <span class="text-xs font-black text-[#0B3022]">৳ <?php echo number_format($item['price']); ?></span>
                                </div>
                                
                                <!-- Quantity Controls -->
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center border border-gray-300 rounded-lg w-24 h-7 bg-white overflow-hidden shadow-xs">
                                        <button type="button" onclick="decreaseQty(<?php echo $item['id']; ?>)" class="w-1/3 h-full text-gray-600 hover:bg-gray-100 font-bold text-sm transition">-</button>
                                        <input type="text" id="qty_<?php echo $item['id']; ?>" name="qty[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" class="w-1/3 h-full text-center border-x border-gray-200 text-xs font-bold focus:outline-none bg-white" readonly>
                                        <button type="button" onclick="increaseQty(<?php echo $item['id']; ?>)" class="w-1/3 h-full text-gray-600 hover:bg-gray-100 font-bold text-sm transition">+</button>
                                    </div>
                                    
                                    <span class="text-[11px] font-bold text-gray-500">Total: <span class="text-gray-800 font-black">৳ <?php echo number_format($item_total); ?></span></span>
                                </div>
                            </div>

                            <!-- Remove Button -->
                            <a href="cart.php?remove=<?php echo $item['id']; ?>" class="absolute top-3 right-3 text-gray-300 hover:text-red-500 transition" title="Remove">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </a>
                        </div>
                        <?php endwhile; ?>
                        
                        <div class="flex justify-end pt-1">
                            <button type="submit" name="update_cart" class="bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-[#0B3022] text-xs font-extrabold px-4 py-2 rounded-xl transition border border-gray-200 flex items-center gap-1.5">
                                <i class="fas fa-sync-alt"></i> Update Cart
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Right Side: Order Summary (5 Cols) -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-24">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-3 border-b pb-2">Cart Summary</h3>
                        
                        <div class="space-y-2 text-xs font-medium mb-3">
                            <div class="flex justify-between text-gray-500">
                                <span>Total Items</span>
                                <span class="font-bold text-gray-800"><?php echo $total_items; ?> Pcs</span>
                            </div>
                            <div class="flex justify-between text-gray-500">
                                <span>Subtotal</span>
                                <span class="font-bold text-gray-800">৳ <?php echo number_format($subtotal); ?></span>
                            </div>
                            <div class="flex justify-between text-gray-400 text-[11px]">
                                <span>Shipping & Taxes</span>
                                <span>Calculated at checkout</span>
                            </div>
                        </div>

                        <div class="flex justify-between my-4 text-base font-black text-gray-800 bg-[#facc15]/20 p-3 rounded-xl border border-[#facc15]/40">
                            <span>Estimated Total</span>
                            <span class="text-[#0B3022]">৳ <?php echo number_format($subtotal); ?></span>
                        </div>
                        
                        <a href="checkout.php" class="w-full block text-center bg-[#0B3022] text-[#facc15] py-3.5 rounded-xl font-extrabold hover:bg-[#072117] transition shadow-md text-sm">
                            Proceed to Checkout <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                        
                        <!-- Trust Badges -->
                        <div class="mt-4 flex justify-center gap-4 opacity-70 grayscale text-sm">
                            <i class="fas fa-shield-alt text-gray-400" title="Secure Payment"></i>
                            <i class="fas fa-undo text-gray-400" title="Easy Returns"></i>
                            <i class="fas fa-truck-fast text-gray-400" title="Fast Delivery"></i>
                        </div>
                    </div>
                </div>

            </form>

        <?php else: ?>
            <!-- Empty Cart State -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center max-w-lg mx-auto mt-6">
                <div class="w-20 h-20 bg-[#0B3022]/5 rounded-full flex items-center justify-center mx-auto mb-4 border border-[#0B3022]/10">
                    <i class="fas fa-shopping-cart text-3xl text-[#0B3022] opacity-50"></i>
                </div>
                <h2 class="text-xl font-extrabold text-gray-800 mb-1">Your cart is empty!</h2>
                <p class="text-xs text-gray-500 font-medium mb-6">Looks like you haven't added anything to your cart yet.</p>
                <a href="index.php" class="inline-block bg-[#0B3022] text-[#facc15] font-extrabold px-6 py-3 rounded-xl hover:bg-[#072117] transition shadow text-xs">
                    <i class="fas fa-gem mr-1"></i> Start Shopping
                </a>
            </div>
        <?php endif; ?>

    </div>
</main>

<script>
    function increaseQty(cartId) {
        let qtyInput = document.getElementById('qty_' + cartId);
        let currentVal = parseInt(qtyInput.value);
        if(currentVal < 20) { 
            qtyInput.value = currentVal + 1;
        } else {
            alert("Maximum limit reached for this item!");
        }
    }

    function decreaseQty(cartId) {
        let qtyInput = document.getElementById('qty_' + cartId);
        let currentVal = parseInt(qtyInput.value);
        if(currentVal > 1) { 
            qtyInput.value = currentVal - 1;
        }
    }
</script>

<?php include 'footer.php'; ?>