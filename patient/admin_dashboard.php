<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ১. 🔒 সিকিউরিটি চেক: সেশন কি (sk) এবং সেশন ডাটা আছে কিনা
if (!isset($_GET['sk']) || !isset($_SESSION[$_GET['sk']])) {
    die("Unauthorized Access! This link is expired or invalid.");
}

// সেশন থেকে ইউজার ডাটা রিড করা
$session_data = $_SESSION[$_GET['sk']];
$user_id   = $session_data['id'];
$user_nid  = $session_data['NID'];
$user_name = $session_data['Full_Name'];
$tab_token = $session_data['tab_token'];

// ডাটাবেজ কানেকশন
include '../config/dbconnect.php';

// ২. 🗄️ পেশেন্টের জন্য স্পেসিফিক ডাটাবেজ কুয়েরি
$patient_query = "SELECT * FROM patient WHERE id='$user_id' AND NID='$user_nid'";
$patient_result = mysqli_query($conn, $patient_query);
$patient_info = mysqli_fetch_assoc($patient_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin View - Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- 🚨 ট্যাব ক্লোজ সিকিউরিটি হ্যান্ডেলার (ইউআরএল প্রটেকশন) 🚨 -->
    <script>
        var localToken = sessionStorage.getItem('tab_secure_token');
        if (window.name !== "<?php echo $tab_token; ?>" || localToken !== "<?php echo $tab_token; ?>") {
            alert('Security Notice: Session expired or invalid tab!');
            window.close();
            document.body.innerHTML = "Access Denied.";
        }

        // ট্যাব বন্ধ করলে সার্ভার থেকে সেশন ধ্বংস করবে (পাথ খেয়াল রাখুন: ../admin/)
        window.addEventListener('unload', function () {
            navigator.sendBeacon('../admin/clear_tab_session.php?sk=<?php echo $_GET['sk']; ?>');
        });
    </script>

    <div class="container mt-5">
        <div class="alert alert-warning fw-bold">⚠️ You are viewing this portal as an Administrator (Patient Mode).</div>
        
        <div class="card p-4 shadow-sm">
            <h3>Patient Profile: <?php echo htmlspecialchars($user_name); ?></h3>
            <p><strong>NID:</strong> <?php echo htmlspecialchars($user_nid); ?></p>
            <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($patient_info['Blood_Group'] ?? 'N/A'); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient_info['Phone'] ?? 'N/A'); ?></p>
            <hr>
            <!-- এখানে আপনার পেশেন্ট ড্যাশবোর্ডের বাকি ডিজাইন ও কোড বসাবেন -->
            <h5>Patient Medical History & Reports</h5>
            <p class="text-muted">No records found for this patient.</p>
        </div>
    </div>

</body>
</html>