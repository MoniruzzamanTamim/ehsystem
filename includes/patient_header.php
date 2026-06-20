<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal - E-Health System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%) !important; }
        .menu-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            background: #ffffff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .bg-patient-teal { background: rgba(23, 162, 184, 0.1); color: #17a2b8; }
        .bg-patient-blue { background: rgba(0, 123, 255, 0.1); color: #007bff; }
        .bg-patient-purple { background: rgba(111, 66, 193, 0.1); color: #6f42c1; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
            <i class="fa-solid fa-heart-pulse me-2"></i>E-HEALTH <span class="badge bg-warning text-dark ms-2">PATIENT</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#patientNavbar" aria-controls="patientNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="patientNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link text-white" href="../patient/dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../patient/medical_history.php">History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../patient/request_appointment.php">Appointment</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../patient/profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../patient/view_appointments.php">View Status</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-power-off me-1"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container">