

<?php
include 'config/dbconnect.php';

$nid = 'doc001'; // আপনার ডাক্তারের NID

// নতুন টোকেন তৈরি করুন
$token = bin2hex(random_bytes(32));
$expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$update = "UPDATE doctor SET login_token='$token', token_expiry='$expiry' WHERE NID='$nid'";
mysqli_query($conn, $update);

echo "Token: $token <br>";
echo "Expiry: $expiry <br>";
echo "<a href='admin/admin_autologin.php?role=doctor&NID=$nid&token=$token' target='_blank'>Test Login</a>";
?>