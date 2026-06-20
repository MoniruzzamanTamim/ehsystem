<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['admin'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | eHealth System</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body{
            background:#f5f6fa;
        }

        .navbar{
            background:#0d6efd;
        }

        .navbar-brand{
            font-weight:bold;
            color:#fff !important;
        }

        .nav-link{
            color:#fff !important;
            margin-right:10px;
        }

        .nav-link:hover{
            color:#ffc107 !important;
        }
    </style>

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow">

    <div class="container-fluid">

        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-hospital"></i>
            eHealth Admin
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="allPortalDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        All Portal
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="allPortalDropdown">
                        <li>
                            <a class="dropdown-item" href="managed_doctors.php">
                                <i class="bi bi-person-badge"></i>
                                Doctors
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="managed_patients.php">
                                <i class="bi bi-people"></i>
                                Patients
                            </a>
                        </li>
                        <li>
    
                            <a class="dropdown-item" href="managed_mts.php">
                                <i class="bi bi-heart-pulse"></i>
                                Medical Technologists
                            </a>
                        </li>
                    </ul>
                </li>


                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="testDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-file-medical"></i>
                        Test
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="testDropdown">
                        <li>
                            <a class="dropdown-item" href="manage_tests.php">
                                <i class="bi bi-plus-circle"></i>
                                Add Test
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="manage_tests.php">
                                <i class="bi bi-shuffle"></i>
                                Assign MT
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pending_aproved_request.php">
                        <i class="bi bi-file-medical"></i>
                       Pending 
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="registration_list.php">
                        <i class="bi bi-file-medical"></i>
                       List 
                    </a>
                </li>

            </ul>

            <ul class="navbar-nav">

                <li class="nav-item">
                    <span class="nav-link">
                        <i class="bi bi-person-circle"></i>
                        <?php echo htmlspecialchars($admin_name); ?>
                    </span>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-warning" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>

<div class="container mt-4">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>