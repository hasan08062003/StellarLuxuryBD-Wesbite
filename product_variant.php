<?php
include 'db.php'; 

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$product_query = $conn->query("SELECT * FROM products WHERE id = $product_id");
if($product_query->num_rows > 0){
    $product = $product_query->fetch_assoc();
} else {
    include 'header.php'; echo "<h2 class='text-center mt-10 text-red-500 font-bold'>Product not found!</h2>"; exit;
}

$current_status = strtolower(trim($product['stock_status']));
$is_out_of_stock = in_array($current_status, ['out of stock', 'not available', 'unavailable']);
$is_pre_order = in_array($current_status, ['pre-order', 'pre order', 'preorder']);

$variants_res = $conn->query("SELECT * FROM product_color_variants WHERE product_id = $product_id");
$variants = [];
while($v = $variants_res->fetch_assoc()) {
    $variants[] = $v;
}

// 🚀 DIRECT BUY NOW (ORDER NOW) LOGIC FOR VARIANTS (No Cart Mixing)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_now'])) {
    if ($user_id == 0) { header("Location: login.php"); exit; }
    
    if (!$is_out_of_stock) {
        $variant_quantities = $_POST['variant_qty'] ?? [];
        $direct_items = [];
        $total_added = 0;

        foreach($variant_quantities as $var_id => $qty) {
            $qty = (int)$qty;
            if($qty > 0) {
                $total_added += $qty;
                // Fetch variant info
                $v_query = $conn->query("SELECT * FROM product_color_variants WHERE id = $var_id");
                if($v_query->num_rows > 0) {
                    $v_data = $v_query->fetch_assoc();
                    $direct_items[] = [
                        'product_id' => $product_id,
                        'variant_id' => $var_id,
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'color_name' => $v_data['color_name'],
                        'variant_image' => $v_data['variant_image'],
                        'checkout_qty' => $qty
                    ];
                }
            }
        }

        if($total_added == 0) {
            echo "<script>alert('Please select at least one quantity for a color variant!'); window.history.back();</script>";
            exit;
        }

        // Save into session for direct checkout
        $_SESSION['direct_checkout_items'] = $direct_items;

        header("Location: checkout.php?direct_buy=1"); 
        exit;
    }
}

$is_favorite = false;
if ($user_id > 0) {
    $fav_check = $conn->query("SELECT * FROM wishlist WHERE user_id=$user_id AND product_id=$product_id");
    if ($fav_check->num_rows > 0) { $is_favorite = true; }
}

$sales_query = $conn->query("SELECT SUM(quantity) as total_sold FROM order_items WHERE product_id = $product_id");
$sales_data = $sales_query->fetch_assoc();
$total_sold = $sales_data['total_sold'] ? (int)$sales_data['total_sold'] : 0;

$main_image = !empty($product['image']) ? $product['image'] : 'https://placehold.co/600x600?text=No+Image';
$product_video = !empty($product['video']) ? $product['video'] : null;

$gallery_images = [];
$gallery_images[] = $main_image; 
if(!empty($product['image2'])) { $gallery_images[] = $product['image2']; }
if(!empty($product['image3'])) { $gallery_images[] = $product['image3']; }
if(!empty($product['image4'])) { $gallery_images[] = $product['image4']; }

include 'header.php'; 
?>

<main class="w-full bg-gray-50 min-h-[70vh] pb-24 md:pb-12 pt-2 md:pt-8 flex-grow">
    
    <div class="max-w-6xl mx-auto md:px-4">
        
        <nav class="flex items-center text-[10px] md:text-xs font-bold text-gray-500 mb-2 md:mb-4 px-4 md:px-0">
            <a href="index.php" class="hover:text-[#0B3022] transition"><i class="fas fa-home"></i> Home</a>
            <i class="fas fa-angle-right mx-1.5 md:mx-2"></i>
            <span class="text-[#0B3022] uppercase truncate"><?php echo htmlspecialchars($product['category']); ?></span>
        </nav>

        <div class="bg-white md:rounded-2xl shadow-sm border-t border-b md:border border-gray-100 overflow-hidden mb-4 md:mb-8">
            <div class="flex flex-col lg:flex-row">
                
                <!-- LEFT SIDE: GALLERY -->
                <div class="w-full lg:w-1/2 p-4 md:p-6 border-b lg:border-b-0 lg:border-r border-gray-100 bg-white flex flex-col">
                    <div class="relative w-full aspect-square bg-gray-50 rounded-lg overflow-hidden shadow-inner group flex items-center justify-center border border-gray-100" id="main-media-container">
                        <img src="<?php echo $main_image; ?>" id="main-image" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <a href="<?php echo $main_image; ?>" download="Product_Image_<?php echo $product_id; ?>" id="download-btn" class="absolute bottom-3 right-3 w-8 h-8 md:w-10 md:h-10 bg-white/90 backdrop-blur rounded-full flex items-center justify-center text-[#0B3022] shadow-lg hover:bg-[#0B3022] hover:text-[#facc15] transition z-10 text-xs md:text-base" title="Download Image">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>

                    <div class="flex gap-2 md:gap-3 mt-3 md:mt-4 overflow-x-auto no-scrollbar py-1">
                        <?php if($product_video): ?>
                        <div onclick="changeMedia('video', '<?php echo $product_video; ?>')" class="w-16 h-16 md:w-20 md:h-20 flex-shrink-0 rounded-lg overflow-hidden border-2 border-transparent hover:border-[#facc15] cursor-pointer relative shadow-sm transition">
                            <video src="<?php echo $product_video; ?>" class="w-full h-full object-cover opacity-80"></video>
                            <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                                <i class="fas fa-play text-white text-sm md:text-xl drop-shadow-md"></i>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php foreach($gallery_images as $img): ?>
                        <div onclick="changeMedia('image', '<?php echo $img; ?>')" class="w-16 h-16 md:w-20 md:h-20 flex-shrink-0 rounded-lg overflow-hidden border-2 border-transparent hover:border-[#0B3022] cursor-pointer shadow-sm transition thumbnail-img">
                            <img src="<?php echo $img; ?>" class="w-full h-full object-cover">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- RIGHT SIDE: DETAILS -->
                <div class="w-full lg:w-1/2 p-4 md:p-8 lg:p-10 flex flex-col justify-center relative">
                    
                    <button type="button" onclick="shareProduct()" class="absolute top-4 right-4 md:top-6 md:right-6 w-8 h-8 md:w-10 md:h-10 bg-gray-50 border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-[#0B3022] hover:border-[#0B3022] transition shadow-sm text-xs md:text-base cursor-pointer z-10" title="Share Product">
                        <i class="fas fa-share-nodes"></i>
                    </button>

                    <div class="flex items-center gap-2 mb-2 md:mb-4 flex-wrap pr-10">
                        <span class="text-[9px] md:text-xs font-bold text-[#0B3022] bg-[#0B3022]/10 px-2 py-1 md:px-3 md:py-1.5 rounded uppercase tracking-wider border border-[#0B3022]/20">
                            <i class="fas fa-tag mr-1"></i> <?php echo htmlspecialchars($product['category']); ?>
                        </span>
                        
                        <?php if($total_sold >= 10): ?>
                            <span class="text-[9px] md:text-xs font-black text-red-600 bg-red-50 px-2 py-1 md:px-3 md:py-1.5 rounded uppercase tracking-wider border border-red-200 shadow-sm animate-pulse">
                                <i class="fas fa-fire mr-1"></i> <?php echo $total_sold; ?>+ Sold
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="text-xl md:text-3xl lg:text-4xl font-extrabold text-gray-800 mb-2 md:mb-4 leading-snug"><?php echo htmlspecialchars($product['name']); ?></h1>

                    <div class="flex items-center gap-2 mb-4 md:mb-6">
                        <div class="flex text-[#facc15] text-[10px] md:text-sm">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="text-[10px] md:text-xs font-bold text-gray-500 underline cursor-pointer" onclick="document.getElementById('reviews-section').scrollIntoView({behavior: 'smooth'})">See Reviews</span>
                    </div>

                    <div class="bg-gradient-to-r from-[#0B3022] to-[#072117] rounded-xl p-4 md:p-5 mb-6 md:mb-8 shadow-lg text-white flex justify-between items-center relative overflow-hidden">
                        <i class="fas fa-coins absolute -right-2 -bottom-2 text-6xl md:text-7xl text-white opacity-5"></i>
                        
                        <div class="relative z-10">
                            <p class="text-[9px] md:text-[10px] text-gray-300 font-bold uppercase tracking-widest mb-0.5">Exclusive Price</p>
                            <div class="flex items-end gap-2 md:gap-3">
                                <span class="text-2xl md:text-4xl font-black text-[#facc15] tracking-tight">৳ <?php echo number_format($product['price']); ?></span>
                                <?php if($product['old_price'] > $product['price']) { ?>
                                    <span class="text-sm md:text-lg text-gray-400 line-through mb-1">৳ <?php echo number_format($product['old_price']); ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <div class="border-l border-white/20 pl-3 md:pl-6 relative z-10 text-right">
                            <p class="text-[9px] md:text-[10px] text-gray-300 font-bold uppercase tracking-widest mb-0.5">Pay with Points</p>
                            <span class="text-base md:text-xl font-bold flex items-center justify-end gap-1.5">
                                <i class="fas fa-coins text-[#facc15]"></i> <?php echo number_format($product['price']); ?>
                            </span>
                        </div>
                    </div>

                    <form method="POST" id="variantProductForm" class="mt-auto">
                        
                        <div class="mb-4 border-b border-gray-100 pb-4">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-[10px] md:text-xs font-bold text-gray-500 uppercase">Availability & Select Variants</p>
                                <?php 
                                    if($is_out_of_stock) {
                                        $stock_class = 'text-red-600';
                                        $display_status = 'Out of Stock';
                                    } elseif($is_pre_order) {
                                        $stock_class = 'text-orange-600';
                                        $display_status = 'Pre-order';
                                    } else {
                                        $stock_class = 'text-green-600';
                                        $display_status = 'In Stock';
                                    }
                                ?>
                                <span class="text-xs md:text-sm font-extrabold <?php echo $stock_class; ?>">
                                    <i class="fas <?php echo ($is_out_of_stock) ? 'fa-times-circle' : 'fa-check-circle'; ?> mr-1"></i> <?php echo $display_status; ?>
                                </span>
                            </div>

                            <!-- 🚀 Pre-order Bangla Notice Box -->
                            <?php if($is_pre_order): ?>
                            <div class="mb-3 bg-amber-50 border-l-4 border-amber-500 p-3.5 rounded-r-xl shadow-xs">
                                <div class="flex items-start gap-3">
                                    <div class="w-7 h-7 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0 text-amber-600 font-bold mt-0.5">
                                        <i class="fas fa-clock text-xs"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-[11px] md:text-xs font-black text-amber-800 uppercase tracking-wide">বিশেষ প্রি-অর্ডার নোটিশ</h4>
                                        <p class="text-[10px] md:text-xs text-amber-900 font-medium leading-relaxed mt-1">
                                            এই প্রি-অর্ডার প্রোডাক্টটি আপনার হাতে পৌঁছাতে প্রায় <span class="font-extrabold text-red-600 underline">৩০ থেকে ৪০ দিন</span> সময় লাগতে পারে। অর্ডারের যেকোনো আপডেট পেতে আমাদের সাথে সরাসরি <a href="https://wa.me/8801304464043" target="_blank" class="text-green-700 font-extrabold hover:underline inline-flex items-center gap-0.5"><i class="fab fa-whatsapp"></i> WhatsApp-এ</a> যোগাযোগ রাখার অনুরোধ করা হচ্ছে।
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- VARIANT ROW BOX CONTAINER -->
                            <?php if(count($variants) > 0): ?>
                            <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 max-h-[220px] overflow-y-auto custom-scrollbar space-y-2">
                                <?php foreach($variants as $index => $var): ?>
                                <div class="bg-white p-2 rounded-lg border border-gray-200 flex items-center justify-between gap-3 shadow-sm">
                                    <div class="flex items-center gap-2.5">
                                        <div class="relative w-10 h-10 flex-shrink-0">
                                            <img src="<?php echo $var['variant_image']; ?>" onclick="changeMedia('image', '<?php echo $var['variant_image']; ?>')" class="w-full h-full object-cover rounded border cursor-pointer">
                                            <!-- Red Badge Count -->
                                            <span id="badge-<?php echo $index; ?>" class="absolute -top-2 -right-2 bg-red-600 text-white text-[8px] font-black px-1.5 py-0.2 rounded-full hidden shadow">0</span>
                                            <!-- Download Icon Button -->
                                            <a href="<?php echo $var['variant_image']; ?>" download="Variant_<?php echo $var['color_name']; ?>" class="absolute bottom-0 right-0 bg-black/70 text-white text-[7px] px-1 rounded-bl hover:bg-black font-bold" title="Download"><i class="fas fa-download"></i></a>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-gray-800"><?php echo htmlspecialchars($var['color_name']); ?></p>
                                            <p class="text-[9px] text-gray-400 font-bold">৳<?php echo number_format($product['price']); ?></p>
                                        </div>
                                    </div>

                                    <!-- Quantity Selector Box -->
                                    <div class="flex items-center border border-gray-300 rounded bg-gray-50 overflow-hidden h-8">
                                        <button type="button" onclick="changeVariantQty(<?php echo $index; ?>, -1)" class="px-2.5 text-gray-600 hover:bg-gray-200 font-bold">-</button>
                                        <input type="text" id="var-qty-<?php echo $index; ?>" name="variant_qty[<?php echo $var['id']; ?>]" value="0" readonly class="w-8 text-center font-black text-xs text-gray-800 outline-none bg-white variant-qty-input">
                                        <button type="button" onclick="changeVariantQty(<?php echo $index; ?>, 1)" class="px-2.5 text-gray-600 hover:bg-gray-200 font-bold">+</button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                                <div class="flex items-center border-2 border-gray-200 rounded-lg w-28 h-9 bg-gray-50 overflow-hidden">
                                    <button type="button" onclick="decreaseQty()" class="w-1/3 h-full text-gray-600 hover:bg-gray-200 font-bold">-</button>
                                    <input type="text" id="qty" name="quantity" value="1" class="w-1/3 h-full text-center border-x-2 border-gray-200 text-xs font-bold focus:outline-none bg-white" readonly>
                                    <button type="button" onclick="increaseQty()" class="w-1/3 h-full text-gray-600 hover:bg-gray-200 font-bold">+</button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 md:gap-3">
                            <button type="button" onclick="toggleWishlist(<?php echo $product_id; ?>, this)" class="w-12 h-12 md:w-14 md:h-14 flex-shrink-0 border-2 rounded-xl flex items-center justify-center text-lg md:text-xl transition <?php echo $is_favorite ? 'border-red-500 text-red-500 bg-red-50' : 'border-gray-200 text-gray-400 hover:border-red-500 hover:text-red-500 bg-white'; ?> focus:outline-none">
                                <i class="<?php echo $is_favorite ? 'fas text-red-500' : 'far text-gray-400'; ?> fa-heart"></i>
                            </button>
                            
                            <?php if($is_out_of_stock): ?>
                                <button type="button" disabled class="flex-1 bg-gray-100 border-2 border-gray-200 text-gray-400 h-12 md:h-14 rounded-xl font-extrabold cursor-not-allowed flex justify-center items-center gap-1.5 text-[11px] md:text-sm">
                                    <i class="fas fa-ban"></i> Out of Stock
                                </button>
                                <button type="button" disabled class="flex-1 bg-gray-300 text-gray-500 h-12 md:h-14 rounded-xl font-extrabold cursor-not-allowed shadow-none flex justify-center items-center gap-1.5 text-[11px] md:text-sm">
                                    <i class="fas fa-ban"></i> Out of Stock
                                </button>
                            <?php else: ?>
                                <button type="button" onclick="addVariantToCartAjax(<?php echo $product_id; ?>)" class="flex-1 bg-white border-2 border-[#0B3022] text-[#0B3022] h-12 md:h-14 rounded-xl font-extrabold hover:bg-[#0B3022] hover:text-white transition flex justify-center items-center gap-1.5 text-[11px] md:text-sm focus:outline-none">
                                    <i class="fas fa-cart-plus text-sm md:text-lg"></i> Add to Cart
                                </button>
                                <button type="submit" name="buy_now" class="flex-1 bg-[#0B3022] text-[#facc15] h-12 md:h-14 rounded-xl font-extrabold hover:bg-[#072117] transition shadow-md flex justify-center items-center gap-1.5 text-[11px] md:text-sm">
                                    <i class="fas fa-bolt text-sm md:text-lg"></i> Order Now
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>

                </div>
            </div>

            <div class="border-t border-gray-100 bg-gray-50/50 p-4 md:p-10">
                <h3 class="text-sm md:text-lg font-extrabold text-[#0B3022] mb-3 md:mb-4 flex items-center gap-2">
                    <i class="fas fa-align-left text-[#facc15]"></i> Product Description
                </h3>
                <div class="bg-white p-4 md:p-6 rounded-xl border border-gray-200 shadow-sm text-xs md:text-base text-gray-600 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
            </div>
        </div>

    </div>
</main>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
    // 🚀 Fully Working Media Switcher for Image & Video with Download Button
    function changeMedia(type, src) {
        const container = document.getElementById('main-media-container');
        if(type === 'image') {
            container.innerHTML = `
                <img src="${src}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                <a href="${src}" download="Product_Image" class="absolute bottom-3 right-3 w-8 h-8 md:w-10 md:h-10 bg-white/90 backdrop-blur rounded-full flex items-center justify-center text-[#0B3022] shadow-lg hover:bg-[#0B3022] hover:text-[#facc15] transition z-10 text-xs md:text-base" title="Download Image"><i class="fas fa-download"></i></a>
            `;
        } else if(type === 'video') {
            container.innerHTML = `
                <video src="${src}" controls autoplay class="w-full h-full object-cover"></video>
                <a href="${src}" download="Product_Video.mp4" class="absolute bottom-3 right-3 w-8 h-8 md:w-10 md:h-10 bg-white/90 backdrop-blur rounded-full flex items-center justify-center text-[#0B3022] shadow-lg hover:bg-[#0B3022] hover:text-[#facc15] transition z-10 text-xs md:text-base" title="Download Video"><i class="fas fa-download"></i></a>
            `;
        }
    }

    // 🚀 Fully Working Share Button
    function shareProduct() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo addslashes($product["name"]); ?>',
                text: 'Check out this premium product from StellarLuxury BD!',
                url: window.location.href
            }).catch(console.error);
        } else {
            navigator.clipboard.writeText(window.location.href);
            alert('Product Link Copied to Clipboard!');
        }
    }

    function addVariantToCartAjax(productId) {
        const form = document.getElementById('variantProductForm');
        const formData = new FormData(form);
        formData.append('product_id', productId);

        fetch('add_to_cart_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'not_logged_in') {
                window.location.href = 'login.php';
                return;
            }

            if(data.status === 'success') {
                const cartCounters = document.querySelectorAll('.cart-count-badge');
                cartCounters.forEach(badge => {
                    badge.innerText = data.cart_count;
                    if(data.cart_count > 0) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                });

                showToast("Selected color variants added to cart!");
            } else {
                alert("Please select at least one quantity for a color variant!");
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function toggleWishlist(productId, element) {
        fetch('toggle_wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'not_logged_in') {
                window.location.href = 'login.php';
                return;
            }

            if(data.status === 'success') {
                const icon = element.querySelector('i');
                if(data.action === 'added') {
                    element.classList.remove('bg-white', 'border-gray-200');
                    element.classList.add('bg-red-50', 'border-red-500');
                    if(icon) {
                        icon.classList.remove('far', 'text-gray-400');
                        icon.classList.add('fas', 'text-red-500');
                    }
                } else {
                    element.classList.remove('bg-red-50', 'border-red-500');
                    element.classList.add('bg-white', 'border-gray-200');
                    if(icon) {
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far', 'text-gray-400');
                    }
                }

                const wishlistCounters = document.querySelectorAll('.wishlist-count-badge');
                wishlistCounters.forEach(badge => {
                    badge.innerText = data.count;
                    if(data.count > 0) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                });
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-5 right-5 bg-[#0B3022] text-[#facc15] font-bold px-5 py-3 rounded-xl shadow-2xl z-50 transition-all duration-300 border border-[#facc15]/30 text-xs md:text-sm flex items-center gap-2';
        toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }

    function changeVariantQty(index, change) {
        const input = document.getElementById('var-qty-' + index);
        const badge = document.getElementById('badge-' + index);
        let currentVal = parseInt(input.value) + change;

        if (currentVal >= 0) {
            input.value = currentVal;
            if (currentVal > 0) {
                badge.innerText = currentVal;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }
</script>

<?php include 'footer.php'; ?>