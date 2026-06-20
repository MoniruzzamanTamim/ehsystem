<?php
include '../config/dbconnect.php';
$ticket_no = mysqli_real_escape_string($conn, $_POST['ticket_no']);

// 1. Path structure: uploads/reports/TICKET_NO/
$target_dir = "../uploads/reports/" . $ticket_no . "/";


// 2. Folder na thakle create koro (Recursive)
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$uploaded_files = [];
if (!empty($_FILES['pic_file']['name'][0])) {
    foreach ($_FILES['pic_file']['tmp_name'] as $key => $tmp_name) {
        if (!empty($_FILES['pic_file']['name'][$key])) {
            $filename = time() . '_' . basename($_FILES['pic_file']['name'][$key]);
            
            // File move koro
            if (move_uploaded_file($tmp_name, $target_dir . $filename)) {
                // Database e path save hobe: TICKET_NO/filename
                $uploaded_files[] = [
                    'name' => mysqli_real_escape_string($conn, $_POST['pic_name'][$key]), 
                    'file' => $ticket_no . '/' . $filename
                ];
            }
        }
    }
}

// JSON format e data update koro
$json_data = json_encode($uploaded_files);
$text_report = mysqli_real_escape_string($conn, $_POST['text_report']);

$query = "UPDATE pathology_tickets SET 
          report_data='$json_data', 
          report_text_note='$text_report', 
          status='Completed' 
          WHERE ticket_no='$ticket_no'";

if(mysqli_query($conn, $query)) {
    echo "<script>alert('Report Delivered Successfully!'); window.location='delivered_report.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>