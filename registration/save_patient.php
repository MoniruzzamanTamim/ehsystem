<?php

include '../config/dbconnect.php';

$nid = $_POST['nid'];
$fullname = $_POST['fullname'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$password = $_POST['password'];
$portal = isset($_POST['portal']) ? preg_replace('/[^a-z0-9_-]/i', '', $_POST['portal']) : '';

$sql = "
INSERT INTO patient
(
    NID,
    Full_Name,
    Phone,
    Email,
    Password,
    Reg_Date
)
VALUES
(
    '$nid',
    '$fullname',
    '$phone',
    '$email',
    '$password',
    NOW()
)
";

if (mysqli_query($conn, $sql))
{
    $loginPage = '../login.php';
    if ($portal !== '')
    {
        $loginPage = "../{$portal}/login.php";
    }
    header("Location: $loginPage");
    exit;
}
else
{
    echo mysqli_error($conn);
}