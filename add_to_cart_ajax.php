<?php
include 'db.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if ($user_id == 0) {
    echo json_encode(['status' => 'not_logged_in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $variant_quantities = isset($_POST['variant_qty']) ? $_POST['variant_qty'] : [];

    $total_added = 0;

    // যদি ভ্যারিয়েশন প্রোডাক্ট হয় (product_variant.php থেকে আসলে)
    if (!empty($variant_quantities) && is_array($variant_quantities)) {
        foreach ($variant_quantities as $var_id => $qty) {
            $qty = (int)$qty;
            if ($qty > 0) {
                $total_added += $qty;
                $cart_check = $conn->query("SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id AND variant_id = $var_id");
                if ($cart_check->num_rows == 0) {
                    $conn->query("INSERT INTO cart (user_id, product_id, variant_id, quantity) VALUES ($user_id, $product_id, $var_id, $qty)");
                } else {
                    $conn->query("UPDATE cart SET quantity = quantity + $qty WHERE user_id = $user_id AND product_id = $product_id AND variant_id = $var_id");
                }
            }
        }
    } 
    // যদি নরমাল প্রোডাক্ট হয় (product.php থেকে আসলে)
    else if ($product_id > 0 && $quantity > 0) {
        $total_added += $quantity;
        $cart_check = $conn->query("SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id AND (variant_id = 0 OR variant_id IS NULL)");
        if ($cart_check->num_rows == 0) {
            $conn->query("INSERT INTO cart (user_id, product_id, variant_id, quantity) VALUES ($user_id, $product_id, 0, $quantity)");
        } else {
            $conn->query("UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND product_id = $product_id AND (variant_id = 0 OR variant_id IS NULL)");
        }
    }

    if ($total_added > 0) {
        // বর্তমান ইউজারের মোট কার্ট কাউন্ট বের করা
        $count_res = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
        $count_row = $count_res->fetch_assoc();
        $cart_count = $count_row['total'] ? (int)$count_row['total'] : 0;

        echo json_encode(['status' => 'success', 'cart_count' => $cart_count]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);