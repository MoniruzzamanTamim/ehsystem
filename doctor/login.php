<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../config/dbconnect.php';

if (isset($_POST['login'])) {
    $nid = mysqli_real_escape_string($conn, $_POST['nid']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Direct Query to doctor table
    $sql = "SELECT * FROM `doctor` WHERE `NID`='$nid' AND `Password`='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Status Check
        if ($row['Status'] === 'Approved') {
            $_SESSION['doctor'] = $nid;
            header("Location: dashboard.php");
            exit();
        } else {
            $msg = "Your account is not approved yet.";
        }
    } else {
        $msg = "Invalid NID or Password.";
    }
}
?>
<?php include '../includes/header.php'; ?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #eef2ff;
    margin: 0;
    padding: 0;
}
.page-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}
.main-content {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
}
.login-card {
    width: 100%;
    max-width: 420px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
    padding: 36px 32px;
}
.login-card h2 {
    margin: 0 0 14px;
    font-size: 28px;
    color: #111827;
}
.login-card p.lead {
    margin: 0 0 24px;
    color: #4b5563;
    line-height: 1.5;
}
.form-group {
    margin-bottom: 18px;
}
.form-group label {
    display: block;
    margin-bottom: 10px;
    font-size: 14px;
    color: #374151;
    font-weight: 500;
}
.form-group input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    font-size: 15px;
    background: #f9fafb;
    color: #111827;
    box-sizing: border-box;
    transition: all 0.2s ease;
}
.form-group input:focus {
    border-color: #2563eb;
    background: #fff;
    outline: none;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}
button[type="submit"] {
    width: 100%;
    padding: 14px 16px;
    border: none;
    border-radius: 12px;
    background: #2563eb;
    color: #ffffff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease;
    margin-top: 10px;
}
button[type="submit"]:hover {
    background: #1d4ed8;
}
.footer-note {
    margin-top: 20px;
    text-align: center;
    font-size: 14px;
    color: #4b5563;
}
.footer-note a {
    color: #2563eb;
    text-decoration: none;
    font-weight: 500;
}
.footer-note a:hover {
    text-decoration: underline;
}
.message {
    margin-top: 22px;
    padding: 14px 16px;
    border-radius: 12px;
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
    font-size: 14px;
    text-align: center;
}
</style>

<div class="page-container">
    <main class="main-content">
        <section class="login-card">
            <h2>Doctor Sign In</h2>
            <p class="lead">Use your NID and password to access the doctor dashboard.</p>

            <form method="POST">
                <div class="form-group">
                    <label for="nid">NID</label>
                    <input id="nid" type="text" name="nid" placeholder="Enter your NID" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" name="login">Login</button>
            </form>

            <p class="footer-note">Don't have an account? <a href="../doctor/doctor_registration.php">Sign Up</a></p>

            <?php if (isset($msg)): ?>
                <div class="message"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
        </section>
    </main>
</div>

<?php include '../includes/footer.php'; ?>