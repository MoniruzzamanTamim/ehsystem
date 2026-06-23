<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MT Portal - E-Health System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: linear-gradient(135deg, #4e54c8 0%, #2c3e50 100%) !important; }
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
        .bg-lab-amber { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
        .bg-lab-blue { background: rgba(0, 123, 255, 0.1); color: #007bff; }
        .bg-lab-teal { background: rgba(23, 162, 184, 0.1); color: #17a2b8; }
        .bg-lab-green { background: rgba(40, 167, 69, 0.1); color: #28a745; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
            <i class="fa-solid fa-flask-vial me-2"></i>E-HEALTH <span class="badge bg-light text-dark ms-2" style="font-weight: 600;">MT LAB</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mtNavbar" aria-controls="mtNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mtNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="check_test.php">Check Lab Tests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="approved_request.php">Approved </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="sample_collected.php">Sample Collected</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="delivered_report.php">Delivery</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small d-none d-md-inline">
                    <i class="fa-solid fa-user-circle me-1"></i> Active MT: <strong><?php echo htmlspecialchars($_SESSION['mt'] ?? ''); ?></strong>
                </span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 text-white border-white">
                    <i class="fa-solid fa-power-off me-1"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>