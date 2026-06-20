<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
?>