<?php
session_start();
unset($_SESSION['mt']); 

header("Location: login.php"); // এটি medical_technologist/login.php পেজে নিয়ে যাবে
exit();
?>