<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// সিকিউরিটি চেক: সেশন কি (sk) এবং সেশন ডাটা আছে কিনা
if (!isset($_GET['sk']) || !isset($_SESSION[$_GET['sk']])) {
    die("Unauthorized Access! This link is expired or invalid.");
}

// সেশন থেকে ইউজার ডাটা রিড করা
$session_data = $_SESSION[$_GET['sk']];
$user_id   = $session_data['id'];
$user_nid  = $session_data['NID'];
$user_name = $session_data['Full_Name'];
$tab_token = $session_data['tab_token'];

// 💡 নোট: আপনার আসল ড্যাশবোর্ডে কোয়েরি করার জন্য যদি $user_id বা $user_nid লাগে, 
// তা এখন এই ভ্যারিয়েবলগুলো থেকে পেয়ে যাবেন।
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin View - Doctor Dashboard</title>
    </head>
<body>

    <script>
        // ১. চেক করা হচ্ছে ইউআরএল কপি করে অন্য ট্যাবে নেওয়া হয়েছে কিনা
        var localToken = sessionStorage.getItem('tab_secure_token');
        if (window.name !== "<?php echo $tab_token; ?>" || localToken !== "<?php echo $tab_token; ?>") {
            alert('Security Notice: Session expired or invalid tab!');
            window.close(); // ট্যাব বন্ধ করে দেবে
            document.body.innerHTML = "Access Denied.";
        }

        // ২. ট্যাব ক্লোজ বা কেটে দেওয়ার সাথে সাথে সার্ভার থেকে সেশনটি ধ্বংস করার রিকোয়েস্ট
        window.addEventListener('unload', function () {
            navigator.sendBeacon('../admin/clear_tab_session.php?sk=<?php echo $_GET['sk']; ?>');
        });
    </script>

    <div class="container mt-5">
        <h2>Welcome Admin, viewing Profile of: <?php echo htmlspecialchars($user_name); ?></h2>
        <p>NID: <?php echo htmlspecialchars($user_nid); ?></p>
        <div class="alert alert-info">You are viewing this portal as an Administrator.</div>
    </div>

</body>
</html>