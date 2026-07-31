<?php 
include 'db.php'; 

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

include 'header.php'; 

$selected_category = isset($_GET['category']) ? $_GET['category'] : "";
$where_clause = "";

if(!empty($selected_category)) {
    $escaped_category = $conn->real_escape_string($selected_category);
    $where_clause = "WHERE category = '$escaped_category'";
}

$wishlist_items = [];
if($user_id > 0) {
    $w_res = $conn->query("SELECT product_id FROM wishlist WHERE user_id = $user_id");
    while($w_row = $w_res->fetch_assoc()) {
        $wishlist_items[] = $w_row['product_id'];
    }
}
?>

    <!-- 🌟 Desktop Premium Unique Background Art 🌟 -->
    <div class="fixed inset-0 z-[-1] hidden md:block bg-[#fafafa] pointer-events-none overflow-hidden">
        <div class="absolute top-[-10%] left-[-5%] w-[500px] h-[500px] bg-[#0B3022] opacity-[0.03] rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[600px] h-[600px] bg-[#facc15] opacity-[0.04] rounded-full blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.015]" style="background-image: radial-gradient(#0B3022 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="absolute top-[20%] right-[8%] w-32 h-32 border-[1px] border-[#0B3022] opacity-[0.05] transform rotate-45"></div>
        <div class="absolute bottom-[30%] left-[5%] w-48 h-48 border-[1px] border-[#facc15] opacity-[0.05] transform rotate-[30deg]"></div>
        <i class="fas fa-crown absolute top-[10%] left-[45%] text-[10rem] text-[#0B3022] opacity-[0.015] transform rotate-[15deg]"></i>
        <i class="fas fa-gem absolute bottom-[10%] right-[40%] text-[15rem] text-[#0B3022] opacity-[0.015] transform -rotate-[10deg]"></i>
    </div>

    <!-- 1. Modern 3-Part Full-Screen Hero Section -->
    <section class="relative w-full px-0 lg:px-4 mt-0 md:mt-4 z-10" id="hero-section">
        <div class="flex flex-col lg:flex-row gap-3 lg:gap-4 h-auto lg:h-[450px] xl:h-[500px]">
            
            <!-- Left Poster -->
            <div class="hidden lg:block lg:w-[20%] rounded-none lg:rounded-xl overflow-hidden shadow-sm relative group bg-white border border-gray-100">
                <img src="images/poster-left.jpg" onerror="this.src='https://placehold.co/400x600/0B3022/facc15?text=New+Arrivals'" alt="Poster Left" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition duration-500"></div>
                <div class="absolute bottom-6 left-0 right-0 text-center">
                    <span class="bg-white/90 backdrop-blur text-[#0B3022] text-xs font-black px-4 py-2 rounded-full shadow-lg uppercase tracking-widest">New Arrivals</span>
                </div>
            </div>

            <!-- Center Sliding Banner -->
            <div class="w-full lg:w-[60%] relative overflow-hidden group lg:rounded-xl shadow-sm bg-[#0B3022]" id="banner-container">
                <div id="slider" class="flex transition-transform duration-700 ease-in-out h-[250px] sm:h-[300px] lg:h-full w-full">
                    <div class="w-full flex-shrink-0 relative">
                        <img src="images/Cover Photo.jpg" onerror="this.src='https://placehold.co/1200x500/0B3022/ffffff?text=Main+Banner+1'" alt="Banner 1" class="w-full h-full object-cover">
                    </div>
                    <div class="w-full flex-shrink-0 relative">
                        <img src="images/Cover Photo.jpg" onerror="this.src='https://placehold.co/1200x500/082117/ffffff?text=Main+Banner+2'" alt="Banner 2" class="w-full h-full object-cover">
                    </div>
                </div>
                
                <div class="absolute bottom-3 md:bottom-6 left-0 right-0 flex justify-center gap-2 z-10">
                    <div id="dot0" class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white shadow-md transition-all duration-300 opacity-100 w-4 md:w-8"></div>
                    <div id="dot1" class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white shadow-md transition-all duration-300 opacity-50"></div>
                </div>
            </div>

            <!-- Right Poster -->
            <div class="hidden lg:block lg:w-[20%] rounded-none lg:rounded-xl overflow-hidden shadow-sm relative group bg-white border border-gray-100">
                <img src="images/poster-right.jpg" onerror="this.src='https://placehold.co/400x600/082117/facc15?text=Trending+Now'" alt="Poster Right" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition duration-500"></div>
                <div class="absolute bottom-6 left-0 right-0 text-center">
                    <span class="bg-[#facc15]/90 backdrop-blur text-[#0B3022] text-xs font-black px-4 py-2 rounded-full shadow-lg uppercase tracking-widest">Trending Now</span>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. Interactive Circular Categories Section with Emojis & Dropdowns -->
    <div class="bg-white border-b border-gray-200 mb-6 shadow-sm mt-4 md:mt-8 relative z-40">
        <div class="w-full px-4 lg:px-8 py-4 flex flex-col items-center justify-center relative">
            
            <?php if(!empty($selected_category)) { ?>
                <div class="flex justify-between items-center w-full mb-3 px-2 max-w-7xl mx-auto">
                    <h3 class="font-bold text-gray-800 text-xs md:text-sm">Showing Category: <span class="text-[#0B3022] font-extrabold bg-[#facc15]/20 px-2 py-0.5 rounded"><?php echo $selected_category; ?></span></h3>
                    <a href="index.php" class="text-[10px] md:text-xs bg-red-50 text-red-500 font-bold px-3 py-1.5 rounded-md hover:bg-red-500 hover:text-white transition shadow-sm">Clear Filter <i class="fas fa-times ml-1"></i></a>
                </div>
            <?php } ?>

            <!-- Horizontal Scrollable Categories -->
            <div class="flex gap-4 md:gap-8 overflow-x-auto no-scrollbar w-full justify-start md:justify-center py-4 px-2 relative max-w-[1400px] mx-auto">
                
                <!-- 1. Jewelry 💎 -->
                <div class="flex flex-col items-center min-w-[65px] md:min-w-[85px] group cursor-pointer flex-shrink-0" onclick="toggleCategoryMenu(event, 'dropdown-jewelry', this)">
                    <div class="w-14 h-14 md:w-[70px] md:h-[70px] rounded-full border-[3px] border-transparent hover:border-[#0B3022] bg-[#0B3022]/5 transition-all duration-300 shadow-sm hover:shadow-md mb-2 flex items-center justify-center">
                        <span class="text-2xl md:text-3xl transform group-hover:scale-110 transition-transform">💎</span>
                    </div>
                    <span class="text-[11px] md:text-[13px] font-bold text-gray-600 group-hover:text-[#0B3022] flex items-center gap-1">
                        Jewelry <i class="fas fa-chevron-down text-[8px]"></i>
                    </span>
                </div>

                <!-- 2. Bags 👜 -->
                <div class="flex flex-col items-center min-w-[65px] md:min-w-[85px] group cursor-pointer flex-shrink-0" onclick="toggleCategoryMenu(event, 'dropdown-bags', this)">
                    <div class="w-14 h-14 md:w-[70px] md:h-[70px] rounded-full border-[3px] border-transparent hover:border-pink-500 bg-pink-50 transition-all duration-300 shadow-sm hover:shadow-md mb-2 flex items-center justify-center">
                        <span class="text-2xl md:text-3xl transform group-hover:scale-110 transition-transform">👜</span>
                    </div>
                    <span class="text-[11px] md:text-[13px] font-bold text-gray-600 group-hover:text-pink-600 flex items-center gap-1">
                        Bags <i class="fas fa-chevron-down text-[8px]"></i>
                    </span>
                </div>

                <!-- 3. Shoes 👠 -->
                <div class="flex flex-col items-center min-w-[65px] md:min-w-[85px] group cursor-pointer flex-shrink-0" onclick="toggleCategoryMenu(event, 'dropdown-shoes', this)">
                    <div class="w-14 h-14 md:w-[70px] md:h-[70px] rounded-full border-[3px] border-transparent hover:border-blue-500 bg-blue-50 transition-all duration-300 shadow-sm hover:shadow-md mb-2 flex items-center justify-center">
                        <span class="text-2xl md:text-3xl transform group-hover:scale-110 transition-transform">👠</span>
                    </div>
                    <span class="text-[11px] md:text-[13px] font-bold text-gray-600 group-hover:text-blue-600 flex items-center gap-1">
                        Shoes <i class="fas fa-chevron-down text-[8px]"></i>
                    </span>
                </div>

                <!-- 4. Beauty 💄 -->
                <div class="flex flex-col items-center min-w-[65px] md:min-w-[85px] group cursor-pointer flex-shrink-0" onclick="toggleCategoryMenu(event, 'dropdown-beauty', this)">
                    <div class="w-14 h-14 md:w-[70px] md:h-[70px] rounded-full border-[3px] border-transparent hover:border-purple-500 bg-purple-50 transition-all duration-300 shadow-sm hover:shadow-md mb-2 flex items-center justify-center">
                        <span class="text-2xl md:text-3xl transform group-hover:scale-110 transition-transform">💄</span>
                    </div>
                    <span class="text-[11px] md:text-[13px] font-bold text-gray-600 group-hover:text-purple-600 flex items-center gap-1">
                        Beauty <i class="fas fa-chevron-down text-[8px]"></i>
                    </span>
                </div>

                <!-- 5. Baby 🍼 -->
                <div class="flex flex-col items-center min-w-[65px] md:min-w-[85px] group cursor-pointer flex-shrink-0" onclick="toggleCategoryMenu(event, 'dropdown-baby', this)">
                    <div class="w-14 h-14 md:w-[70px] md:h-[70px] rounded-full border-[3px] border-transparent hover:border-cyan-500 bg-cyan-50 transition-all duration-300 shadow-sm hover:shadow-md mb-2 flex items-center justify-center">
                        <span class="text-2xl md:text-3xl transform group-hover:scale-110 transition-transform">🍼</span>
                    </div>
                    <span class="text-[11px] md:text-[13px] font-bold text-gray-600 group-hover:text-cyan-600 flex items-center gap-1">
                        Baby <i class="fas fa-chevron-down text-[8px]"></i>
                    </span>
                </div>

                <!-- 6. Sunglasses 🕶️ -->
                <div class="flex flex-col items-center min-w-[65px] md:min-w-[85px] group cursor-pointer flex-shrink-0" onclick="toggleCategoryMenu(event, 'dropdown-sunglasses', this)">
                    <div class="w-14 h-14 md:w-[70px] md:h-[70px] rounded-full border-[3px] border-transparent hover:border-orange-500 bg-orange-50 transition-all duration-300 shadow-sm hover:shadow-md mb-2 flex items-center justify-center">
                        <span class="text-2xl md:text-3xl transform group-hover:scale-110 transition-transform">🕶️</span>
                    </div>
                    <span class="text-[11px] md:text-[13px] font-bold text-gray-600 group-hover:text-orange-600 flex items-center gap-1">
                        Eyewear <i class="fas fa-chevron-down text-[8px]"></i>
                    </span>
                </div>

                <!-- 7. Watches ⌚ -->
                <div class="flex flex-col items-center min-w-[65px] md:min-w-[85px] group cursor-pointer flex-shrink-0" onclick="toggleCategoryMenu(event, 'dropdown-watches', this)">
                    <div class="w-14 h-14 md:w-[70px] md:h-[70px] rounded-full border-[3px] border-transparent hover:border-slate-600 bg-slate-100 transition-all duration-300 shadow-sm hover:shadow-md mb-2 flex items-center justify-center">
                        <span class="text-2xl md:text-3xl transform group-hover:scale-110 transition-transform">⌚</span>
                    </div>
                    <span class="text-[11px] md:text-[13px] font-bold text-gray-600 group-hover:text-slate-700 flex items-center gap-1">
                        Watches <i class="fas fa-chevron-down text-[8px]"></i>
                    </span>
                </div>

                <!-- 8. Home Decor 🌼 -->
                <div class="flex flex-col items-center min-w-[65px] md:min-w-[85px] group cursor-pointer flex-shrink-0" onclick="toggleCategoryMenu(event, 'dropdown-decor', this)">
                    <div class="w-14 h-14 md:w-[70px] md:h-[70px] rounded-full border-[3px] border-transparent hover:border-emerald-500 bg-emerald-50 transition-all duration-300 shadow-sm hover:shadow-md mb-2 flex items-center justify-center">
                        <span class="text-2xl md:text-3xl transform group-hover:scale-110 transition-transform">🌼</span>
                    </div>
                    <span class="text-[11px] md:text-[13px] font-bold text-gray-600 group-hover:text-emerald-600 flex items-center gap-1">
                        Decor <i class="fas fa-chevron-down text-[8px]"></i>
                    </span>
                </div>

            </div>
        </div>
    </div>

    <!-- 3. Product Display Section -->
    <section class="w-full px-3 lg:px-6 py-4 pb-24 md:pb-12 relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 lg:gap-5">
            
            <?php
            $sql = "SELECT * FROM products $where_clause ORDER BY id DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $p_id = $row['id'];
                    $discount = 0;
                    if($row['old_price'] > $row['price']) { 
                        $discount = round((($row['old_price'] - $row['price']) / $row['old_price']) * 100);
                    }
                    $sold = rand(10, 85); 
                    
                    $stock = $row['stock_status'];
                    $badge_color = ($stock == 'In Stock') ? "bg-green-100 text-green-700" : (($stock == 'Pre-order') ? "bg-orange-100 text-orange-600" : "bg-red-100 text-red-600");
                    
                    $is_wished = in_array($p_id, $wishlist_items);

                    // Dynamic Routing: Checks if color variants exist for this product
                    $check_v = $conn->query("SELECT COUNT(*) as cnt FROM product_color_variants WHERE product_id = $p_id");
                    $v_count = $check_v->fetch_assoc()['cnt'];
                    $product_target_page = ($v_count > 0) ? "product_variant.php?id=$p_id" : "product.php?id=$p_id";
            ?>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:border-[#0B3022] hover:shadow-lg transition-all duration-300 overflow-hidden group relative flex flex-col h-full cursor-pointer">
                
                <!-- 🚀 AJAX Wishlist Button (No Page Reload) -->
                <button type="button" onclick="toggleWishlist(<?php echo $p_id; ?>, this)" class="absolute top-2 right-2 z-20 w-8 h-8 <?php echo $is_wished ? 'bg-red-50 border-red-200' : 'bg-white/90 hover:bg-red-50'; ?> backdrop-blur rounded-full flex items-center justify-center shadow transition border border-gray-100 focus:outline-none">
                    <i class="<?php echo $is_wished ? 'fas text-red-500' : 'far text-gray-400 hover:text-red-500'; ?> fa-heart text-sm transition-colors"></i>
                </button>

                <!-- Smart Link to Product Page -->
                <a href="<?php echo $product_target_page; ?>" class="flex flex-col flex-grow">
                    <div class="relative overflow-hidden aspect-square bg-[#f9fafb] flex items-center justify-center p-2">
                        <img src="<?php echo $row['image']; ?>" onerror="this.src='https://placehold.co/400x400/f0f0f0/cccccc?text=No+Image'" alt="<?php echo $row['name']; ?>" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">
                        <?php if($discount > 0) { ?>
                            <div class="absolute top-2 left-2 bg-gradient-to-r from-red-500 to-pink-500 text-white text-[10px] md:text-xs font-extrabold px-2 py-0.5 rounded shadow-sm z-10">
                                -<?php echo $discount; ?>%
                            </div>
                        <?php } ?>
                    </div>
                    
                    <div class="p-3 md:p-4 flex flex-col flex-grow border-t border-gray-50">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider"><?php echo $row['material']; ?></span>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full shadow-sm <?php echo $badge_color; ?>"><?php echo $stock; ?></span>
                        </div>

                        <h3 class="text-sm lg:text-base font-bold text-gray-800 line-clamp-2 leading-snug mb-2 group-hover:text-[#0B3022] transition-colors">
                            <?php echo $row['name']; ?>
                        </h3>
                        
                        <div class="flex items-center gap-1 mb-2 mt-auto text-yellow-400 text-[10px]">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <span class="text-gray-400 ml-1 font-semibold">(<?php echo $sold; ?>)</span>
                        </div>
                        
                        <div class="text-base lg:text-lg font-extrabold text-[#0B3022]">৳ <?php echo number_format($row['price']); ?></div>
                    </div>
                </a>
            </div>

            <?php
                } 
            } else {
                echo "<div class='col-span-full text-center py-16 bg-gray-50 rounded-xl border border-dashed border-gray-300'>
                        <i class='fas fa-box-open text-4xl text-gray-300 mb-3'></i>
                        <h3 class='text-lg font-bold text-gray-700'>No Products Found</h3>
                        <p class='text-sm text-gray-500'>Try selecting another category.</p>
                      </div>";
            }
            ?>

        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-[#0B3022] pt-16 pb-24 md:pb-8 border-t-[4px] border-[#facc15] text-gray-300 relative overflow-hidden mt-auto z-10">
        <i class="fas fa-crown absolute -right-10 -bottom-10 text-9xl text-white opacity-[0.03]"></i>
        <i class="fas fa-gem absolute -left-10 top-10 text-9xl text-white opacity-[0.03]"></i>

        <div class="w-full px-4 lg:px-8 relative z-10 max-w-none">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12 xl:px-12">
                
                <!-- Column 1: Brand Info & Social -->
                <div>
                    <a href="index.php" class="flex items-center gap-2 mb-4">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center p-1 border-2 border-[#facc15] shadow-md">
                            <img src="images/logo.png" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" alt="Logo" class="w-full h-full object-contain rounded-full">
                            <i class="fas fa-gem text-[#0B3022] hidden"></i>
                        </div>
                        <span class="text-2xl font-bold text-white tracking-wide">StellarLuxury<span class="text-[10px] text-[#facc15] uppercase ml-1">BD</span></span>
                    </a>
                    <p class="text-sm text-gray-400 mb-6 leading-relaxed pr-4">
                        Discover the epitome of elegance. We provide the most premium, exclusive, and authentic luxury products in Bangladesh.
                    </p>
                    <div class="flex gap-3">
                        <a href="https://facebook.com/stellarluxuryBD" target="_blank" class="w-9 h-9 rounded-full bg-[#072117] flex items-center justify-center text-white hover:bg-[#facc15] hover:text-[#0B3022] transition shadow-sm border border-gray-700 hover:border-transparent"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://instagram.com/stellarluxuryBD" target="_blank" class="w-9 h-9 rounded-full bg-[#072117] flex items-center justify-center text-white hover:bg-[#facc15] hover:text-[#0B3022] transition shadow-sm border border-gray-700 hover:border-transparent"><i class="fab fa-instagram"></i></a>
                        <a href="https://tiktok.com/@stellarluxuryBD" target="_blank" class="w-9 h-9 rounded-full bg-[#072117] flex items-center justify-center text-white hover:bg-[#facc15] hover:text-[#0B3022] transition shadow-sm border border-gray-700 hover:border-transparent"><i class="fab fa-tiktok"></i></a>
                        <a href="https://youtube.com/stellarluxuryBD" target="_blank" class="w-9 h-9 rounded-full bg-[#072117] flex items-center justify-center text-white hover:bg-[#facc15] hover:text-[#0B3022] transition shadow-sm border border-gray-700 hover:border-transparent"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Column 2: Information -->
                <div>
                    <h3 class="text-lg font-extrabold text-white mb-5 uppercase tracking-wider relative inline-block after:content-[''] after:absolute after:-bottom-1.5 after:left-0 after:w-1/2 after:h-0.5 after:bg-[#facc15]">Information</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="about.php" class="text-gray-400 hover:text-[#facc15] transition flex items-center gap-2"><i class="fas fa-angle-right text-[#facc15] text-[10px]"></i> About Us</a></li>
                        <li><a href="contact.php" class="text-gray-400 hover:text-[#facc15] transition flex items-center gap-2"><i class="fas fa-angle-right text-[#facc15] text-[10px]"></i> Contact Us</a></li>
                        <li><a href="faq.php" class="text-gray-400 hover:text-[#facc15] transition flex items-center gap-2"><i class="fas fa-angle-right text-[#facc15] text-[10px]"></i> FAQ</a></li>
                        <li><a href="privacy.php" class="text-gray-400 hover:text-[#facc15] transition flex items-center gap-2"><i class="fas fa-angle-right text-[#facc15] text-[10px]"></i> Privacy Policy</a></li>
                        <li><a href="refund.php" class="text-gray-400 hover:text-[#facc15] transition flex items-center gap-2"><i class="fas fa-angle-right text-[#facc15] text-[10px]"></i> Refund Policy</a></li>
                    </ul>
                </div>

                <!-- Column 3: Apps -->
                <div>
                    <h3 class="text-lg font-extrabold text-white mb-5 uppercase tracking-wider relative inline-block after:content-[''] after:absolute after:-bottom-1.5 after:left-0 after:w-1/2 after:h-0.5 after:bg-[#facc15]">Mobile Apps</h3>
                    <div class="relative inline-block mb-2">
                        <span class="absolute -top-3 -right-3 bg-red-600 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-lg z-10 animate-pulse border border-red-400">COMING SOON</span>
                        <div class="flex flex-col gap-3 opacity-50 grayscale hover:grayscale-0 hover:opacity-80 transition cursor-not-allowed">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="App Store" class="h-10 w-auto">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Play Store" class="h-10 w-auto">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-3 font-medium pr-4">Our official mobile apps are launching very soon!</p>
                </div>

                <!-- Column 4: Contact -->
                <div>
                    <h3 class="text-lg font-extrabold text-white mb-5 uppercase tracking-wider relative inline-block after:content-[''] after:absolute after:-bottom-1.5 after:left-0 after:w-1/2 after:h-0.5 after:bg-[#facc15]">Contact Us</h3>
                    <ul class="space-y-4 text-sm">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded bg-[#072117] flex items-center justify-center flex-shrink-0 text-[#facc15] shadow-sm"><i class="fas fa-map-marker-alt"></i></div>
                            <span class="text-gray-400 mt-1 leading-relaxed">Level 4, Elite Tower, Banani<br>Dhaka-1213, Bangladesh</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-[#072117] flex items-center justify-center flex-shrink-0 text-[#facc15] shadow-sm"><i class="fas fa-envelope"></i></div>
                            <a href="mailto:support@stellarluxury.bd" class="text-gray-400 hover:text-[#facc15] transition">support@stellarluxury.bd</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-[#072117] flex items-center justify-center flex-shrink-0 text-[#facc15] shadow-sm"><i class="fas fa-phone-alt"></i></div>
                            <a href="tel:+8801711000000" class="text-gray-400 hover:text-[#facc15] transition font-medium tracking-wider">+880 1711-000000</a>
                        </li>
                        <li class="flex items-center gap-3 mt-2">
                            <div class="w-8 h-8 rounded bg-green-500 flex items-center justify-center flex-shrink-0 text-white text-lg shadow-[0_0_10px_rgba(34,197,94,0.4)]"><i class="fab fa-whatsapp"></i></div>
                            <a href="https://wa.me/8801304464043" target="_blank" class="text-gray-300 font-bold hover:text-green-400 transition">Chat on WhatsApp</a>
                        </li>
                    </ul>
                </div>
                
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="border-t border-[#072117] mt-12 pt-6 pb-2 text-center flex flex-col md:flex-row justify-between items-center gap-4 xl:px-12">
                <p class="text-xs font-medium text-gray-500">&copy; <?php echo date('Y'); ?> <span class="text-gray-400 font-bold">StellarLuxury BD</span>. All rights reserved.</p>
                <div class="flex items-center gap-4 text-xl text-gray-500 opacity-60">
                    <i class="fab fa-cc-visa hover:text-white transition cursor-pointer"></i>
                    <i class="fab fa-cc-mastercard hover:text-white transition cursor-pointer"></i>
                    <i class="fas fa-shield-alt hover:text-[#facc15] transition cursor-pointer"></i>
                </div>
            </div>
        </div>
    </footer>

    <!-- 🚀 FLOATING DROPDOWN MENUS -->
    <div id="dropdowns-container">
        <!-- Dropdown for Jewelry -->
        <div id="dropdown-jewelry" class="category-dropdown fixed mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-[0_15px_40px_rgba(0,0,0,0.15)] hidden z-[9999] overflow-hidden text-left">
            <a href="index.php?category=Ring" class="block px-4 py-3 text-gray-700 hover:bg-[#0B3022]/10 hover:text-[#0B3022] border-b text-sm font-bold transition">Ring</a>
            <a href="index.php?category=Necklace" class="block px-4 py-3 text-gray-700 hover:bg-[#0B3022]/10 hover:text-[#0B3022] border-b text-sm font-bold transition">Necklace</a>
            <a href="index.php?category=Bracelet" class="block px-4 py-3 text-gray-700 hover:bg-[#0B3022]/10 hover:text-[#0B3022] border-b text-sm font-bold transition">Bracelet</a>
            <a href="index.php?category=Anklet" class="block px-4 py-3 text-gray-700 hover:bg-[#0B3022]/10 hover:text-[#0B3022] border-b text-sm font-bold transition">Anklet</a>
            <a href="index.php?category=Earring" class="block px-4 py-3 text-gray-700 hover:bg-[#0B3022]/10 hover:text-[#0B3022] text-sm font-bold transition">Earring</a>
        </div>

        <!-- Dropdown for Bags -->
        <div id="dropdown-bags" class="category-dropdown fixed mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-[0_15px_40px_rgba(0,0,0,0.15)] hidden z-[9999] overflow-hidden text-left">
            <a href="index.php?category=Handbag" class="block px-4 py-3 text-gray-700 hover:bg-pink-50 hover:text-pink-600 border-b text-sm font-bold transition">Handbags</a>
            <a href="index.php?category=Backpack" class="block px-4 py-3 text-gray-700 hover:bg-pink-50 hover:text-pink-600 border-b text-sm font-bold transition">Backpacks</a>
            <a href="index.php?category=Tote" class="block px-4 py-3 text-gray-700 hover:bg-pink-50 hover:text-pink-600 border-b text-sm font-bold transition">Tote Bags</a>
            <a href="index.php?category=Wallet" class="block px-4 py-3 text-gray-700 hover:bg-pink-50 hover:text-pink-600 text-sm font-bold transition">Wallets</a>
        </div>

        <!-- Dropdown for Shoes -->
        <div id="dropdown-shoes" class="category-dropdown fixed mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-[0_15px_40px_rgba(0,0,0,0.15)] hidden z-[9999] overflow-hidden text-left">
            <a href="index.php?category=Sneakers" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b text-sm font-bold transition">Sneakers</a>
            <a href="index.php?category=Heels" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b text-sm font-bold transition">Heels</a>
            <a href="index.php?category=Flats" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b text-sm font-bold transition">Flats</a>
            <a href="index.php?category=Sandals" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 text-sm font-bold transition">Sandals</a>
        </div>

        <!-- Dropdown for Beauty -->
        <div id="dropdown-beauty" class="category-dropdown fixed mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-[0_15px_40px_rgba(0,0,0,0.15)] hidden z-[9999] overflow-hidden text-left">
            <a href="index.php?category=Makeup" class="block px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 border-b text-sm font-bold transition">Makeup</a>
            <a href="index.php?category=Skincare" class="block px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 border-b text-sm font-bold transition">Skincare</a>
            <a href="index.php?category=Fragrance" class="block px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 border-b text-sm font-bold transition">Fragrance</a>
            <a href="index.php?category=Haircare" class="block px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 text-sm font-bold transition">Haircare</a>
        </div>

        <!-- Dropdown for Baby -->
        <div id="dropdown-baby" class="category-dropdown fixed mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-[0_15px_40px_rgba(0,0,0,0.15)] hidden z-[9999] overflow-hidden text-left">
            <a href="index.php?category=Baby Clothes" class="block px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 border-b text-sm font-bold transition">Baby Clothes</a>
            <a href="index.php?category=Toys" class="block px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 border-b text-sm font-bold transition">Toys</a>
            <a href="index.php?category=Diapering" class="block px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 border-b text-sm font-bold transition">Diapering</a>
            <a href="index.php?category=Feeding" class="block px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 text-sm font-bold transition">Feeding</a>
        </div>

        <!-- Dropdown for Sunglasses -->
        <div id="dropdown-sunglasses" class="category-dropdown fixed mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-[0_15px_40px_rgba(0,0,0,0.15)] hidden z-[9999] overflow-hidden text-left">
            <a href="index.php?category=Aviator" class="block px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 border-b text-sm font-bold transition">Aviator</a>
            <a href="index.php?category=Wayfarer" class="block px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 border-b text-sm font-bold transition">Wayfarer</a>
            <a href="index.php?category=Cat Eye" class="block px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 border-b text-sm font-bold transition">Cat Eye</a>
            <a href="index.php?category=Round Glasses" class="block px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 text-sm font-bold transition">Round Glasses</a>
        </div>

        <!-- Dropdown for Watches -->
        <div id="dropdown-watches" class="category-dropdown fixed mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-[0_15px_40px_rgba(0,0,0,0.15)] hidden z-[9999] overflow-hidden text-left">
            <a href="index.php?category=Analog" class="block px-4 py-3 text-gray-700 hover:bg-slate-50 hover:text-slate-700 border-b text-sm font-bold transition">Analog Watches</a>
            <a href="index.php?category=Digital" class="block px-4 py-3 text-gray-700 hover:bg-slate-50 hover:text-slate-700 border-b text-sm font-bold transition">Digital Watches</a>
            <a href="index.php?category=Smartwatch" class="block px-4 py-3 text-gray-700 hover:bg-slate-50 hover:text-slate-700 border-b text-sm font-bold transition">Smartwatches</a>
            <a href="index.php?category=Luxury" class="block px-4 py-3 text-gray-700 hover:bg-slate-50 hover:text-slate-700 text-sm font-bold transition">Luxury Watches</a>
        </div>

        <!-- Dropdown for Decor -->
        <div id="dropdown-decor" class="category-dropdown fixed mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-[0_15px_40px_rgba(0,0,0,0.15)] hidden z-[9999] overflow-hidden text-left">
            <a href="index.php?category=Lighting" class="block px-4 py-3 text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 border-b text-sm font-bold transition">Lighting</a>
            <a href="index.php?category=Vases" class="block px-4 py-3 text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 border-b text-sm font-bold transition">Vases & Pots</a>
            <a href="index.php?category=Wall Art" class="block px-4 py-3 text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 border-b text-sm font-bold transition">Wall Art</a>
            <a href="index.php?category=Cushions" class="block px-4 py-3 text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 text-sm font-bold transition">Cushions</a>
        </div>
    </div>

    <!-- JavaScript For Controls & AJAX Wishlist -->
    <script>
        let currentSlide = 0;
        const slides = document.getElementById('slider');
        const totalSlides = slides.children.length;
        
        function updateSlider() { 
            slides.style.transform = `translateX(-${currentSlide * 100}%)`; 
            
            const dot0 = document.getElementById('dot0');
            dot0.classList.replace(currentSlide === 0 ? 'opacity-50' : 'opacity-100', currentSlide === 0 ? 'opacity-100' : 'opacity-50');
            if(window.innerWidth >= 1024) { dot0.style.width = currentSlide === 0 ? '32px' : '12px'; } else { dot0.style.width = currentSlide === 0 ? '16px' : '8px'; }

            const dot1 = document.getElementById('dot1');
            dot1.classList.replace(currentSlide === 1 ? 'opacity-50' : 'opacity-100', currentSlide === 1 ? 'opacity-100' : 'opacity-50');
            if(window.innerWidth >= 1024) { dot1.style.width = currentSlide === 1 ? '32px' : '12px'; } else { dot1.style.width = currentSlide === 1 ? '16px' : '8px'; }
        }
        
        function nextSlide() { currentSlide = (currentSlide + 1) % totalSlides; updateSlider(); }
        function prevSlide() { currentSlide = (currentSlide - 1 + totalSlides) % totalSlides; updateSlider(); }
        let slideInterval = setInterval(nextSlide, 4000); 

        const bannerContainer = document.getElementById('banner-container');
        let touchStartX = 0; let touchEndX = 0;

        bannerContainer.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; clearInterval(slideInterval); });
        bannerContainer.addEventListener('touchend', e => { touchEndX = e.changedTouches[0].screenX; handleSwipe(); slideInterval = setInterval(nextSlide, 4000); });

        function handleSwipe() {
            const swipeThreshold = 50; 
            if (touchStartX - touchEndX > swipeThreshold) nextSlide(); 
            if (touchEndX - touchStartX > swipeThreshold) prevSlide(); 
        }

        // 🚀 AJAX WISHLIST TOGGLE FUNCTION (No Page Reload)
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
                        element.classList.remove('bg-white/90');
                        element.classList.add('bg-red-50', 'border-red-200');
                        icon.classList.remove('far', 'text-gray-400', 'hover:text-red-500');
                        icon.classList.add('fas', 'text-red-500');
                    } else {
                        element.classList.remove('bg-red-50', 'border-red-200');
                        element.classList.add('bg-white/90');
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far', 'text-gray-400', 'hover:text-red-500');
                    }

                    // 🚀 হেডার বা নেভবারে থাকা উইশলিস্ট কাউন্ট ব্যাজ লাইভ আপডেট করার কোড
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

        // 🚀 ADVANCED FLOATING DROPDOWN LOGIC
        let activeDropdown = null;

        function toggleCategoryMenu(event, id, element) {
            event.stopPropagation();
            
            const dropdown = document.getElementById(id);
            const isHidden = dropdown.classList.contains('hidden');
            
            hideAllCategoryDropdowns();
            
            if (isHidden) {
                const rect = element.getBoundingClientRect();
                dropdown.style.top = (rect.bottom + 4) + 'px';
                
                const dropdownWidth = 192; 
                let leftPos = rect.left + (rect.width / 2) - (dropdownWidth / 2);
                
                if (leftPos < 10) leftPos = 10;
                if (leftPos + dropdownWidth > window.innerWidth - 10) leftPos = window.innerWidth - dropdownWidth - 10;
                
                dropdown.style.left = leftPos + 'px';
                dropdown.classList.remove('hidden');
                activeDropdown = dropdown;
            }
        }

        function hideAllCategoryDropdowns() {
            const allDropdowns = document.querySelectorAll('.category-dropdown');
            allDropdowns.forEach(menu => {
                menu.classList.add('hidden');
            });
            activeDropdown = null;
        }

        window.addEventListener('click', hideAllCategoryDropdowns);
        
        window.addEventListener('scroll', function() {
            if(activeDropdown) hideAllCategoryDropdowns();
        }, true);
        
        window.addEventListener('resize', updateSlider);
    </script>
</body>
</html>