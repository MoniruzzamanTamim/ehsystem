<?php
// Temporarily suppress notices to avoid "session_start(): Ignoring session_start()"
$__old_error_reporting = error_reporting();
error_reporting($__old_error_reporting & ~E_NOTICE);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['central_admin']) && !isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include '../config/dbconnect.php';
include '../includes/header_link.php';
// restore previous error reporting level
error_reporting($__old_error_reporting);

// ডাটাবেজ থেকে মোট ইউজারের সংখ্যা গণনা (Total Counts)
$total_patients = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM patient WHERE Status='Approved'"));
$total_doctors = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM doctor WHERE Status='Approved'"));
$total_mts = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM medical_technologist WHERE Status='Approved'"));
?>

<div style="padding: 30px; max-width: 1200px; margin: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="margin: 0; font-size: 32px;">Dashboard</h1>
            <p style="margin: 5px 0 0; color: #555;">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin'] ?? 'Admin'); ?></strong></p>
        </div>
        <a href="logout.php" style="padding: 10px 18px; background: #f44336; color: #fff; text-decoration: none; border-radius: 5px;">Logout</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: #e3f2fd; padding: 24px; border-radius: 12px; border: 1px solid #90caf9;">
            <h3 style="margin-top: 0;">Approved Patients</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 15px 0 0;"><?php echo $total_patients; ?></p>
        </div>
        <div style="background: #e8f5e9; padding: 24px; border-radius: 12px; border: 1px solid #a5d6a7;">
            <h3 style="margin-top: 0;">Approved Doctors</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 15px 0 0;"><?php echo $total_doctors; ?></p>
        </div>
        <div style="background: #fff3e0; padding: 24px; border-radius: 12px; border: 1px solid #ffcc80;">
            <h3 style="margin-top: 0;">Approved MTs</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 15px 0 0;"><?php echo $total_mts; ?></p>
        </div>
    </div>

    <div style="background: #fafafa; padding: 24px; border-radius: 12px; border: 1px solid #ddd;">
        <h2 style="margin-top: 0;">Services</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <a href="dashboard.php" style="display: block; padding: 18px; background: #1976d2; color: #fff; text-decoration: none; border-radius: 10px; text-align: center; font-weight: bold;">Dashboard</a>
            <a href="approve_doctor.php" style="display: block; padding: 18px; background: #43a047; color: #fff; text-decoration: none; border-radius: 10px; text-align: center; font-weight: bold;">Approve Doctor</a>
            <a href="approve_patient.php" style="display: block; padding: 18px; background: #1e88e5; color: #fff; text-decoration: none; border-radius: 10px; text-align: center; font-weight: bold;">Approve Patient</a>
            <a href="approve_mt.php" style="display: block; padding: 18px; background: #fb8c00; color: #fff; text-decoration: none; border-radius: 10px; text-align: center; font-weight: bold;">Approve MT</a>
            <a href="manage_test.php" style="display: block; padding: 18px; background: #8e24aa; color: #fff; text-decoration: none; border-radius: 10px; text-align: center; font-weight: bold;">Manage Test</a>
            <a href="manage_user.php" style="display: block; padding: 18px; background: #6d4c41; color: #fff; text-decoration: none; border-radius: 10px; text-align: center; font-weight: bold;">Manage User</a>
            <a href="registration.php" style="display: block; padding: 18px; background: #546e7a; color: #fff; text-decoration: none; border-radius: 10px; text-align: center; font-weight: bold;">Registration</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>