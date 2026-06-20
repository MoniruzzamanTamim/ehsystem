<?php
include '../config/dbconnect.php';

if(isset($_POST['register']))
{
    $nid = $_POST['nid'];
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    mysqli_query(
        $conn,
        "INSERT INTO doctor
        (
            NID,
            Full_Name,
            Specialization,
            Phone,
            Email,
            Password,
            Reg_Date
        )
        VALUES
        (
            '$nid',
            '$name',
            '$specialization',
            '$phone',
            '$email',
            '$password',
            NOW()
        )"
    );

    echo "Doctor Registration Successful";
}
?>

<h2>Doctor Registration</h2>

<form method="POST">

NID:
<input type="text" name="nid" required>

<br><br>

Full Name:
<input type="text" name="name" required>

<br><br>

Specialization:
<input type="text" name="specialization" required>

<br><br>

Phone:
<input type="text" name="phone">

<br><br>

Email:
<input type="email" name="email">

<br><br>

Password:
<input type="password" name="password" required>

<br><br>

<button name="register">
Register
</button>

</form>
