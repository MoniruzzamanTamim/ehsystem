<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "helthcare";

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if (!$conn)
{
    die(
        "Database Connection Failed : "
        . mysqli_connect_error()
    );
}

?>


<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "helthcare"; // আপনার ডাটাবেজের নাম অনুযায়ী

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// === থিসিস সিকিউরিটি ফিল্টার ফাংশন ===
if (!function_exists('secure_input')) {
    function secure_input($data) {
        global $conn;
        // অতিরিক্ত স্পেস দূর করা
        $data = trim($data);
        // ব্যাকস্ল্যাশ দূর করা
        $data = stripslashes($data);
        // HTML কোড এক্সিকিউট হওয়া বন্ধ করা (XSS Protection)
        $data = htmlspecialchars($data);
        // SQL Injection থেকে বাঁচার জন্য স্ট্রিং এস্কেপ করা
        $data = mysqli_real_escape_string($conn, $data);
        return $data;
    }
}


?>



