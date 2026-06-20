<?php
// Session check included to ensure security
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['doctor'])) { header("Location: login.php"); exit(); }
$doc_id = $_SESSION['doctor'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Portal | Secured E-Health</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #0c59cf; /* Health Blue */
            --secondary-color: #00b4d8; /* Medical Cyan */
            --dark-bg: #1e293b;
            --light-bg: #f1f5f9;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--light-bg); }
        
        /* Navbar Design */
        .navbar-doctor {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 12px 0;
        }
        .nav-logo { font-family: 'Poppins', sans-serif; font-weight: 700; color: var(--primary-color); font-size: 24px; text-decoration: none; }
        .nav-logo span { color: var(--secondary-color); }
        
        /* Menu Links */
        .nav-link-custom {
            color: #475569;
            font-weight: 500;
            margin: 0 10px;
            transition: 0.3s;
        }
        .nav-link-custom:hover { color: var(--primary-color); }
        .active-link { color: var(--primary-color) !important; border-bottom: 2px solid var(--primary-color); }

        /* Profile Section */
        .profile-badge {
            background: var(--light-bg);
            padding: 5px 15px;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
        }

        /* Dashboard Cards */
        .menu-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
            background: white;
            overflow: hidden;
        }
        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(12, 89, 207, 0.15);
        }
        .icon-box {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 30px;
        }
        .bg-medical-blue { background: rgba(12, 89, 207, 0.1); color: var(--primary-color); }
        .bg-medical-teal { background: rgba(0, 180, 216, 0.1); color: var(--secondary-color); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-doctor sticky-top">
    <div class="container">
        <a class="nav-logo" href="dashboard.php">
            <i class="fa-solid fa-house-chimney-medical"></i> E-<span>HEALTH</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#doctorNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="doctorNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link nav-link-custom active-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="appointment_requests.php">Appointments</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="check_patient.php">Check Patients</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="appointment_history.php">Medical History</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="done_patient_list.php">Patient</a></li>
        
            </ul>
            
            <div class="d-flex align-items-center">
                <div class="profile-badge me-3 d-none d-md-block">
                    <small class="text-muted">Doctor ID:</small>
                    <span class="fw-bold text-dark"><?php echo $doc_id; ?></span>
                </div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-power-off me-1"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container py-5">