<?php
include '../db.php';
if (!isset($_SESSION['admin_id'])) { exit; }

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ord_q = $conn->query("SELECT orders.*, users.name, users.phone FROM orders JOIN users ON orders.user_id = users.id WHERE orders.id = $order_id");

if($ord_q->num_rows > 0) {
    $ord = $ord_q->fetch_assoc();
    
    // 👤 Customer Info Box
    echo '<div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-4 space-y-1">';
    echo '<p class="font-black text-gray-800 text-sm"><i class="fas fa-user text-[#0B3022] mr-2"></i>Customer: ' . htmlspecialchars($ord['name']) . ' (' . htmlspecialchars($ord['phone']) . ')</p>';
    echo '<p class="text-gray-600 font-medium"><i class="fas fa-map-marker-alt text-red-500 mr-2"></i>Address: ' . htmlspecialchars($ord['shipping_address']) . '</p>';
    echo '<p class="text-gray-600 font-medium"><i class="fas fa-wallet text-blue-500 mr-2"></i>Payment Method: <span class="uppercase font-bold text-gray-800">' . htmlspecialchars($ord['payment_method']) . '</span></p>';
    
    // 📸 Payment Screenshot (If exists)
    if(!empty($ord['payment_screenshot'])) {
        echo '<div class="mt-2"><span class="font-bold text-gray-700 block mb-1">Payment Screenshot:</span><a href="../' . $ord['payment_screenshot'] . '" target="_blank"><img src="../' . $ord['payment_screenshot'] . '" class="w-24 h-24 object-cover rounded-lg border shadow-xs hover:scale-105 transition"></a></div>';
    }
    echo '</div>';

    echo '<h4 class="font-extrabold text-[#0B3022] text-xs uppercase tracking-wider mb-2">Ordered Products</h4>';
    echo '<div class="space-y-3">';

    // 📦 Ordered Items List
    $items_q = $conn->query("SELECT order_items.*, products.name, products.image, products.category FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = $order_id");
    
    while($item = $items_q->fetch_assoc()) {
        
        // 🚀 SMART LINK LOGIC: Check if it's a variant product or normal product
        if(isset($item['variant_id']) && $item['variant_id'] > 0) {
            $product_link = "../product_variant.php?id=" . $item['product_id'];
        } else {
            $product_link = "../product.php?id=" . $item['product_id'];
        }

        echo '<div class="bg-white p-3 rounded-xl border border-gray-200 flex items-center justify-between gap-4 shadow-xs">';
        echo '<div class="flex items-center gap-3">';
        echo '<img src="../' . $item['image'] . '" class="w-14 h-14 object-cover rounded-lg border flex-shrink-0">';
        echo '<div>';
        
        // 🔗 Clickable Product Link
        echo '<a href="' . $product_link . '" target="_blank" title="Click to view product in website" class="font-extrabold text-gray-800 text-xs hover:text-blue-600 transition block underline">' . htmlspecialchars($item['name']) . ' <i class="fas fa-external-link-alt text-[9px] ml-0.5"></i></a>';
        
        echo '<span class="text-[10px] bg-gray-100 text-gray-600 font-bold px-2 py-0.5 rounded uppercase mt-0.5 inline-block">' . htmlspecialchars($item['category']) . '</span>';
        echo '<p class="text-[10px] text-gray-500 font-bold mt-1">Qty: ' . $item['quantity'] . ' x ৳' . number_format($item['price']) . '</p>';
        echo '</div>';
        echo '</div>';
        echo '<span class="font-black text-[#0B3022] text-sm flex-shrink-0">৳ ' . number_format($item['price'] * $item['quantity']) . '</span>';
        echo '</div>';
    }
    echo '</div>';
    
    // 💰 Grand Total
    echo '<div class="mt-4 pt-3 border-t border-gray-200 flex justify-between items-center text-sm font-black text-gray-800 bg-[#facc15]/20 p-3 rounded-xl border border-[#facc15]/50">';
    echo '<span>Grand Total (Incl. Shipping):</span>';
    echo '<span class="text-[#0B3022]">৳ ' . number_format($ord['total_amount']) . '</span>';
    echo '</div>';

} else {
    echo '<p class="text-red-500 font-bold text-center">Order not found!</p>';
}
?>