<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// অ্যাডমিন লগইন ছাড়া কেউ যেন এই ফাইল অ্যাক্সেস করতে না পারে
if (!isset($_SESSION['central_admin']) && !isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

// ডাটাবেজ কানেকশন যুক্ত করা
include '../config/dbconnect.php';

// URL থেকে NID এবং type প্যারামিটার আছে কিনা তা চেক করা
if (isset($_GET['NID']) && isset($_GET['type'])) {
    $nid = mysqli_real_escape_string($conn, $_GET['NID']);
    $type = $_GET['type'];
    
    // ইউজার টাইপ অনুযায়ী সঠিক ডাটাবেজ টেবিল সিলেক্ট করা
    $table = '';
    if ($type === 'patient') {
        $table = 'patient';
    } elseif ($type === 'doctor') {
        $table = 'doctor';
    } elseif ($type === 'mt') {
        $table = 'medical_technologist';
    }

    // টেবিল যদি ভ্যালিড হয়, তবে Status আপডেট কুয়েরি চলবে
    if (!empty($table)) {
        // ডাটাবেজে Status পরিবর্তন করে 'Approved' করা হচ্ছে
        $sql = "UPDATE `$table` SET `Status`='Approved' WHERE `NID`='$nid'";
        
        if (mysqli_query($conn, $sql)) {
            /* HTTP_REFERER ব্যবহার করে ইউজার যে পেজ থেকে ক্লিক করেছে (pending_approved_request.php), 
               ঠিক সেই পেজেই তাকে ফেরত পাঠানো হচ্ছে।
            */
            if (isset($_SERVER['HTTP_REFERER'])) {
                $referer = $_SERVER['HTTP_REFERER'];
                // যদি অলরেডি ইউআরএল এ কোনো কুয়েরি স্ট্রিং বা পুরানো স্ট্যাটাস থাকে তা হ্যান্ডেল করার জন্য
                if (strpos($referer, 'status=') !== false) {
                    header("Location: " . $referer);
                } else {
                    $separator = (strpos($referer, '?') !== false) ? '&' : '?';
                    header("Location: " . $referer . $separator . "status=success");
                }
            } else {
                // কোনো কারণে রেফারার না পাওয়া গেলে ডিফল্ট নতুন ড্যাশবোর্ডে ব্যাক করবে
                header("Location: pending_approved_request.php?status=success"); 
            }
            exit();
        } else {
            echo "Database Error: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid User Type requested!";
    }
} else {
    // সরাসরি অ্যাক্সেস করলে আগের পেজে বা নতুন ড্যাশবোর্ডে ফেরত যাবে
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: pending_approved_request.php");
    }
    exit();
}
?>