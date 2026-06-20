<?php
session_start();

// লগআউট করার আগে কোন ইউজার লগইন ছিল তা ট্র্যাক করা
$redirect_url = "index.php"; // ডিফল্ট বা ফলব্যাক পেজ

if (isset($_SESSION['doctor'])) {
    $redirect_url = "doctor/login.php";
} elseif (isset($_SESSION['nid'])) {
    $redirect_url = "patient/login.php"; // তোমার স্ট্রাকচার অনুযায়ী পাথ ঠিক করে নিও
} elseif (isset($_SESSION['mt'])) {
    $redirect_url = "medical_technologist/login.php";
}

// সেশন সম্পূর্ণ খালি ও ধ্বংস করা
$_SESSION = array();
session_destroy();

// নির্দিষ্ট পোর্টালে রিডাইরেক্ট করা
header("Location: " . $redirect_url);
exit();
?>