<?php
// mt/admin_dashboard.php

// ১. ডাটাবেজ ও ভেরিফিকেশন কমন গেটওয়ে ফাইল ইনক্লুড করা হলো
include 'admin_session_fix.php';

// ২. ইউআরএল প্যারামিটার সেট করা (যাতে অন্যান্য লিংকে ক্লিক করলেও view_id ডাটা লস্ট না হয়)
$url_param = isset($_GET['view_id']) ? "?view_id=" . urlencode($_GET['view_id']) : "";

// ৩. আপনার প্রজেক্টের আসল হেডার ফাইলগুলো ইনক্লুড করা হলো
include '../includes/header_link.php'; 


// ৪. ড্যাশবোর্ডের লাইভ কাউন্টারগুলোর জন্য ডাটাবেজ থেকে কাউন্ট আনা
$count_new      = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pathology_tickets WHERE Status='Pending'"));
$count_approved = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pathology_tickets WHERE Status='Approved'"));
$count_samples  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pathology_tickets WHERE Status='Collected'"));
$count_done     = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pathology_tickets WHERE Status='Completed'"));
?>

<div class="container mt-4">
    
    <?php if (isset($_GET['view_id'])): ?>
    <div class="alert alert-danger fw-bold shadow-sm rounded-3 mb-4">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> You are viewing this portal as an Administrator (Technologist Mode).
    </div>
    <?php endif; ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white p-4 rounded-4 shadow-sm border-start border-primary border-4 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Welcome back, Laboratory Specialist!</h3>
                    <p class="text-muted mb-0">
                        <i class="fa-solid fa-user-gear me-1 text-primary"></i> 
                        Active MT: <strong class="text-primary"><?php echo htmlspecialchars($user_name); ?></strong>
                        (ID: <strong class="text-secondary"><?php echo htmlspecialchars($mt_id); ?></strong>)
                    </p>
                </div>
                <div class="fs-1 text-secondary d-none d-md-block">
                    <i class="fa-solid fa-microscope text-primary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 menu-card p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-lab-amber">
                        <i class="fa-solid fa-bell-concierge"></i>
                    </div>
                    <span class="badge bg-warning text-dark fs-6"><?php echo $count_new; ?></span>
                </div>
                <h5 class="fw-bold mt-2 text-dark">New Requests</h5>
                <p class="text-muted small flex-grow-1">Pending test orders forwarded from doctor rooms waiting for approval.</p>
                <a href="check_test.php<?php echo $url_param; ?>" class="btn btn-sm btn-outline-warning w-100 mt-2 rounded-pill fw-bold">View Tickets</a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 menu-card p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-lab-blue">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <span class="badge bg-primary fs-6"><?php echo $count_approved; ?></span>
                </div>
                <h5 class="fw-bold mt-2 text-dark">Approved Queue</h5>
                <p class="text-muted small flex-grow-1">Tickets with assigned sample collection schedules.</p>
                <a href="approved_request.php<?php echo $url_param; ?>" class="btn btn-sm btn-outline-primary w-100 mt-2 rounded-pill fw-bold">Manage Queue</a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 menu-card p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-lab-teal">
                        <i class="fa-solid fa-vials"></i>
                    </div>
                    <span class="badge bg-info text-dark fs-6"><?php echo $count_samples; ?></span>
                </div>
                <h5 class="fw-bold mt-2 text-dark">Samples Collected</h5>
                <p class="text-muted small flex-grow-1">Biological samples collected and currently undergoing laboratory testing.</p>
                <a href="sample_collected.php<?php echo $url_param; ?>" class="btn btn-sm btn-outline-info w-100 mt-2 rounded-pill fw-bold text-dark">Track Testing</a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 menu-card p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-lab-green">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <span class="badge bg-success fs-6"><?php echo $count_done; ?></span>
                </div>
                <h5 class="fw-bold mt-2 text-dark">Completed Reports</h5>
                <p class="text-muted small flex-grow-1">Finished lab records and successfully uploaded diagnostic reports.</p>
                <a href="delivered_report.php<?php echo $url_param; ?>" class="btn btn-sm btn-outline-success w-100 mt-2 rounded-pill fw-bold">View History</a>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm rounded-4 p-4 bg-dark text-white mb-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-link me-2 text-warning"></i>Quick Laboratory Actions</h5>
                <div class="d-grid gap-2">
                    <a href="check_test.php<?php echo $url_param; ?>" class="btn btn-primary text-start"><i class="fa-solid fa-magnifying-glass-chart me-2"></i> Open Test Verification Room</a>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
// ৫. আপনার প্রজেক্টের আসল ফুটার ফাইল ইনক্লুড করা হলো
include '../includes/footer.php'; 
?>