<?php
include '../config/dbconnect.php';

// যদি জাভাস্ক্রিপ্ট থেকে check_nid প্যারামিটার পাঠানো হয়
if (isset($_GET['check_nid'])) {
    // গ্লোবাল সিকিউরিটি ফিল্টার দিয়ে ইনপুট সেফ করা
    $nid = secure_input($_GET['check_nid']);

    // ডাটাবেজে এই NID আগে থেকেই আছে কি না তা চেক করা
    $sql = "SELECT id FROM patient WHERE NID = '$nid'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "taken";
    } else {
        echo "available";
    }
    exit();
}
?>