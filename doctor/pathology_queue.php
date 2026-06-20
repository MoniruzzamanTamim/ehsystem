<?php
session_start();
if (!isset($_SESSION['doctor'])) {
    header("Location: login.php");
    exit();
}
include '../config/dbconnect.php';
include '../includes/header.php';

$doctor_nid = $_SESSION['doctor'];
?>

<div style="padding: 20px;">
    <h2>Completed Pathology Lab Reports</h2>
    <p>Below are the reports submitted by Medical Technologists for your prescribed tests.</p>
    <hr><br>

    <table border="1" cellpadding="10" width="100%" style="border-collapse: collapse; text-align: left;">
        <tr style="background: #f2f2f2;">
            <th>Patient NID</th>
            <th>Test Name</th>
            <th>Test Findings / Result</th>
            <th>Submission Date</th>
            <th>Status</th>
        </tr>
        <?php
        // ডাক্তারের আইডি দিয়ে ফিল্টার করে শুধুমাত্র কমপ্লিট হওয়া টেস্টগুলো দেখা যাবে
        $sql = "SELECT * FROM pathology_tests WHERE Doctor_NID='$doctor_nid' AND Status='Completed' ORDER BY Update_Date DESC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['Patient_NID']) . "</td>
                        <td>" . htmlspecialchars($row['Test_Name']) . "</td>
                        <td style='background: #f9fff9; color: #2e7d32;'><strong>" . nl2br(htmlspecialchars($row['Test_Result'])) . "</strong></td>
                        <td>" . $row['Update_Date'] . "</td>
                        <td><span style='color: green; font-weight: bold;'>✓ Ready</span></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center; color: gray;'>No completed lab reports found.</td></tr>";
        }
        ?>
    </table>
    
    <br><br>
    <a href="dashboard.php">Back to Dashboard</a>
</div>

<?php include '../includes/footer.php'; ?>