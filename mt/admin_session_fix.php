<?php
// mt/admin_session_fix.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ১. যদি ইউআরএল-এ 'sk' কোডটি সরাসরি পাওয়া যায়
if (isset($_GET['sk']) && isset($_SESSION[$_GET['sk']])) {
    // এই কোডটি ব্রাউজারের কুকি সেশনে আজীবনের জন্য (ট্যাব খোলা থাকা পর্যন্ত) সেভ করে রাখো
    $_SESSION['active_admin_sk'] = $_GET['sk']; 
    
    $session_data = $_SESSION[$_GET['sk']];
    $_SESSION['mt'] = $session_data['NID']; 
} 
// ২. 💡 ম্যাজিক পার্ট: যদি ইউআরএল-এ 'sk' না থাকে, কিন্তু ব্রাউজারের মেমরিতে আগে থেকে সেভ থাকে
else if (isset($_SESSION['active_admin_sk']) && isset($_SESSION[$_SESSION['active_admin_sk']])) {
    $saved_sk = $_SESSION['active_admin_sk'];
    $session_data = $_SESSION[$_saved_sk];
    
    $_SESSION['mt'] = $session_data['NID'];
}
// ৩. যদি কোনো সেশনই ম্যাচ না করে, শুধুমাত্র তখনই লগইন পেজে পাঠাবে
else if (!isset($_SESSION['mt'])) {
    header("Location: login.php");
    exit();
}