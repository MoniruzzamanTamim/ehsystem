<?php
session_start();
include '../config/dbconnect.php';

if (isset($_SESSION['mt'])) {
    header("Location: dashboard.php");
    exit();
}

$msg = "";

if (isset($_POST['login'])) {
    $nid = mysqli_real_escape_string($conn, $_POST['nid']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM medical_technologist WHERE NID='$nid' AND Password='$password' AND Status='Approved'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['mt'] = $nid;
        header("Location: dashboard.php");
        exit();
    } else {
        $msg = "Invalid MT NID/password or account is not approved yet.";
    }
}

include '../includes/header.php';
?>

<style>
    body {
        background:
            radial-gradient(circle at 18% 18%, rgba(30, 136, 229, 0.12), transparent 28%),
            radial-gradient(circle at 86% 12%, rgba(0, 150, 136, 0.10), transparent 30%),
            linear-gradient(135deg, #f4f8fb 0%, #e8eef5 100%);
    }

    .mt-login-page,
    .mt-login-page * {
        box-sizing: border-box;
    }

    .mt-login-page {
        width: 100%;
        min-height: 440px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px 0;
        font-family: Arial, Helvetica, sans-serif;
        color: #182033;
    }

    .login-shell {
        width: min(1040px, 100%);
        min-height: 420px;
        display: grid;
        grid-template-columns: 1fr 1.08fr;
        background: #ffffff;
        border: 1px solid rgba(24, 32, 51, 0.08);
        border-radius: 8px;
        box-shadow: 0 24px 70px rgba(27, 45, 75, 0.14);
        overflow: hidden;
    }

    .brand-panel {
        padding: 36px 28px;
        color: #ffffff;
        background:
            linear-gradient(rgba(15, 37, 70, 0.86), rgba(15, 37, 70, 0.88)),
            url("../images/slider1.png") center/cover no-repeat;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .brand-mark {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        letter-spacing: 0.2px;
    }

    .brand-icon,
    .stat-icon,
    .input-icon {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.14);
        font-weight: 700;
    }

    .brand-panel h1 {
        margin: 56px 0 14px;
        font-size: 34px;
        line-height: 1.18;
        letter-spacing: 0;
    }

    .brand-panel p {
        max-width: 420px;
        margin: 0;
        color: rgba(255, 255, 255, 0.78);
        line-height: 1.7;
        font-size: 15px;
    }

    .stat-list {
        display: grid;
        gap: 16px;
        margin-top: 42px;
    }

    .stat-item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .stat-item strong {
        display: block;
        margin-bottom: 4px;
        font-size: 14px;
    }

    .stat-item span {
        color: rgba(255, 255, 255, 0.72);
        font-size: 13px;
        line-height: 1.5;
    }

    .login-panel {
        padding: 54px 56px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .kicker {
        color: #1e88e5;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .login-panel h2 {
        margin: 0 0 10px;
        color: #182033;
        font-size: 30px;
        line-height: 1.2;
    }

    .login-panel .intro {
        margin: 0 0 30px;
        color: #66758a;
        line-height: 1.6;
        font-size: 14px;
    }

    .alert {
        background: #fff1f1;
        color: #b42318;
        border: 1px solid #ffd2cf;
        padding: 12px 14px;
        border-radius: 6px;
        margin-bottom: 18px;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .mt-login-page label {
        display: block;
        color: #344054;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .input-wrap {
        display: flex;
        align-items: center;
        border: 1px solid #d8e0ea;
        border-radius: 7px;
        background: #ffffff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .input-wrap:focus-within {
        border-color: #1e88e5;
        box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.12);
    }

    .input-icon {
        flex: 0 0 48px;
        height: 48px;
        border-radius: 0;
        background: #f4f7fb;
        color: #526375;
    }

    .mt-login-page input {
        width: 100%;
        height: 48px;
        border: 0;
        outline: 0;
        padding: 0 14px;
        font-size: 15px;
        color: #182033;
        background: transparent;
    }

    .form-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 2px 0 24px;
        color: #66758a;
        font-size: 13px;
    }

    .btn-primary {
        width: 100%;
        border: 0;
        border-radius: 7px;
        background: #1e88e5;
        color: #ffffff;
        padding: 14px 16px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-primary:hover {
        background: #166fbd;
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(30, 136, 229, 0.22);
    }

    .register-box {
        margin-top: 24px;
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        background: #f8fafc;
        text-align: center;
        color: #66758a;
        font-size: 14px;
    }

    .register-box a {
        display: inline-block;
        margin-top: 8px;
        color: #1e88e5;
        font-weight: 700;
        text-decoration: none;
    }

    .footer-note {
        margin-top: 28px;
        color: #8a98aa;
        font-size: 12px;
        line-height: 1.5;
        text-align: center;
    }

    @media (max-width: 840px) {
        .mt-login-page {
            align-items: flex-start;
            padding: 18px 0;
        }

        .login-shell {
            grid-template-columns: 1fr;
            min-height: 0;
        }

        .brand-panel {
            min-height: 320px;
            padding: 34px 28px;
        }

        .brand-panel h1 {
            margin-top: 34px;
            font-size: 28px;
        }

        .login-panel {
            padding: 34px 28px;
        }
    }
</style>

<div class="mt-login-page">
    <main class="login-shell">
        <section class="brand-panel">
            <div>
                <div class="brand-mark">
                    <span class="brand-icon">MT</span>
                    <span>Secured E-Health Lab</span>
                </div>

                <h1>Laboratory access for verified technicians.</h1>
                <p>Review pending diagnostic requests, submit lab findings, and keep patient reports moving through the hospital workflow.</p>
            </div>

            <div class="stat-list">
                <div class="stat-item">
                    <span class="stat-icon">01</span>
                    <div>
                        <strong>Approved users only</strong>
                        <span>Login access is limited to admin-verified medical technologists.</span>
                    </div>
                </div>
                <div class="stat-item">
                    <span class="stat-icon">02</span>
                    <div>
                        <strong>Fast report updates</strong>
                        <span>Submit pathology findings directly to patient medical records.</span>
                    </div>
                </div>
                <div class="stat-item">
                    <span class="stat-icon">03</span>
                    <div>
                        <strong>Connected care</strong>
                        <span>Doctors and patients can follow completed diagnostic results.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="login-panel">
            <div class="kicker">MT Portal</div>
            <h2>Sign in to your lab workspace</h2>
            <p class="intro">Enter your registered NID and password to access pending pathology tests and report submission tools.</p>

            <?php if (!empty($msg)): ?>
                <div class="alert">
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="nid">Technician NID</label>
                    <div class="input-wrap">
                        <span class="input-icon">ID</span>
                        <input type="text" id="nid" name="nid" placeholder="Enter your registered NID" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">PW</span>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="form-row">
                    <span>Account must be approved by admin.</span>
                    <span>Secure session</span>
                </div>

                <button type="submit" name="login" class="btn-primary">Sign In</button>
            </form>

            <div class="register-box">
                Need access to the MT portal?<br>
                <a href="../mt/mt_registration.php">Apply for MT Account</a>
            </div>

            <p class="footer-note">Secured E-Health System | Laboratory Diagnostic Report Management</p>
        </section>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
