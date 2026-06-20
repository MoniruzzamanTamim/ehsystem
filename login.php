<?php
session_start();
include 'config/dbconnect.php';

// যদি ইউজার অলরেডি লগইন থাকে, তবে ড্যাশবোর্ডে পাঠিয়ে দেবে
if (isset($_SESSION['nid'])) {
    header("Location: patient/dashboard.php");
    exit();
}

$msg = "";

if (isset($_POST['login'])) {
    // config/dbconnect.php এর secure_input ফাংশন ব্যবহার করে ডাটা ফিল্টার করা হচ্ছে
    $nid = secure_input($_POST['nid']);
    $password = secure_input($_POST['password']);
    
    // ডাটাবেজ কুয়েরি (শুধুমাত্র Approved পেশেন্টরাই লগইন করতে পারবে)
    $sql = "SELECT * FROM patient WHERE NID='$nid' AND Password='$password' AND Status='Approved'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        // সেশন ভ্যারিয়েবল সেট করা হচ্ছে
        $_SESSION['nid'] = $nid;
        header("Location: patient/dashboard.php");
        exit();
    } else {
        // অ্যাকাউন্ট পেন্ডিং, রিজেক্টেড নাকি ভুল ডাটা তা চেক করার জন্য আরেকটি কুয়েরি
        $check_status = mysqli_query($conn, "SELECT Status FROM patient WHERE NID='$nid' AND Password='$password'");
        if (mysqli_num_rows($check_status) > 0) {
            $row = mysqli_fetch_assoc($check_status);
            if ($row['Status'] == 'Pending') {
                $msg = "Your account is pending admin approval.";
            } else if ($row['Status'] == 'Rejected') {
                $msg = "Your account registration has been rejected by admin.";
            }
        } else {
            $msg = "Invalid NID or Password. Please try again.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.05);">
    <h2 style="text-align: center; color: #333; margin-bottom: 20px;">Patient Login</h2>
    
    <?php if (!empty($msg)): ?>
        <div style="background-color: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; font-size: 14px;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">National ID (NID):</label>
            <input type="text" name="nid" required placeholder="Enter your NID" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Password:</label>
            <input type="password" name="password" required placeholder="Enter your password" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>
        
        <button type="submit" name="login" style="width: 100%; background-color: #99CC00; color: white; padding: 12px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background 0.2s;">
            Sign In
        </button>
    </form>
    
    <div style="margin-top: 15px; text-align: center; font-size: 14px;">
        <p>Don't have an account? <a href="patient/patient_registration.php" style="color: #1e88e5; text-decoration: none; font-weight: bold;">Register Here</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
