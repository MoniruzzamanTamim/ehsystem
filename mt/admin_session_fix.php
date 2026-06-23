<?php
// mt/admin_session_fix.php

// ডাটাবেজ কানেকশন নিশ্চিত করা
if (!isset($conn)) {
    include '../config/dbconnect.php';
}

// সেশন চেক করা (যদি স্টার্ট করা না থাকে)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 🎯 ১. প্রধান চেক: যদি ইউআরএল-এ 'view_id' (NID) পাওয়া যায় (অ্যাডমিন মোড)
if (isset($_GET['view_id']) && !empty($_GET['view_id'])) {
    
    $mt_id = mysqli_real_escape_string($conn, $_GET['view_id']);
    
    // ডাটাবেজ থেকে এই মেডিকেল টেকনোলজিস্টের নাম তুলে আনা
    $mt_user_query = mysqli_query($conn, "SELECT Full_Name FROM medical_technologist WHERE NID='$mt_id'");
    
    if (mysqli_num_rows($mt_user_query) > 0) {
        $mt_user_data = mysqli_fetch_assoc($mt_user_query);
        $user_name = $mt_user_data['Full_Name'];
    } else {
        $user_name = "Laboratory Specialist";
    }
    
    // 💡 ম্যাজিক লাইন: পরবর্তী সাব-পেজগুলোর সুবিধার জন্য ব্রাউজার সেশনেও এটি ব্যাকআপ রেখে দেওয়া হলো
    $_SESSION['admin_view_mt_id'] = $mt_id;
    $_SESSION['admin_view_mt_name'] = $user_name;

} 
// 🎯 ২. ব্যাকআপ চেক: ইউআরএল-এ যদি 'view_id' না থাকে, কিন্তু ব্রাউজার সেশনে আগে থেকেই সেভ থাকে
elseif (isset($_SESSION['admin_view_mt_id'])) {
    
    $mt_id = $_SESSION['admin_view_mt_id'];
    $user_name = $_SESSION['admin_view_mt_name'];

} 
// 🎯 ৩. নরমাল মোড: যদি অ্যাডমিন মোড না হয়, তবে সাধারণ ল্যাব কর্মীর নিজস্ব লগইন সেশন চেক করো
else {
    
    if (!isset($_SESSION['mt'])) {
        // যদি কোনো সেশনই না পাওয়া যায়, শুধু তখনই লগইন পেজে পাঠাবে
        header("Location: login.php");
        exit();
    }
    
    $mt_id = $_SESSION['mt'];
    
    // নরমাল লগইন করা এমটি-র নাম ডাটাবেজ থেকে আনা
    $mt_user_query = mysqli_query($conn, "SELECT Full_Name FROM medical_technologist WHERE NID='$mt_id'");
    if (mysqli_num_rows($mt_user_query) > 0) {
        $mt_user_data = mysqli_fetch_assoc($mt_user_query);
        $user_name = $mt_user_data['Full_Name'];
    } else {
        $user_name = "Laboratory Specialist";
    }
}
?>