<?php 
include 'db.php'; 
include 'header.php'; 
?>

<main class="container mx-auto px-4 py-6 md:py-12 flex-grow min-h-[70vh] pb-24 md:pb-12 bg-gray-50">
    
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-5 mb-6 bg-white p-4 md:p-5 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden">
        
        <div class="absolute top-0 right-0 w-40 h-40 bg-green-50 rounded-bl-full -z-10 opacity-60 hidden md:block"></div>
        
        <div class="flex items-center gap-4 z-10">
            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-blue-100 border-2 border-blue-500 overflow-hidden shadow-sm">
                <img src="https://ui-avatars.com/api/?name=User+Name&background=0D8ABC&color=fff&size=128" alt="User" class="w-full h-full object-cover">
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">User Name</h2>
                <p class="text-sm text-gray-500 mt-0.5">Member since 2026</p>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-50 to-green-100/40 border border-green-200/60 rounded-xl p-3 flex items-center justify-between gap-4 w-full md:w-auto z-10 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 text-white rounded-full flex items-center justify-center font-extrabold text-xl shadow-[0_2px_5px_rgba(34,197,94,0.4)] border-2 border-white">
                    S
                </div>
                <div>
                    <p class="text-[10px] md:text-xs text-green-700 font-bold uppercase tracking-wide mb-0.5">S-Coin Balance</p>
                    <p class="text-lg md:text-xl font-extrabold text-gray-800 leading-none">1,250 <span class="text-xs font-semibold text-gray-500">Pts</span></p>
                </div>
            </div>
            
            <div class="flex flex-col gap-1.5 ml-2 border-l border-green-200/80 pl-4">
                <button class="bg-green-600 hover:bg-green-700 text-white text-[10px] md:text-xs font-bold px-4 py-1.5 rounded-md transition shadow-sm w-full text-center flex items-center justify-center gap-1">
                    <i class="fas fa-plus text-[9px]"></i> Deposit
                </button>
                <button class="bg-white border border-green-600 text-green-600 hover:bg-green-50 text-[10px] md:text-xs font-bold px-4 py-1.5 rounded-md transition shadow-sm w-full text-center flex items-center justify-center gap-1">
                    <i class="fas fa-arrow-down text-[9px]"></i> Withdraw
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-50">
            <h3 class="font-bold text-gray-800 text-lg">My Orders</h3>
            <a href="orders.php" class="text-sm text-gray-500 flex items-center hover:text-blue-600 transition">View All <i class="fas fa-chevron-right text-xs ml-1"></i></a>
        </div>
        
        <div class="flex justify-between items-start text-center pt-2">
            <a href="#" class="flex flex-col items-center relative text-gray-600 hover:text-blue-600 transition w-1/5">
                <div class="relative">
                    <i class="fas fa-wallet text-2xl mb-1.5 text-gray-400"></i>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center border border-white">1</span>
                </div>
                <span class="text-[11px] font-semibold mt-1 leading-tight">To Pay</span>
            </a>
            <a href="#" class="flex flex-col items-center relative text-gray-600 hover:text-blue-600 transition w-1/5">
                <i class="fas fa-box text-2xl mb-1.5 text-gray-400"></i>
                <span class="text-[11px] font-semibold mt-1 leading-tight">To Ship</span>
            </a>
            <a href="#" class="flex flex-col items-center relative text-gray-600 hover:text-blue-600 transition w-1/5">
                <div class="relative">
                    <i class="fas fa-truck text-2xl mb-1.5 text-gray-400"></i>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center border border-white">2</span>
                </div>
                <span class="text-[11px] font-semibold mt-1 leading-tight">To Receive</span>
            </a>
            <a href="#" class="flex flex-col items-center relative text-gray-600 hover:text-blue-600 transition w-1/5">
                <i class="fas fa-star text-2xl mb-1.5 text-gray-400"></i>
                <span class="text-[11px] font-semibold mt-1 leading-tight">To Review</span>
            </a>
            <a href="#" class="flex flex-col items-center relative text-gray-600 hover:text-blue-600 transition w-1/5">
                <i class="fas fa-undo-alt text-2xl mb-1.5 text-gray-400"></i>
                <span class="text-[11px] font-semibold mt-1 leading-tight">Refund</span>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="font-bold text-gray-800 text-lg mb-4">Recent Orders Status</h3>
        
        <div class="space-y-4">
            
            <div class="border border-gray-100 rounded-lg p-3 relative hover:border-blue-200 transition">
                <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-50">
                    <span class="text-xs text-gray-500 font-medium"><i class="fas fa-store mr-1"></i> ShopName Official</span>
                    <span class="text-[11px] font-bold text-orange-500 bg-orange-50 px-2.5 py-1 rounded-md">Pending</span>
                </div>
                <div class="flex gap-3">
                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=150&q=80" alt="Product" class="w-20 h-20 object-cover rounded-md border border-gray-100 flex-shrink-0">
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="text-sm md:text-base font-bold text-gray-800 line-clamp-2 leading-tight">Smart Watch Pro Series 8 with Heart Rate Monitor</h4>
                            <p class="text-xs text-gray-400 mt-1">Color: Black</p>
                        </div>
                        <div class="flex justify-between items-end mt-2">
                            <span class="text-xs font-semibold text-gray-500">x 2 pcs</span>
                            <span class="text-sm md:text-base font-bold text-gray-800">৳ ৫,০০০</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-50 flex justify-end gap-2">
                    <button class="px-4 py-1.5 border border-gray-300 text-gray-600 text-xs font-bold rounded-full hover:bg-gray-50 transition">Cancel Order</button>
                    <button class="px-4 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-full hover:bg-blue-700 transition">Pay Now</button>
                </div>
            </div>

            <div class="border border-gray-100 rounded-lg p-3 relative hover:border-blue-200 transition">
                <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-50">
                    <span class="text-xs text-gray-500 font-medium"><i class="fas fa-store mr-1"></i> ShopName Official</span>
                    <span class="text-[11px] font-bold text-green-500 bg-green-50 px-2.5 py-1 rounded-md">Delivered</span>
                </div>
                <div class="flex gap-3">
                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=150&q=80" alt="Product" class="w-20 h-20 object-cover rounded-md border border-gray-100 flex-shrink-0">
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="text-sm md:text-base font-bold text-gray-800 line-clamp-2 leading-tight">Nike Sport Running Shoes Lightweight</h4>
                            <p class="text-xs text-gray-400 mt-1">Size: 42</p>
                        </div>
                        <div class="flex justify-between items-end mt-2">
                            <span class="text-xs font-semibold text-gray-500">x 1 pcs</span>
                            <span class="text-sm md:text-base font-bold text-gray-800">৳ ১,৮০০</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-50 flex justify-end gap-2">
                    <button class="px-4 py-1.5 border border-blue-600 text-blue-600 text-xs font-bold rounded-full hover:bg-blue-50 transition">Buy Again</button>
                    <button class="px-4 py-1.5 bg-orange-500 text-white text-xs font-bold rounded-full hover:bg-orange-600 transition">Review</button>
                </div>
            </div>

        </div>
    </div>
</main>

</body>
</html>