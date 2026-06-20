<?php
session_start();
include '../config/dbconnect.php';

if (!isset($_SESSION['doctor'])) {
    exit("Unauthorized Access");
}

$doctor_nid = $_SESSION['doctor'];
$today = date('Y-m-d');

$sql = "SELECT q.*, p.Full_Name FROM patient_queue q 
        JOIN patient p ON q.Patient_NID = p.NID 
        WHERE q.Doctor_NID = '$doctor_nid' AND q.Queue_Date = '$today' AND q.Status != 'Done'
        ORDER BY q.Serial_No ASC";
        

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='10' width='100%'>
            <tr>
                <th>Serial No</th>
                <th>Patient Name</th>
                <th>NID</th>
                <th>Action</th>
            </tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>" . $row['Serial_No'] . "</td>
                <td>" . $row['Full_Name'] . "</td>
                <td>" . $row['Patient_NID'] . "</td>
                <td><a href='prescription.php?nid=" . $row['Patient_NID'] . "'>Write Prescription</a></td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No patients in the queue for today.</p>";
}
?>