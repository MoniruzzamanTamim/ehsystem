<?php
session_start();
include '../config/dbconnect.php';

if (!isset($_SESSION['doctor'])) {
    exit("Unauthorized Access");
}

$patient_nid = isset($_GET['nid']) ? mysqli_real_escape_length($conn, $_GET['nid']) : '';
$patient_nid = $_GET['nid'];

// প্রথমে পেশেন্টের বেসিক ইনফো চেক করি
$patient_query = mysqli_query($conn, "SELECT Full_Name FROM patient WHERE NID = '$patient_nid'");
$patient_data = mysqli_fetch_assoc($patient_query);

if (!$patient_data) {
    echo "<p style='color: red; font-weight: bold;'>No patient found with this NID.</p>";
    exit();
}

echo "<h3>Medical Records for: " . htmlspecialchars($patient_data['Full_Name']) . " (NID: $patient_nid)</h3>";

// প্রেসক্রিপশন হিস্ট্রি কুয়েরি
$sql = "SELECT pr.*, d.Full_Name as Doctor_Name, d.Specialization 
        FROM prescription pr
        JOIN doctor d ON pr.Doctor_NID = d.NID
        WHERE pr.Patient_NID = '$patient_nid'
        ORDER BY pr.Prescription_Date DESC";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div style='background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;'>";
        echo "<p><strong>Date:</strong> " . $row['Prescription_Date'] . "</p>";
        echo "<p><strong>Doctor:</strong> Dr. " . htmlspecialchars($row['Doctor_Name']) . " (" . htmlspecialchars($row['Specialization']) . ")</p>";
        echo "<hr>";
        echo "<p><strong>Symptoms:</strong><br>" . nl2br(htmlspecialchars($row['Symptoms'])) . "</p>";
        echo "<p><strong>Diagnosis:</strong><br>" . nl2br(htmlspecialchars($row['Diagnosis'])) . "</p>";
        echo "<p><strong>Medicines & Advice:</strong><br style='color: green;'>" . nl2br(htmlspecialchars($row['Medicines'])) . "</p>";
        echo "</div>";
    }
} else {
    echo "<p style='color: orange;'>No previous prescriptions found for this patient.</p>";
}
?>