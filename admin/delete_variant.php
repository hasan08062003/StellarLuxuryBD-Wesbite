<?php
include '../db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

if(isset($_GET['id']) && isset($_GET['product_id'])) {
    $v_id = (int)$_GET['id'];
    $p_id = (int)$_GET['product_id'];

    $conn->query("DELETE FROM product_color_variants WHERE id = $v_id");
    $_SESSION['success'] = "Color variant removed successfully!";
    header("Location: add_variant_product.php?edit=" . $p_id);
    exit;
}
header("Location: products.php");
exit;