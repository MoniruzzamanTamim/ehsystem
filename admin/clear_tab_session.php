<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['sk'])) {
    $session_key = $_GET['sk'];
    if (isset($_SESSION[$session_key])) {
        unset($_SESSION[$session_key]); // ওয়ান-টাইম অ্যাডমিন ভিউ সেশন ডিলিট
    }
    unset($_SESSION['mt']); // টেম্পোরারি এমটি সেশন ডিলিট
}
?>