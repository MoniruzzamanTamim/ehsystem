<?php
session_start();
if (!isset($_SESSION['nid'])) {
    header("Location: ../login.php");
    exit();
}
include '../config/dbconnect.php';
include '../includes/header.php';

$patient_nid = $_SESSION['nid'];
?>

<div style="padding: 20px;">
    <h2>Your Electronic Health Records (EHR)</h2>
    <a href="dashboard.php">Back to Dashboard</a>
    <hr><br>

    <h3>1. Prescriptions Given by Doctors</h3>
    <?php
    $p_sql = "SELECT pr.*, d.Full_Name as Doctor_Name, d.Specialization 
              FROM prescription pr
              JOIN doctor d ON pr.Doctor_NID = d.NID
              WHERE pr.Patient_NID = '$patient_nid'
              ORDER BY pr.Prescription_Date DESC";
    $p_result = mysqli_query($conn, $p_sql);

    if (mysqli_num_rows($p_result) > 0) {
        while ($row = mysqli_fetch_assoc($p_result)) {
            echo "<div style='background: #fdfefe; border-left: 5px solid #99CC00; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);'>";
            echo "<p><strong>Date:</strong> " . $row['Prescription_Date'] . " | <strong>Doctor:</strong> Dr. " . htmlspecialchars($row['Doctor_Name']) . " (" . htmlspecialchars($row['Specialization']) . ")</p>";
            echo "<p><strong>Symptoms:</strong> " . nl2br(htmlspecialchars($row['Symptoms'])) . "</p>";
            echo "<p><strong>Diagnosis:</strong> " . nl2br(htmlspecialchars($row['Diagnosis'])) . "</p>";
            echo "<p style='color: green;'><strong>Medicines & Advice:</strong><br>" . nl2br(htmlspecialchars($row['Medicines'])) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p style='color: gray;'>No prescriptions found.</p>";
    }
    ?>

    <br><br>
    
    <h3>2. Pathology Test Reports</h3>
    <?php
    $t_sql = "SELECT t.*, d.Full_Name as Doctor_Name 
              FROM pathology_tests t
              JOIN doctor d ON t.Doctor_NID = d.NID
              WHERE t.Patient_NID = '$patient_nid'
              ORDER BY t.Request_Date DESC";
    $t_result = mysqli_query($conn, $t_sql);

    if (mysqli_num_rows($t_result) > 0) {
        while ($row = mysqli_fetch_assoc($t_result)) {
            $status_color = ($row['Status'] == 'Completed') ? 'green' : 'orange';
            echo "<div style='background: #fafafa; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;'>";
            echo "<p><strong>Test Name:</strong> " . htmlspecialchars($row['Test_Name']) . "</p>";
            echo "<p><strong>Requested By:</strong> Dr. " . htmlspecialchars($row['Doctor_Name']) . " | <strong>Date:</strong> " . $row['Request_Date'] . "</p>";
            echo "<p><strong>Status:</strong> <span style='color: $status_color; font-weight: bold;'>" . $row['Status'] . "</span></p>";
            
            if ($row['Status'] == 'Completed') {
                echo "<div style='background: #fff; border: 1px solid #a5d6a7; padding: 10px; margin-top: 10px;'>";
                echo "<strong>Report Results / Findings:</strong><br>" . nl2br(htmlspecialchars($row['Test_Result']));
                echo "<br><small style='color:gray;'>Updated on: " . $row['Update_Date'] . "</small>";
                echo "</div>";
            }
            echo "</div>";
        }
    } else {
        echo "<p style='color: gray;'>No test records found.</p>";
    }
    ?>
</div>

<?php include '../includes/footer.php'; ?>