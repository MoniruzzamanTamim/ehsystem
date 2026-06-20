<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../config/dbconnect.php';

// অ্যাডমিন প্রোটেকশন চেক (শুধুমাত্র অ্যাডমিনই ঢুকতে পারবে)
if (!isset($_SESSION['central_admin']) && !isset($_SESSION['admin'])) {
    die("Unauthorized Access!");
}

if (isset($_GET['role']) && isset($_GET['NID']) && isset($_GET['token'])) {
    
    $role = mysqli_real_escape_string($conn, $_GET['role']);
    $nid = mysqli_real_escape_string($conn, $_GET['NID']);
    $token = mysqli_real_escape_string($conn, $_GET['token']);

    $table = '';
    $redirect_url = '';

    // আপনার নতুন ফাইল প্যাথ অনুযায়ী রিডাইরেক্ট সেটআপ
    if ($role === 'doctor') {
        $table = 'doctor';
        $redirect_url = '../doctor/admin_dashboard.php';
    } elseif ($role === 'patient') {
        $table = 'patient';
        $redirect_url = '../patient/admin_dashboard.php'; // (আপনার ফোল্ডার নাম অনুযায়ী পাথ ঠিক করে নিন)
    } elseif ($role === 'mt') {
        $table = 'medical_technologist';
        $redirect_url = '../mt/admin_dashboard.php';
    } else {
        die("Invalid Role.");
    }

    // ডাটাবেজ থেকে টোকেন ও স্ট্যাটাস চেক
    $query = "SELECT * FROM $table WHERE NID='$nid' AND login_token='$token' AND Status='Approved'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user_data = mysqli_fetch_assoc($result);

        // 🔒 ওয়ান-টাইম ইউজ: ডাটাবেজ থেকে টোকেন ডিলিট
        $clear_token_query = "UPDATE $table SET login_token=NULL WHERE NID='$nid'";
        mysqli_query($conn, $clear_token_query);

        // ট্যাব লেভেল সিকিউরিটির জন্য ইউনিক টোকেন ও সেশন কি জেনারেট
        $tab_session_token = bin2hex(random_bytes(32));
        $session_key = "autologin_" . $role . "_" . $nid;
        
        // অ্যাডমিনের মূল সেশন নষ্ট না করে আলাদা কি-তে ডাটা রাখা
        $_SESSION[$session_key] = [
            'id' => $user_data['id'],
            'NID' => $user_data['NID'],
            'Full_Name' => $user_data['Full_Name'],
            'tab_token' => $tab_session_token
        ];

        // জাভাস্ক্রিপ্ট সিকিউরিটি গেটওয়ে
        ?>
        <!DOCTYPE html>
        <html>
        <head><title>Loading Admin View...</title></head>
        <body>
            <script>
                // শুধুমাত্র বর্তমান ট্যাবের মেমরিতে টোকেন লক করা
                window.name = "<?php echo $tab_session_token; ?>";
                sessionStorage.setItem('tab_secure_token', "<?php echo $tab_session_token; ?>");
                
                // ডাইনামিক সেশন কি সহ অ্যাডমিন ড্যাশবোর্ডে রিডাইরেক্ট
                window.location.href = "<?php echo $redirect_url; ?>?sk=<?php echo $session_key; ?>";
            </script>
        </body>
        </html>
        <?php
        exit();
    } else {
        echo "<script>alert('Link Expired or Invalid Token!'); window.close();</script>";
        exit();
    }
}
?>