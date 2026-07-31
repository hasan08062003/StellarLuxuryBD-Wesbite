<?php
include 'db.php'; 

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// ==========================================
// AJAX REMOVE HANDLER (No Page Reload)
// ==========================================
if(isset($_POST['remove_wish_id'])) {
    $remove_id = (int)$_POST['remove_wish_id'];
    $conn->query("DELETE FROM wishlist WHERE id=$remove_id AND user_id=$user_id");
    
    // Get updated count
    $count_res = $conn->query("SELECT COUNT(*) as total FROM wishlist WHERE user_id = $user_id");
    $count_row = $count_res->fetch_assoc();
    
    echo json_encode(['status' => 'success', 'count' => $count_row['total']]);
    exit;
}

// ==========================================
// FORM SUBMIT & REDIRECT LOGIC FOR CART
// ==========================================
if(isset($_GET['add_to_cart'])) {
    $prod_id = (int)$_GET['add_to_cart'];
    $check = $conn->query("SELECT * FROM cart WHERE user_id=$user_id AND product_id=$prod_id");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $prod_id, 1)");
    } else {
        $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id=$user_id AND product_id=$prod_id");
    }
    header("Location: wishlist.php?cart_added=1"); 
    exit;
}

include 'header.php';
?>

<div class="container mx-auto px-4 py-8 min-h-[70vh]">
    <h2 class="text-2xl font-extrabold text-[#0B3022] mb-6"><i class="fas fa-heart text-red-500 mr-2"></i> My Favorites</h2>

    <!-- কার্টে অ্যাড হলে সাকসেস মেসেজ দেখাবে -->
    <?php if(isset($_GET['cart_added'])): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded-lg font-bold mb-4 shadow-sm border border-green-200">
            <i class="fas fa-check-circle mr-1"></i> Product successfully added to your cart!
        </div>
    <?php endif; ?>

    <div id="wishlist-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php
        $sql = "SELECT wishlist.id as wish_id, products.* FROM wishlist INNER JOIN products ON wishlist.product_id = products.id WHERE wishlist.user_id = $user_id ORDER BY wishlist.id DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
        ?>
            <!-- Product Card -->
            <div id="wishlist-card-<?php echo $row['wish_id']; ?>" class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex gap-4 items-center relative hover:shadow-md transition">
                
                <!-- Delete Button (AJAX No Reload) -->
                <button type="button" onclick="removeWishlistItem(<?php echo $row['wish_id']; ?>)" class="absolute top-2 right-2 text-gray-300 hover:text-red-500 hover:bg-red-50 w-7 h-7 flex items-center justify-center rounded-full transition focus:outline-none" title="Remove">
                    <i class="fas fa-times text-sm"></i>
                </button>
                
                <img src="<?php echo $row['image']; ?>" onerror="this.src='https://placehold.co/400x400/f0f0f0/cccccc?text=No+Image'" class="w-24 h-24 object-cover rounded-md bg-gray-50 p-1 border border-gray-100">
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-gray-800 line-clamp-1 mb-1"><?php echo $row['name']; ?></h3>
                    <p class="text-[#0B3022] font-extrabold mb-2">৳ <?php echo number_format($row['price']); ?></p>
                    
                    <div class="flex gap-2">
                        <!-- Add to Cart Button (Corrected product_id) -->
                        <a href="wishlist.php?add_to_cart=<?php echo $row['id']; ?>" class="flex-1 text-center text-[11px] font-bold bg-[#0B3022] text-[#facc15] px-2 py-1.5 rounded hover:bg-[#072117] transition shadow-sm">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </a>
                        <!-- View Product Button (Corrected product_id) -->
                        <a href="product_variant.php?id=<?php echo $row['id']; ?>" class="text-center text-[11px] font-bold bg-gray-100 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-200 transition">
                            View
                        </a>
                    </div>
                </div>
            </div>
        <?php 
            }
        } else {
            echo "<div id='empty-wishlist-msg' class='col-span-full text-center py-16 bg-white rounded-xl border border-dashed border-gray-300'>
                    <i class='far fa-heart text-4xl text-gray-300 mb-3'></i>
                    <h3 class='text-lg font-bold text-gray-700'>No favorite items yet!</h3>
                    <p class='text-sm text-gray-500'>Browse our collection and add some.</p>
                  </div>";
        }
        ?>
    </div>
</div>

<script>
// 🚀 AJAX Remove from Wishlist page without reload
function removeWishlistItem(wishId) {
    fetch('wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'remove_wish_id=' + wishId
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            // কার্ডটি পেজ থেকে হাইড করে দেওয়া
            const card = document.getElementById('wishlist-card-' + wishId);
            if(card) {
                card.remove();
            }

            // হেডার বা নেভবারের উইশলিস্ট কাউন্ট আপডেট করা
            const wishlistCounters = document.querySelectorAll('.wishlist-count-badge');
            wishlistCounters.forEach(badge => {
                badge.innerText = data.count;
                if(data.count > 0) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                    // যদি সব আইটেম মুছে যায়, তবে খালি মেসেজ দেখানো
                    const container = document.getElementById('wishlist-container');
                    if(container && container.children.length === 0) {
                        container.innerHTML = `
                            <div class='col-span-full text-center py-16 bg-white rounded-xl border border-dashed border-gray-300'>
                                <i class='far fa-heart text-4xl text-gray-300 mb-3'></i>
                                <h3 class='text-lg font-bold text-gray-700'>No favorite items yet!</h3>
                                <p class='text-sm text-gray-500'>Browse our collection and add some.</p>
                            </div>
                        `;
                    }
                }
            });
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?php include 'footer.php'; ?>