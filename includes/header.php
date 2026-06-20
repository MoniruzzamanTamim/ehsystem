<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secured E-Health System</title>
    <link rel="stylesheet" href="http://localhost/ehsystem/css/style.css">
    <link rel="stylesheet" href="http://localhost/ehsystem/css/header.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="http://localhost/ehsystem/js/all_js.js"></script>

    
</head>
<body>

    <header class="main-header">
        <div class="brand-area">
        <a class="nav-logo" href="dashboard.php">
            <i class="fa-solid fa-house-chimney-medical"></i>
            <span> Secured E-Health System</span>
        </a>
    
                
        </div>
        <div class="main-header-right">
         <p> Opening Hours : Monday to Saturday - 8am to 9pm </p>
         <p> Contact : +8801739820399</p>

        </div>
    </header>

    <nav class="top-nav second-menu">

        <div class="logo-section">
         <a class="nav-logo" href="../../ehsystem/index.php">
            <i class="fa-solid fa-house-chimney-medical"></i> E-<span>HEALTH</span>
        </a>
        </div>
        <div class="nav-links header-menu">
            <a href="http://localhost/ehsystem/index.php">🏠 Home</a>
            <a href="http://localhost/ehsystem/patient/login.php">🏠 Patient</a>
            <a href="http://localhost/ehsystem/doctor/login.php">🏠 Doctor</a>
            <a href="http://localhost/ehsystem/mt/login.php">🏠 MT</a>
            <a href="http://localhost/ehsystem/admin/login.php">🏠 Admin</a>

            
        </div>
    </nav>

