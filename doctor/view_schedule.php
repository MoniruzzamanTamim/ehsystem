<?php
session_start();
include '../config/dbconnect.php';

if (!isset($_SESSION['doctor'])) {
    header("Location: login.php");
    exit();
}

$doctor_nid = $_SESSION['doctor'];

$query = "SELECT a.*, p.Full_Name AS Patient_Name 
          FROM appointments a 
          JOIN patient p ON a.Patient_NID = p.NID 
          WHERE a.Doctor_NID = '$doctor_nid' AND a.Status = 'Accepted' 
          ORDER BY a.Appointment_Time ASC, a.Serial_No ASC";
$result = mysqli_query($conn, $query);
?>

<h2>Accepted Appointment Schedule</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Serial No</th>
        <th>Appointment Time</th>
        <th>Patient Name</th>
        <th>Patient NID</th>
        <th>Reason</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><strong><?php echo $row['Serial_No']; ?></strong></td>
            <td><?php echo $row['Appointment_Time']; ?></td>
            <td><?php echo $row['Patient_Name']; ?></td>
            <td><?php echo $row['Patient_NID']; ?></td>
            <td><?php echo $row['Reason']; ?></td>
        </tr>
    <?php } ?>
</table>
<br>
<a href="dashboard.php">Back to Dashboard</a>