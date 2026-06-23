<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 🎯 ম্যাজিক লজিক: যদি ইউআরএল-এ view_id থাকে, তার মানে অ্যাডমিন ল্যাব কর্মীর প্রোফাইল দেখছে।
// তাই ব্রাউজারে অ্যাডমিন সেশন থাকলেও জোর করে mt_header.php লোড করা হবে।
if (isset($_GET['view_id'])) {
    
    include 'mt_header.php';

} else {
    // ইউআরএল-এ view_id না থাকলে আপনার আগের নরমাল লজিক কাজ করবে
    if (isset($_SESSION['doctor'])) {

        include 'doctor_header.php';

    } elseif (isset($_SESSION['patient'])) {

        include 'patient_header.php';

    } elseif (isset($_SESSION['mt'])) {

        include 'mt_header.php';

    } elseif (isset($_SESSION['admin'])) {

        include 'admin_header.php';

    } else {

        echo "<a href='../login.php' style='color:white; margin:0 15px; text-decoration:none;'>Patient Login</a>";

        echo "<a href='../doctor/login.php' style='color:white; margin:0 15px; text-decoration:none;'>Doctor Login</a>";

        echo "<a href='../mt/login.php' style='color:white; margin:0 15px; text-decoration:none;'>MT Login</a>";

        echo "<a href='../admin/login.php' style='color:white; margin:0 15px; text-decoration:none;'>Admin Login</a>";
    }
}
?>