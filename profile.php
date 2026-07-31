<?php 
include 'db.php'; 

// ইউজার লগিন করা না থাকলে সরাসরি লগিন পেজে পাঠিয়ে দেবে
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// ==========================================
// PROFILE UPDATE BACKEND LOGIC
// ==========================================
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $dob = $conn->real_escape_string(trim($_POST['dob']));
    $primary_address = $conn->real_escape_string(trim($_POST['primary_address']));
    $secondary_address = $conn->real_escape_string(trim($_POST['secondary_address']));
    $shipping_address = $conn->real_escape_string(trim($_POST['shipping_address']));
    
    // ইমেজ আপলোড প্রসেসিং
    $profile_image_query = "";
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed_extensions)) {
            // ছবির নাম ইউনিক করা হচ্ছে (ইউজার আইডি ও টাইমস্ট্যাম্প দিয়ে)
            $image_name = "user_" . $user_id . "_" . time() . "." . $ext;
            // তোমার কাস্টম ফোল্ডার 'useruploads' ব্যবহার করা হলো
            $upload_path = "useruploads/" . $image_name;
            
            if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                $profile_image_query = ", profile_image='$upload_path'";
            }
        } else {
            $error_msg = "Invalid image format! Only JPG, JPEG, PNG, and WEBP are allowed.";
        }
    }

    if(empty($error_msg)) {
        // ডেটাবেস আপডেট কুয়েরি
        $update_sql = "UPDATE users SET name='$name', phone='$phone', dob='$dob', primary_address='$primary_address', secondary_address='$secondary_address', shipping_address='$shipping_address' $profile_image_query WHERE id=$user_id";
        
        if($conn->query($update_sql)) {
            $_SESSION['user_name'] = $name; // সেশনের নাম তাৎক্ষণিক আপডেট করার জন্য
            $success_msg = "Profile information updated successfully!";
        } else {
            $error_msg = "Database query failed! Please try again.";
        }
    }
}

// ডেটাবেস থেকে রিয়েল-টাইম ডাটা ফেচ করা হচ্ছে
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// ডিফল্ট ইমেজ অবতার (যদি প্রোফাইল পিকচার সেট করা না থাকে)
$profile_pic = !empty($user['profile_image']) ? $user['profile_image'] : "https://ui-avatars.com/api/?name=".urlencode($user['name'])."&background=0B3022&color=facc15&size=128";

// মেম্বারশিপ টায়ার এবং পয়েন্ট ডাইনামিক ভ্যালু অ্যাসাইন
$tier = !empty($user['membership_tier']) ? $user['membership_tier'] : 'New Member';
$points = isset($user['points']) ? $user['points'] : 0;

// মেম্বারশিপ ব্যাজের কালার কন্ডিশনাল লজিক
$badge_color = "bg-gray-100 text-gray-700 border-gray-200"; // Default New Member
if($tier == 'Regular Member') $badge_color = "bg-blue-50 text-blue-700 border-blue-200";
if($tier == 'Gold Member') $badge_color = "bg-[#facc15]/10 text-[#0B3022] border-[#0B3022]/20 font-extrabold";
if($tier == 'VIP Member') $badge_color = "bg-purple-100 text-purple-700 border-purple-200 font-extrabold animate-pulse";

include 'header.php'; 
?>

<!-- padding-bottom (pb-24) দেওয়া হয়েছে যাতে মোবাইলের নিচের মেনু কন্টেন্টকে ঢেকে না ফেলে -->
<main class="container mx-auto px-4 py-6 md:py-12 flex-grow min-h-[70vh] pb-24 md:pb-12 bg-gray-50">
    
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">My Profile <i class="fas fa-user-edit text-[#0B3022] ml-2"></i></h1>
        </div>

        <!-- সাকসেস এবং এরর অ্যালার্ট মেসেজ -->
        <?php if(!empty($success_msg)): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded-xl font-bold mb-4 border border-green-200 shadow-sm">
                <i class="fas fa-check-circle mr-1"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-xl font-bold mb-4 border border-red-200 shadow-sm">
                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <!-- ফর্ম সাবমিশন এবং ফাইল আপলোডের এনক্রিপশন টাইপ যুক্ত করা হয়েছে -->
            <form id="profileForm" action="profile.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_profile" value="1">
                
                <!-- Profile Image Section -->
                <div class="flex flex-col items-center sm:items-start sm:flex-row gap-6 mb-8 border-b border-gray-100 pb-8">
                    <div class="relative group">
                        <div class="w-24 h-24 md:w-28 md:h-28 rounded-full border-4 border-[#0B3022]/10 overflow-hidden bg-gray-50 shadow-sm">
                            <img id="profilePreview" src="<?php echo $profile_pic; ?>" alt="Profile Image" class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Camera Edit Overlay Icon -->
                        <label id="imageUploadLabel" class="hidden absolute bottom-0 right-0 bg-[#0B3022] hover:bg-[#072117] text-[#facc15] w-8 h-8 rounded-full flex items-center justify-center cursor-pointer shadow-md transition transform hover:scale-110">
                            <i class="fas fa-camera text-sm"></i>
                            <input type="file" name="profile_image" class="hidden" accept="image/*" onchange="previewImage(event)">
                        </label>
                    </div>
                    
                    <div class="text-center sm:text-left mt-2 sm:mt-2">
                        <h2 class="text-xl md:text-2xl font-extrabold text-[#0B3022] uppercase tracking-wide"><?php echo htmlspecialchars($user['name']); ?></h2>
                        <p class="text-sm font-semibold text-gray-500 mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                        
                        <!-- Dynamic Badge & Point Row -->
                        <div class="flex items-center justify-center sm:justify-start gap-2.5">
                            <span class="<?php echo $badge_color; ?> text-xs font-bold px-3.5 py-1 rounded-full border shadow-sm flex items-center gap-1">
                                <i class="fas fa-crown text-[10px]"></i> <?php echo htmlspecialchars($tier); ?>
                            </span>
                            <span class="bg-gray-50 text-gray-700 text-xs font-bold px-3.5 py-1 rounded-full border border-gray-200 shadow-sm flex items-center gap-1">
                                <i class="fas fa-coins text-[#facc15]"></i> <?php echo number_format($points); ?> Points
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Personal Information Grid -->
                <h3 class="text-lg font-bold text-gray-800 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" readonly class="profile-input w-full border border-transparent bg-transparent rounded-lg px-3 py-2 text-gray-800 font-bold focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email Address (Locked)</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly class="w-full border border-transparent bg-transparent rounded-lg px-3 py-2 text-gray-400 font-bold focus:outline-none cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="Not specified" readonly class="profile-input w-full border border-transparent bg-transparent rounded-lg px-3 py-2 text-gray-800 font-bold focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Date of Birth</label>
                        <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" readonly class="profile-input w-full border border-transparent bg-transparent rounded-lg px-3 py-2 text-gray-800 font-bold focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Address Section -->
                <h3 class="text-lg font-bold text-gray-800 mb-4">Address Details</h3>
                <div class="space-y-5 mb-8">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Primary Address</label>
                        <textarea name="primary_address" readonly rows="2" placeholder="Not specified" class="profile-input w-full border border-transparent bg-transparent rounded-lg px-3 py-2 text-gray-800 font-bold focus:outline-none transition-all resize-none"><?php echo htmlspecialchars($user['primary_address']); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Secondary Address (Optional)</label>
                        <textarea name="secondary_address" readonly rows="2" placeholder="Not specified" class="profile-input w-full border border-transparent bg-transparent rounded-lg px-3 py-2 text-gray-800 font-bold focus:outline-none transition-all resize-none"><?php echo htmlspecialchars($user['secondary_address']); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Default Shipping Address</label>
                        <textarea name="shipping_address" readonly rows="2" placeholder="Not specified" class="profile-input w-full border border-transparent bg-transparent rounded-lg px-3 py-2 text-gray-800 font-bold focus:outline-none transition-all resize-none"><?php echo htmlspecialchars($user['shipping_address']); ?></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <!-- Default View Mode Button -->
                    <button type="button" id="editBtn" onclick="enableEditMode()" class="bg-[#0B3022] text-[#facc15] px-6 py-2.5 rounded-lg font-bold hover:bg-[#072117] transition flex items-center gap-2 shadow-md">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                    
                    <!-- Edit Mode Buttons (Hidden initially) -->
                    <button type="button" id="cancelBtn" onclick="cancelEditMode()" class="hidden border-2 border-gray-300 text-gray-700 px-6 py-2.5 rounded-lg font-bold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" id="saveBtn" class="hidden bg-[#0B3022] text-[#facc15] px-8 py-2.5 rounded-lg font-bold hover:bg-[#072117] transition shadow-[0_4px_14px_0_rgba(11,48,34,0.39)] flex items-center gap-2">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>
</main>

<!-- JavaScript for Edit Mode & Image Preview -->
<script>
    const inputs = document.querySelectorAll('.profile-input');
    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const saveBtn = document.getElementById('saveBtn');
    const imageUploadLabel = document.getElementById('imageUploadLabel');

    // এডিট মোড চালু করার ফাংশন
    function enableEditMode() {
        inputs.forEach(input => {
            input.removeAttribute('readonly');
            input.classList.remove('border-transparent', 'bg-transparent');
            input.classList.add('border-gray-300', 'bg-white', 'focus:border-[#0B3022]');
        });
        
        imageUploadLabel.classList.remove('hidden');
        editBtn.classList.add('hidden');
        cancelBtn.classList.remove('hidden');
        saveBtn.classList.remove('hidden');
    }

    // এডিট মোড ক্যানসেল করার ফাংশন
    function cancelEditMode() {
        inputs.forEach(input => {
            input.setAttribute('readonly', true);
            input.classList.add('border-transparent', 'bg-transparent');
            input.classList.remove('border-gray-300', 'bg-white', 'focus:border-[#0B3022]');
        });
        
        imageUploadLabel.classList.add('hidden');
        editBtn.classList.remove('hidden');
        cancelBtn.classList.add('hidden');
        saveBtn.classList.add('hidden');
        
        // ফর্ম রিসেট করে ছবি ও ডাটা আগের অবস্থায় ফিরিয়ে আনা
        document.getElementById('profileForm').reset();
        document.getElementById('profilePreview').src = "<?php echo $profile_pic; ?>";
    }

    // ছবি সিলেক্ট করলে ইনস্ট্যান্টলি প্রিভিউ দেখানোর ফাংশন
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('profilePreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

<?php include 'footer.php'; ?>
</body>
</html>