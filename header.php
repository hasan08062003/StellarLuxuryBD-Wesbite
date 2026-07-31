<?php
// ডাইনামিক উইশলিস্ট, কার্ট এবং পয়েন্ট কাউন্টের জন্য লজিক
$wishlist_count = 0;
$cart_count = 0;
$header_user_points = 0;
$header_user_tier = "New Member";

// ইউজার লগিন থাকলে ডেটাবেস থেকে কাউন্ট বের করবে
if(isset($_SESSION['user_id'])) {
    $user_id_nav = (int)$_SESSION['user_id'];
    
    // Wishlist Count (নিশ্চিত করা হচ্ছে যেন শুধু বর্তমান ইউজারের ডেডাই গোনা হয়)
    $w_query = $conn->query("SELECT COUNT(*) AS total FROM wishlist WHERE user_id = $user_id_nav");
    if($w_query && $w_query->num_rows > 0) {
        $w_row = $w_query->fetch_assoc();
        $wishlist_count = (int)$w_row['total'];
    }
    
    // Cart Count (কার্টে মোট কয়টি আইটেম আছে)
    $c_query = $conn->query("SELECT SUM(quantity) AS total FROM cart WHERE user_id = $user_id_nav");
    if($c_query && $c_query->num_rows > 0) {
        $c_row = $c_query->fetch_assoc();
        $cart_count = $c_row['total'] ? (int)$c_row['total'] : 0;
    }

    // ইউজারের পয়েন্ট ও ব্যাজ আনা হচ্ছে
    $u_query = $conn->query("SELECT points, membership_tier FROM users WHERE id = $user_id_nav");
    if($u_query && $u_query->num_rows > 0) {
        $u_data = $u_query->fetch_assoc();
        $header_user_points = isset($u_data['points']) ? (int)$u_data['points'] : 0;
        $header_user_tier = !empty($u_data['membership_tier']) ? $u_data['membership_tier'] : "New Member";
    }
}

// 🚀 DYNAMIC OFFER TEXT (অ্যাডমিন প্যানেল থেকে আসবে)
$dynamic_offer_text = "🎉 Welcome to StellarLuxury BD! Pre-order your dream jewelry today! 🚀 | 💎 Experience the epitome of elegance with our exclusive collections! ✨";
$offer_query = $conn->query("SELECT setting_value FROM settings WHERE setting_key='offer_text'");
if ($offer_query && $offer_query->num_rows > 0) {
    $dynamic_offer_text = $offer_query->fetch_assoc()['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StellarLuxury BD</title>
    <!-- Tailwind CSS & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @keyframes marquee { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }
        .animate-marquee { display: inline-block; white-space: nowrap; animation: marquee 15s linear infinite; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <!-- Header Section -->
    <header id="main-header" class="bg-[#0B3022] shadow-md sticky top-0 z-50 border-b border-[#072117] transition-transform duration-300 ease-in-out">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            
            <!-- Top Header Logo (StellarLuxury BD) -->
            <div class="text-xl md:text-2xl font-bold text-white tracking-wide">
                <a href="index.php" class="flex items-center gap-2">
                    <div class="relative flex items-center justify-center">
                        <img src="images/logo.png" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';" alt="Logo" class="w-10 h-10 md:w-12 md:h-12 object-contain transform scale-125 origin-center">
                        <i class="fas fa-crown hidden text-2xl text-[#facc15]"></i>
                    </div>
                    <span class="flex items-start ml-2 md:ml-3">StellarLuxury<span class="text-[9px] md:text-[10px] font-bold text-[#facc15] mt-0.5 ml-1 uppercase tracking-widest">BD</span></span>
                </a>
            </div>

            <!-- ডেস্কটপ সার্চ বার -->
            <form action="search.php" method="GET" class="hidden md:flex flex-1 mx-12 items-center bg-white rounded-md overflow-hidden shadow-inner">
                <input type="text" name="query" placeholder="Search for premium jewelry..." class="w-full px-4 py-2.5 text-gray-800 bg-transparent focus:outline-none text-sm font-medium" required>
                <button type="submit" class="px-6 bg-gray-100 text-[#0B3022] hover:bg-gray-200 h-full py-2.5 transition border-l border-gray-300 font-bold">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <!-- ডেস্কটপ আইকন -->
            <div class="flex items-center space-x-5 md:space-x-6 relative">
                
                <!-- DYNAMIC WISHLIST COUNT -->
                <a href="wishlist.php" class="text-white hover:text-[#facc15] relative transition">
                    <i class="far fa-heart text-xl"></i>
                    <span class="wishlist-count-badge absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 shadow <?php echo ($wishlist_count > 0) ? '' : 'hidden'; ?>">
                        <?php echo $wishlist_count; ?>
                    </span>
                </a>
                
                <!-- DYNAMIC CART COUNT (Updated with cart-count-badge class) -->
                <a href="cart.php" class="text-white hover:text-[#facc15] relative transition">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span class="cart-count-badge absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 shadow <?php echo ($cart_count > 0) ? '' : 'hidden'; ?>">
                        <?php echo $cart_count; ?>
                    </span>
                </a>
                
                <!-- প্রোফাইল আইকন -->
                <div class="relative cursor-pointer" onclick="toggleProfileMenu(event)">
                    <div class="text-white hover:text-[#facc15] flex items-center gap-1 transition">
                        <i class="far fa-user text-xl"></i>
                    </div>
                    
                    <!-- ড্রপডাউন মেনু -->
                    <div id="desktopProfileMenu" class="absolute right-0 top-full mt-3 w-56 bg-white border border-gray-100 rounded-lg shadow-2xl hidden transition-all z-50 overflow-hidden">
                        
                        <?php if(isset($_SESSION['user_id'])): ?>
                            
                            <!-- ইউজার লগিন করা থাকলে -->
                            <div class="px-4 py-3 border-b border-gray-100 bg-[#0B3022]/5">
                                <p class="text-sm font-extrabold text-[#0B3022] uppercase tracking-wider"><?php echo $_SESSION['user_name']; ?></p>
                                <p class="text-[11px] text-gray-500 font-bold truncate mt-0.5 mb-1.5"><?php echo $_SESSION['user_email']; ?></p>
                                
                                <div class="flex items-center gap-1.5 mt-1">
                                    <p class="text-[10px] font-bold text-[#0B3022] bg-[#facc15] px-2 py-0.5 rounded shadow-sm border border-[#0B3022]/10 uppercase tracking-wide">
                                        <i class="fas fa-crown mr-0.5"></i> <?php echo htmlspecialchars($header_user_tier); ?>
                                    </p>
                                    <p class="text-[11px] font-bold text-[#facc15] bg-[#0B3022] px-2 py-0.5 rounded shadow-sm">
                                        <i class="fas fa-coins mr-0.5"></i> <?php echo number_format($header_user_points); ?>
                                    </p>
                                </div>
                            </div>
                            
                            <a href="overview.php" class="block px-4 py-3 text-gray-700 hover:bg-[#0B3022]/10 hover:text-[#0B3022] border-b text-sm transition font-bold"><i class="fas fa-chart-pie mr-2 text-[#0B3022]"></i> Overview</a>
                            <a href="profile.php" class="block px-4 py-3 text-gray-700 hover:bg-[#0B3022]/10 hover:text-[#0B3022] border-b text-sm transition font-bold"><i class="fas fa-user-circle mr-2 text-gray-400"></i> My Profile</a>
                            <a href="orders.php" class="block px-4 py-3 text-gray-700 hover:bg-[#0B3022]/10 hover:text-[#0B3022] border-b text-sm transition font-bold"><i class="fas fa-box-open mr-2 text-gray-400"></i> My Orders</a>
                            <a href="logout.php" class="block px-4 py-3 text-red-500 hover:bg-red-50 text-sm transition font-bold"><i class="fas fa-sign-out-alt mr-2 text-red-400"></i> Logout</a>
                        
                        <?php else: ?>
                            
                            <!-- ইউজার লগিন করা না থাকলে -->
                            <a href="login.php?action=login" class="block px-4 py-3 text-gray-700 hover:bg-[#0B3022]/10 hover:text-[#0B3022] border-b text-sm transition font-bold"><i class="fas fa-sign-in-alt mr-2 text-gray-400"></i> Login</a>
                            <a href="login.php?action=register" class="block px-4 py-3 text-gray-700 hover:bg-[#0B3022]/10 hover:text-[#0B3022] text-sm transition font-bold"><i class="fas fa-user-plus mr-2 text-gray-400"></i> Registration</a>
                        
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- মোবাইল সার্চ বার -->
        <div class="md:hidden px-4 pb-3 mt-1">
            <form action="search.php" method="GET" class="flex items-center bg-white rounded-md overflow-hidden shadow-inner">
                <input type="text" name="query" placeholder="Search jewelry..." class="w-full px-4 py-2 text-gray-800 bg-transparent focus:outline-none text-sm font-medium" required>
                <button type="submit" class="px-5 text-[#0B3022] bg-gray-100 h-full py-2 border-l border-gray-300"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </header>

    <!-- News Ticker -->
    <div class="bg-[#072117] text-gray-200 flex items-stretch relative overflow-hidden min-h-[36px] md:min-h-[44px]">
        <div class="bg-red-600 text-white px-2 md:px-4 py-1.5 md:py-2 text-[11px] md:text-sm font-bold z-10 flex items-center shadow-[2px_0_5px_rgba(0,0,0,0.3)] flex-shrink-0">
            <i class="fas fa-bell mr-1.5 animate-pulse"></i> <span class="hidden sm:inline mr-1">UPDATE</span> OFFER
        </div>
        <div class="flex-1 px-2 md:px-4 py-1.5 md:py-2 flex items-center whitespace-normal">
            <p class="text-[11px] md:text-sm leading-tight md:leading-normal">
                <span id="typewriter-text" class="font-medium text-white"></span><span class="animate-pulse font-black text-[#facc15] ml-[1px]">|</span>
            </p>
        </div>
    </div>

    <!-- Typewriter JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rawText = "<?php echo addslashes($dynamic_offer_text); ?>";
            const textArray = rawText.includes('|') ? rawText.split('|').map(item => item.trim()) : [rawText];
            
            let textIndex = 0;
            let charIndex = 0;
            let isDeleting = false;
            const typingSpeed = 50;   
            const erasingSpeed = 25;  
            const delayBetween = 3000; 
            
            const typeWriterElement = document.getElementById("typewriter-text");

            function type() {
                if(!typeWriterElement || textArray.length === 0 || textArray[0] === "") return;
                const currentText = textArray[textIndex];
                
                if (isDeleting) {
                    typeWriterElement.textContent = currentText.substring(0, charIndex - 1);
                    charIndex--;
                } else {
                    typeWriterElement.textContent = currentText.substring(0, charIndex + 1);
                    charIndex++;
                }

                let speed = isDeleting ? erasingSpeed : typingSpeed;

                if (!isDeleting && charIndex === currentText.length) {
                    speed = delayBetween;
                    isDeleting = true;
                } else if (isDeleting && charIndex === 0) {
                    isDeleting = false;
                    textIndex = (textIndex + 1) % textArray.length;
                    speed = 500;
                }

                setTimeout(type, speed);
            }
            
            setTimeout(type, 1000); 
        });
    </script>

    <!-- Mobile Bottom Navigation -->
    <div id="bottom-nav" class="md:hidden fixed bottom-0 left-0 w-full bg-white shadow-[0_-4px_15px_rgba(0,0,0,0.1)] z-50 flex justify-between items-center px-6 py-2 border-t border-gray-200 transition-transform duration-300 ease-in-out">
        
        <a href="index.php" class="flex flex-col items-center text-[#0B3022] transition mt-[-5px]">
            <div class="w-9 h-9 mb-0.5 rounded-full border-2 border-[#0B3022] overflow-hidden flex items-center justify-center bg-white shadow-md p-0.5 transform scale-110">
                <img src="images/logo.png" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" alt="Logo" class="w-full h-full object-contain rounded-full">
                <i class="fas fa-crown text-[#0B3022] text-[14px] hidden"></i>
            </div>
            <span class="text-[10px] font-extrabold mt-1">Home</span>
        </a>

        <!-- DIRECT WHATSAPP LINK -->
        <a href="https://wa.me/8801304464043" target="_blank" class="flex flex-col items-center text-gray-500 hover:text-green-500 relative transition mt-1 group">
            <i class="fab fa-whatsapp text-[20px] mb-1 group-hover:scale-110 transition-transform"></i>
            <span class="absolute -top-1 -right-1.5 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center shadow-sm animate-bounce">1</span>
            <span class="text-[10px] font-medium group-hover:font-bold">Message</span>
        </a>
        
        <!-- DYNAMIC CART COUNT (MOBILE) (Updated with cart-count-badge class) -->
        <a href="cart.php" class="flex flex-col items-center text-gray-500 hover:text-[#0B3022] relative transition mt-1">
            <i class="fas fa-shopping-cart text-[20px] mb-1"></i>
            <span class="cart-count-badge absolute -top-1 -right-1.5 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center shadow-sm <?php echo ($cart_count > 0) ? '' : 'hidden'; ?>">
                <?php echo $cart_count; ?>
            </span>
            <span class="text-[10px] font-medium">Cart</span>
        </a>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="profile.php" class="flex flex-col items-center text-gray-500 hover:text-[#0B3022] transition mt-1">
                <i class="fas fa-user-circle text-[20px] mb-1"></i>
                <span class="text-[10px] font-bold">Profile</span>
            </a>
        <?php else: ?>
            <a href="login.php" class="flex flex-col items-center text-gray-500 hover:text-[#0B3022] transition mt-1">
                <i class="far fa-user text-[20px] mb-1"></i>
                <span class="text-[10px] font-medium">Me</span>
            </a>
        <?php endif; ?>

    </div>

    <!-- Smart Auto-Hide Scroll Logic & Dropdown -->
    <script>
        function toggleProfileMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('desktopProfileMenu');
            menu.classList.toggle('hidden');
        }
        window.addEventListener('click', function(e) {
            const menu = document.getElementById('desktopProfileMenu');
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            let lastScrollTop = 0;
            const header = document.getElementById('main-header');
            const bottomNav = document.getElementById('bottom-nav');
            const scrollThreshold = 10;

            window.addEventListener('scroll', function() {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (scrollTop <= 10) {
                    if (header) header.style.transform = 'translateY(0)';
                    if (bottomNav) bottomNav.style.transform = 'translateY(0)';
                    lastScrollTop = scrollTop;
                    return;
                }

                if (Math.abs(scrollTop - lastScrollTop) <= scrollThreshold) return;

                if (scrollTop > lastScrollTop) {
                    if (header) header.style.transform = 'translateY(-100%)';
                    if (bottomNav) bottomNav.style.transform = 'translateY(0)';
                } else {
                    if (header) header.style.transform = 'translateY(0)';
                    if (bottomNav) bottomNav.style.transform = 'translateY(100%)';
                }
                
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; 
            }, false);
        });
    </script>