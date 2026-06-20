<?php include '../includes/header.php'; ?>
<?php
include '../config/dbconnect.php';
$message = '';
$messageClass = '';
if (isset($_POST['register'])) {
    $nid = mysqli_real_escape_string($conn, $_POST['nid']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $lab = mysqli_real_escape_string($conn, $_POST['lab']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "INSERT INTO medical_technologist (NID, Full_Name, Lab_Name, Phone, Password, Reg_Date) VALUES ('$nid', '$name', '$lab', '$phone', '$password', NOW())";

    if (mysqli_query($conn, $sql)) {
        $message = 'Registration Successful! Waiting for Admin Approval.';
        $messageClass = 'success';
    } else {
        $message = 'Error: ' . mysqli_error($conn);
        $messageClass = 'error';
    }
}
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f3f6fb;
    color: #1f2937;
    margin: 0;
    padding: 0;
}
.mt-registration {
    max-width: 420px;
    margin: 48px auto;
    padding: 28px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
}
.mt-registration h2 {
    margin: 0 0 20px;
    font-size: 1.6rem;
    text-align: center;
    color: #0f172a;
}
.mt-registration .field {
    margin-bottom: 16px;
}
.mt-registration label {
    display: block;
    margin-bottom: 6px;
    font-size: 0.95rem;
    color: #334155;
}
.mt-registration input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    background: #f8fafc;
    color: #0f172a;
    font-size: 0.95rem;
    box-sizing: border-box;
}
.mt-registration input:focus {
    outline: none;
    border-color: #60a5fa;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
}
.mt-registration button {
    width: 100%;
    padding: 12px 14px;
    border: none;
    border-radius: 12px;
    background: #2563eb;
    color: #ffffff;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease;
}
.mt-registration button:hover {
    background: #1d4ed8;
}
.mt-registration .message {
    margin-bottom: 18px;
    padding: 14px 16px;
    border-radius: 12px;
    font-size: 0.95rem;
}
.mt-registration .success {
    background: #ecfdf5;
    color: #166534;
    border: 1px solid #bbf7d0;
}
.mt-registration .error {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}
</style>

<div class="mt-registration">
    <h2>Medical Technologist Registration</h2>
    <?php if ($message): ?>
        <div class="message <?php echo $messageClass; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="field">
            <label for="nid">NID</label>
            <input type="text" id="nid" name="nid" required>
        </div>
        <div class="field">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="field">
            <label for="lab">Lab/Hospital Name</label>
            <input type="text" id="lab" name="lab" required>
        </div>
        <div class="field">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" name="register">Register</button>
    </form>
    <div class="login-box">
                Already Have an Account?<br>
                <a href="../mt/login.php">Login  MT Account</a>
            </div>

            <p class="footer-note">Secured E-Health System | Laboratory Diagnostic Report Management</p>
</div>

<?php include '../includes/footer.php'; ?>