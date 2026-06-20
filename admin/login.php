<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../config/dbconnect.php';

if (isset($_POST['login'])) {
    // SQL Injection Protection
    $nid = mysqli_real_escape_string($conn, $_POST['nid']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Direct Query to central_admin table
    $sql = "SELECT * FROM `central_admin` WHERE `NID`='$nid' AND `Password`='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Setting Sessions for both admin roles to stay safe
        $_SESSION['admin'] = $nid;
        $_SESSION['central_admin'] = $nid;

        header("Location: dashboard.php");
        exit();
    }

    $msg = "Invalid Admin Credentials.";
}

include __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Admin - Secure Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(-45deg, #eef2ff, #f1f5f9, #e0e7ff, #f8fafc);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: #0f172a;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .wrapper {
            min-height: calc(100vh - 120px); /* Adjusting for header/footer space */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-glass-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.08);
            padding: 40px 36px;
        }

        .brand-title {
            margin: 0 0 8px;
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #1e293b;
            text-align: center;
        }

        .brand-subtitle {
            font-size: 0.95rem;
            color: #64748b;
            text-align: center;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569;
        }

        .form-group input {
            width: 100%;
            padding: 13px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 0.95rem;
            background: #f8fafc;
            color: #0f172a;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        .btn-submit {
            width: 100%;
            padding: 14px 16px;
            border: none;
            border-radius: 12px;
            background: #2563eb;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            margin-top: 8px;
        }

        .btn-submit:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-danger {
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            font-size: 0.9rem;
            text-align: center;
            font-weight: 500;
        }

        .action-links {
            margin-top: 24px;
            font-size: 0.9rem;
            color: #64748b;
            text-align: center;
        }

        .action-links a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
    <div class="wrapper">
        <div class="login-glass-card">
            <h2 class="brand-title"><i class="bi bi-shield-lock-fill text-primary"></i> Control Panel</h2>
            <p class="brand-subtitle">Central Admin Portal</p>
            
            <?php if (isset($msg)): ?>
                <div class="alert-danger"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="nid">National ID (NID)</label>
                    <input type="text" id="nid" name="nid" placeholder="Enter NID number" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" name="login" class="btn-submit">Sign In</button>
            </form>
            
            <div class="action-links">
                <span>Not registered yet? </span><a href="register.php">Create an account</a>
            </div>
        </div>
    </div>
   
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>