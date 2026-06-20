<?php
session_start();
// ১. সিকিউরিটি চেক: শুধুমাত্র লগইন করা অ্যাডমিনরাই যেন এটি অ্যাক্সেস করতে পারে
if (!isset($_SESSION['central_admin']) && !isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

// ২. ডাটাবেজ কানেকশন ইনক্লুড করা
include '../config/dbconnect.php';

// ৩. চেক করা যে URL-এ NID এবং Type দেওয়া হয়েছে কিনা
if (isset($_GET['NID']) && !empty($_GET['NID']) && isset($_GET['type'])) {
    
    // ৪. SQL Injection থেকে বাঁচার জন্য ডাটা ক্লিন করা
    $nid = mysqli_real_escape_string($conn, trim($_GET['NID'])); 
    $type = mysqli_real_escape_string($conn, trim($_GET['type'])); 

    // ৫. ইউজার টাইপ অনুযায়ী সঠিক টেবিল নির্ধারণ করা
    $table = "";
    if ($type === 'patient') {
        $table = "patient";
    } elseif ($type === 'doctor') {
        $table = "doctor";
    } elseif ($type === 'mt') {
        $table = "medical_technologist";
    }

    if (!empty($table)) {
        // ৬. ডাটাবেজ থেকে রিমুভ না করে স্ট্যাটাস আপডেট করে 'Rejected' করা হচ্ছে
        $query = "UPDATE $table SET Status='Rejected' WHERE NID='$nid'";
        $result = mysqli_query($conn, $query);

        if (mysqli_query($conn, $sql)) {
            header("Location: pending_approved_request.php?status=rejected"); 
            exit();
        } else {
            die("Error rejecting record: " . mysqli_error($conn));
        }
    } else {
        header("Location: registration.php?status=invalid_type");
        exit();
    }
} else {
    header("Location: registration.php?status=invalid_nid");
    exit();
}
?>