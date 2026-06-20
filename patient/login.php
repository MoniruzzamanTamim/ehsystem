<?php
session_start();
include '../config/dbconnect.php'; // পাথ চেক করে নিও, রুট ফোল্ডারে হলে 'config/dbconnect.php' হবে

$msg = "";

if (isset($_POST['login'])) {
    $nid = mysqli_real_escape_string($conn, $_POST['nid']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM patient WHERE NID='$nid' AND Password='$password' AND Status='Approved'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['nid'] = $nid;
        header("Location: dashboard.php");
        exit();
    } else {
        $msg = "Invalid Patient NID/Password or Account Not Approved Yet.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login - EH System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: #ffffff;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #17a2b8 0%, #00838f 100%);
            padding: 30px;
            color: white;
            text-align: center;
        }
        .btn-login {
            background: linear-gradient(135deg, #17a2b8 0%, #00838f 100%);
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        }
        .form-control {
            border-radius: 0 10px 10px 0;
            padding: 12px 15px;
        }
        .input-group-text {
            border-radius: 10px 0 0 10px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<section>
    <div class="container mt-5">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-5 col-md-7">
                <div class="card login-card">
                    <div class="login-header">
                        <h3 class="fw-bold mb-1"><i class="fa-solid fa-hospital-user me-2"></i>Patient Portal</h3>
                        <p class="small mb-0 text-white-50">Access your digital health record & prescriptions</p>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        
                        <?php if(!empty($msg)): ?>
                            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $msg; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">Patient NID</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-id-card text-secondary"></i></span>
                                    <input type="text" name="nid" class="form-control" placeholder="Enter your NID number" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-muted">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock text-secondary"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                </div>
                            </div>

                            <button type="submit" name="login" class="btn btn-primary btn-login w-100 text-white mb-3">
                                Patient Login <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
                            </button>
                            
                            <div class="border-top pt-3 mt-3 text-center">
                                <p class="small text-muted mb-2 fw-semibold">New to EH System?</p>
                                <div class="d-grid">
                                    <a href="patient_registration.php" class="btn btn-outline-info btn-sm rounded-pill text-dark fw-bold">
                                        <i class="fa-solid fa-user-plus me-1"></i> Create Patient Account
                                    </a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
