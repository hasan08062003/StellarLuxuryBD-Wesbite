<?php
include 'db.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if ($user_id == 0) {
    echo json_encode(['status' => 'not_logged_in']);
    exit;
}

if (isset($_POST['product_id'])) {
    $prod_id = (int)$_POST['product_id'];

    $check = $conn->query("SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $prod_id");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $prod_id)");
        $action = 'added';
    } else {
        $conn->query("DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $prod_id");
        $action = 'removed';
    }

    // বর্তমান ইউজারের মোট উইশলিস্ট কাউন্ট বের করা
    $count_res = $conn->query("SELECT COUNT(*) as total FROM wishlist WHERE user_id = $user_id");
    $count_row = $count_res->fetch_assoc();
    $wishlist_count = $count_row['total'];

    echo json_encode(['status' => 'success', 'action' => $action, 'count' => $wishlist_count]);
    exit;
}

echo json_encode(['status' => 'error']);