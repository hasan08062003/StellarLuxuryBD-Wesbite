<?php
include '../db.php'; 
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

// 🚀 File Upload Helper Function
function uploadFile($file_param, $prefix) {
    if(isset($_FILES[$file_param]) && $_FILES[$file_param]['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES[$file_param]['name'], PATHINFO_EXTENSION));
        $filename = $prefix . "_" . time() . "_" . rand(1000, 9999) . "." . $ext;
        $target_dir = "../images/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        if(move_uploaded_file($_FILES[$file_param]['tmp_name'], $target_dir . $filename)) {
            return "images/" . $filename;
        }
    }
    return "";
}

// 🚀 CHECK IF EDIT MODE
$edit_mode = false;
$product = [
    'name' => '', 'category' => '', 'material' => '', 'price' => '', 
    'old_price' => '', 'stock_status' => 'In Stock', 'description' => '', 
    'image' => '', 'image2' => '', 'image3' => '', 'image4' => '', 
    'image5' => '', 'image6' => '', 'image7' => '', 'image8' => '', 'video' => ''
];
$existing_variants = [];

if(isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM products WHERE id = $edit_id");
    if($res->num_rows > 0) {
        $product = $res->fetch_assoc();
        $edit_mode = true;

        // Fetch existing color variants
        $v_res = $conn->query("SELECT * FROM product_color_variants WHERE product_id = $edit_id");
        while($v = $v_res->fetch_assoc()) {
            $existing_variants[] = $v;
        }
    }
}

// 🚀 Save or Update Logic
if(isset($_POST['save_variant_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $material = $conn->real_escape_string($_POST['material']);
    $price = (float)$_POST['price'];
    $old_price = (float)$_POST['old_price'];
    $stock_status = $conn->real_escape_string($_POST['stock_status']);
    $description = $conn->real_escape_string($_POST['description']);
    
    if($edit_mode) {
        // --- UPDATE MODE ---
        $product_id = (int)$_POST['product_id'];
        
        $update_sql = "UPDATE products SET name='$name', category='$category', material='$material', price='$price', old_price='$old_price', stock_status='$stock_status', description='$description'";

        // Check if new images uploaded
        if($img = uploadFile('image', 'img1')) $update_sql .= ", image='$img'";
        if($img = uploadFile('image2', 'img2')) $update_sql .= ", image2='$img'";
        if($img = uploadFile('image3', 'img3')) $update_sql .= ", image3='$img'";
        if($img = uploadFile('image4', 'img4')) $update_sql .= ", image4='$img'";
        if($img = uploadFile('image5', 'img5')) $update_sql .= ", image5='$img'";
        if($img = uploadFile('image6', 'img6')) $update_sql .= ", image6='$img'";
        if($img = uploadFile('image7', 'img7')) $update_sql .= ", image7='$img'";
        if($img = uploadFile('image8', 'img8')) $update_sql .= ", image8='$img'";
        if($vid = uploadFile('video', 'vid')) $update_sql .= ", video='$vid'";

        $update_sql .= " WHERE id=$product_id";
        $conn->query($update_sql);

        // Add new color variants if provided
        if(isset($_POST['color_names']) && is_array($_POST['color_names'])) {
            for($i = 0; $i < count($_POST['color_names']); $i++) {
                $c_name = trim($_POST['color_names'][$i]);
                if(!empty($c_name) && isset($_FILES['color_images']['name'][$i]) && $_FILES['color_images']['error'][$i] == 0) {
                    $c_name_escaped = $conn->real_escape_string($c_name);
                    $ext = strtolower(pathinfo($_FILES['color_images']['name'][$i], PATHINFO_EXTENSION));
                    $v_filename = "var_" . $product_id . "_" . time() . "_" . $i . "." . $ext;
                    if(move_uploaded_file($_FILES['color_images']['tmp_name'][$i], "../images/" . $v_filename)) {
                        $v_image_path = "images/" . $v_filename;
                        $conn->query("INSERT INTO product_color_variants (product_id, variant_image, color_name) VALUES ($product_id, '$v_image_path', '$c_name_escaped')");
                    }
                }
            }
        }

        $_SESSION['success'] = "Product updated successfully!";
        header("Location: products.php");
        exit;

    } else {
        // --- INSERT MODE ---
        $image = uploadFile('image', 'img1');
        if(empty($image)) { $image = 'images/placeholder.jpg'; }

        $image2 = uploadFile('image2', 'img2');
        $image3 = uploadFile('image3', 'img3');
        $image4 = uploadFile('image4', 'img4');
        $image5 = uploadFile('image5', 'img5');
        $image6 = uploadFile('image6', 'img6');
        $image7 = uploadFile('image7', 'img7');
        $image8 = uploadFile('image8', 'img8');
        $video  = uploadFile('video', 'vid');

        $insert_prod = "INSERT INTO products (name, category, material, price, old_price, stock_status, description, image, image2, image3, image4, image5, image6, image7, image8, video) 
                        VALUES ('$name', '$category', '$material', '$price', '$old_price', '$stock_status', '$description', '$image', '$image2', '$image3', '$image4', '$image5', '$image6', '$image7', '$image8', '$video')";
        
        if($conn->query($insert_prod)) {
            $new_product_id = $conn->insert_id;

            if(isset($_POST['color_names']) && is_array($_POST['color_names'])) {
                for($i = 0; $i < count($_POST['color_names']); $i++) {
                    $c_name = trim($_POST['color_names'][$i]);
                    if(!empty($c_name) && isset($_FILES['color_images']['name'][$i]) && $_FILES['color_images']['error'][$i] == 0) {
                        $c_name_escaped = $conn->real_escape_string($c_name);
                        $ext = strtolower(pathinfo($_FILES['color_images']['name'][$i], PATHINFO_EXTENSION));
                        $v_filename = "var_" . $new_product_id . "_" . time() . "_" . $i . "." . $ext;
                        if(move_uploaded_file($_FILES['color_images']['tmp_name'][$i], "../images/" . $v_filename)) {
                            $v_image_path = "images/" . $v_filename;
                            $conn->query("INSERT INTO product_color_variants (product_id, variant_image, color_name) VALUES ($new_product_id, '$v_image_path', '$c_name_escaped')");
                        }
                    }
                }
            }

            $_SESSION['success'] = "Variant Product published successfully!";
            header("Location: products.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit Product' : 'Add Variant Product'; ?> - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>.custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }</style>
</head>
<body class="bg-gray-50 flex flex-col md:flex-row h-screen overflow-hidden text-sm font-sans">

    <!-- SIDEBAR -->
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex justify-between items-center z-10">
            <h1 class="text-xl font-extrabold text-gray-800">
                <i class="fas <?php echo $edit_mode ? 'fa-edit text-blue-600' : 'fa-palette text-[#0B3022];'; ?> mr-2"></i> 
                <?php echo $edit_mode ? 'Edit Product: ' . htmlspecialchars($product['name']) : 'Add Color Variant Product'; ?>
            </h1>
            <a href="products.php" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-bold border hover:bg-gray-200 transition text-xs"><i class="fas fa-arrow-left mr-1"></i> Back</a>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar">
            <form action="" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-200 max-w-5xl mx-auto p-6 md:p-8 space-y-8">
                
                <?php if($edit_mode): ?>
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <?php endif; ?>

                <!-- 1. General Info & Main Images -->
                <div>
                    <h3 class="text-base font-extrabold text-[#0B3022] border-b pb-2 mb-4">1. Product Information & Gallery</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Product Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required class="w-full border rounded-lg px-3 py-2 font-bold outline-none bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Category <span class="text-red-500">*</span></label>
                            <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required class="w-full border rounded-lg px-3 py-2 font-bold outline-none bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Material</label>
                            <input type="text" name="material" value="<?php echo htmlspecialchars($product['material']); ?>" class="w-full border rounded-lg px-3 py-2 font-bold outline-none bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Price (৳) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" value="<?php echo $product['price']; ?>" required class="w-full border rounded-lg px-3 py-2 font-black text-[#0B3022] outline-none bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Old Price</label>
                            <input type="number" name="old_price" value="<?php echo $product['old_price']; ?>" class="w-full border rounded-lg px-3 py-2 font-bold text-gray-400 outline-none bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Stock Status <span class="text-red-500">*</span></label>
                            <select name="stock_status" required class="w-full border rounded-lg px-3 py-2 font-bold outline-none bg-gray-50">
                                <option value="In Stock" <?php if($product['stock_status']=='In Stock') echo 'selected'; ?>>In Stock</option>
                                <option value="Pre-order" <?php if($product['stock_status']=='Pre-order') echo 'selected'; ?>>Pre-order</option>
                                <option value="Out of Stock" <?php if($product['stock_status']=='Out of Stock') echo 'selected'; ?>>Out of Stock</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-xl border mb-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-700">Main Cover Img 1</label>
                            <?php if(!empty($product['image'])): ?><img src="../<?php echo $product['image']; ?>" class="w-8 h-8 object-cover rounded mb-1"><?php endif; ?>
                            <input type="file" name="image" accept="image/*" class="w-full text-[10px] bg-white border p-1 rounded cursor-pointer">
                        </div>
                        <?php for($i=2; $i<=8; $i++): ?>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500">Image <?php echo $i; ?></label>
                            <?php if(!empty($product["image$i"])): ?><img src="../<?php echo $product["image$i"]; ?>" class="w-8 h-8 object-cover rounded mb-1"><?php endif; ?>
                            <input type="file" name="image<?php echo $i; ?>" accept="image/*" class="w-full text-[10px] bg-white border p-1 rounded cursor-pointer">
                        </div>
                        <?php endfor; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Product Video (Optional)</label>
                            <input type="file" name="video" accept="video/*" class="w-full border rounded-lg p-2 text-xs bg-gray-50 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Description <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="2" required class="w-full border rounded-lg px-3 py-1 font-medium outline-none bg-gray-50"><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. Existing & New Color Variants -->
                <div>
                    <div class="flex justify-between items-center border-b pb-2 mb-4">
                        <h3 class="text-base font-extrabold text-[#0B3022]"><i class="fas fa-palette mr-1"></i> 2. Color Variants</h3>
                        <button type="button" onclick="addVariantRow()" class="bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-600 hover:text-white transition"><i class="fas fa-plus mr-1"></i> Add More Color</button>
                    </div>

                    <?php if($edit_mode && count($existing_variants) > 0): ?>
                    <p class="text-xs font-bold text-purple-600 mb-2"><i class="fas fa-info-circle"></i> Already uploaded color variants for this product:</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                        <?php foreach($existing_variants as $ev): ?>
                        <div class="bg-purple-50/50 p-2 rounded-xl border border-purple-200 flex items-center gap-2">
                            <img src="../<?php echo $ev['variant_image']; ?>" class="w-10 h-10 object-cover rounded-lg border">
                            <div class="overflow-hidden">
                                <p class="text-xs font-black text-gray-800 truncate"><?php echo htmlspecialchars($ev['color_name']); ?></p>
                                <a href="delete_variant.php?id=<?php echo $ev['id']; ?>&product_id=<?php echo $product['id']; ?>" onclick="return confirm('Delete this color variant?');" class="text-[10px] text-red-500 hover:underline font-bold"><i class="fas fa-trash-alt"></i> Remove</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div id="variantContainer" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 variant-row flex items-center gap-3">
                            <div class="flex-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Color Name</label>
                                <input type="text" name="color_names[]" placeholder="e.g. Jet Black" class="w-full border rounded-lg px-3 py-1.5 text-xs font-bold bg-white outline-none">
                            </div>
                            <div class="flex-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Variant Image</label>
                                <input type="file" name="color_images[]" accept="image/*" class="w-full border rounded-lg p-1 text-xs bg-white cursor-pointer">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t">
                    <button type="submit" name="save_variant_product" class="w-full bg-[#0B3022] text-[#facc15] py-4 rounded-xl font-extrabold hover:bg-[#072117] transition shadow-lg text-base">
                        <i class="fas fa-save mr-2"></i> <?php echo $edit_mode ? 'Update Product Changes' : 'Publish Product'; ?>
                    </button>
                </div>

            </form>
        </div>
    </main>

    <script>
        function addVariantRow() {
            const container = document.getElementById('variantContainer');
            const count = container.children.length + 1;
            const row = document.createElement('div');
            row.className = "bg-gray-50 p-3 rounded-xl border border-gray-200 variant-row flex items-center gap-3";
            row.innerHTML = `
                <div class="flex-1">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Color Name</label>
                    <input type="text" name="color_names[]" placeholder="Color Name" class="w-full border rounded-lg px-3 py-1.5 text-xs font-bold bg-white outline-none">
                </div>
                <div class="flex-1">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Variant Image</label>
                    <input type="file" name="color_images[]" accept="image/*" class="w-full border rounded-lg p-1 text-xs bg-white cursor-pointer">
                </div>
            `;
            container.appendChild(row);
        }
    </script>
</body>
</html>