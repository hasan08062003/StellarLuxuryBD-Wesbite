<?php
session_start();
session_destroy(); // ইউজারের ডেটা মুছে ফেলা হলো
header("Location: index.php"); // হোমে পাঠিয়ে দেওয়া হলো
exit;
?>