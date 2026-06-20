<?php
session_start();
// শুধু রোগীর সেশনটি আনসেট করা
unset($_SESSION['nid']); 

header("Location: login.php"); // এটি patient/login.php পেজে নিয়ে যাবে
exit();
?>