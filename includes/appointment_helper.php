<?php

function ensure_appointment_table($conn)
{
    $sql = "
        CREATE TABLE IF NOT EXISTS doctor_appointments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            Patient_NID VARCHAR(50) NOT NULL,
            Doctor_NID VARCHAR(50) NOT NULL,
            Preferred_Date DATE NULL,
            Reason TEXT NULL,
            Appointment_Date DATE NULL,
            Appointment_Time TIME NULL,
            Serial_No INT NULL,
            Status VARCHAR(20) NOT NULL DEFAULT 'Pending',
            Request_Date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            Response_Date DATETIME NULL,
            INDEX idx_patient (Patient_NID),
            INDEX idx_doctor (Doctor_NID),
            INDEX idx_status (Status),
            INDEX idx_schedule (Doctor_NID, Appointment_Date, Serial_No)
        )
    ";

    mysqli_query($conn, $sql);
}

function next_appointment_serial($conn, $doctor_nid, $appointment_date)
{
    $stmt = mysqli_prepare(
        $conn,
        "SELECT COALESCE(MAX(Serial_No), 0) + 1 AS next_serial
         FROM doctor_appointments
         WHERE Doctor_NID = ? AND Appointment_Date = ? AND Status = 'Accepted'"
    );
    mysqli_stmt_bind_param($stmt, "ss", $doctor_nid, $appointment_date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return (int)($row['next_serial'] ?? 1);
}

?>
