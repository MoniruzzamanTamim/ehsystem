<?php
session_start();
if (!isset($_SESSION['nid'])) {
    header("Location: ../login.php");
    exit();
}
include '../config/dbconnect.php';
include '../includes/header.php';

$patient_nid = $_SESSION['nid'];

// ডাটাবেজ থেকে পেশেন্টের সব তথ্য নিয়ে আসা
$sql = "SELECT * FROM patient WHERE NID = '$patient_nid'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>

<div style="max-width: 600px; margin: 30px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
    <h2 style="text-align: center; color: #333;">My Personal Health Profile</h2>
    <hr><br>

    <table cellpadding="10" cellspacing="0" width="100%" style="border-collapse: collapse;">
        <tr style="background: #f9f9f9;">
            <td style="font-weight: bold; width: 40%;">National ID (NID):</td>
            <td><?php echo htmlspecialchars($user['NID']); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Full Name:</td>
            <td><?php echo htmlspecialchars($user['Full_Name']); ?></td>
        </tr>
        <tr style="background: #f9f9f9;">
            <td style="font-weight: bold;">Father's Name:</td>
            <td><?php echo htmlspecialchars($user['Father_Name'] ?? 'Not Provided'); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Mother's Name:</td>
            <td><?php echo htmlspecialchars($user['Mother_Name'] ?? 'Not Provided'); ?></td>
        </tr>
        <tr style="background: #f9f9f9;">
            <td style="font-weight: bold;">Date of Birth:</td>
            <td><?php echo htmlspecialchars($user['Birth_Date'] ?? 'Not Provided'); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Blood Group:</td>
            <td style="color: red; font-weight: bold;"><?php echo htmlspecialchars($user['Blood_Group'] ?? 'Unknown'); ?></td>
        </tr>
        <tr style="background: #f9f9f9;">
            <td style="font-weight: bold;">Phone Number:</td>
            <td><?php echo htmlspecialchars($user['Phone']); ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Email Address:</td>
            <td><?php echo htmlspecialchars($user['Email']); ?></td>
        </tr>
        <tr style="background: #f9f9f9;">
            <td style="font-weight: bold;">Account Status:</td>
            <td><span style="background: green; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px;"><?php echo $user['Status']; ?></span></td>
        </tr>
    </table>

    <br><br>
    <div style="text-align: center;">
        <a href="dashboard.php" style="background: #555; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Back to Dashboard</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>