<?php
// ১. যেকোনো এক্সট্রা আউটপুট বন্ধ করতে এবং সেশন স্টার্ট করতে সবার উপরে রাখুন
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ডাটাবেজ কানেকশন
include '../config/dbconnect.php';

// ২. 🔒 AJAX হ্যান্ডেলার (এটি একদম উপরেই থাকতে হবে, এর আগে কোনো HTML বা include থাকা যাবে না)
if (isset($_POST['action']) && $_POST['action'] == 'generate_token' && isset($_POST['nid'])) {
    error_reporting(0);
    ini_set('display_errors', 0);
    header('Content-Type: application/json; charset=utf-8');

    if (!$conn) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
        exit();
    }

    $nid = mysqli_real_escape_string($conn, $_POST['nid']);
    $token = bin2hex(random_bytes(16));
    
    // মেডিকেল টেকনোলজিস্ট টেবিলে 'login_token' কলামটি থাকতে হবে অটো-লগইনের জন্য
    $update_query = "UPDATE medical_technologist SET login_token='$token' WHERE NID='$nid'";
    
    if (mysqli_query($conn, $update_query)) {
        echo json_encode(['status' => 'success', 'token' => $token]);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit();
}

// অ্যাডমিন লগইন প্রোটেকশন
if (!isset($_SESSION['central_admin']) && !isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

// রিজেক্ট লজিক
if (isset($_GET['action']) && $_GET['action'] == 'reject' && isset($_GET['NID'])) {
    $nid = mysqli_real_escape_string($conn, $_GET['NID']);
    $update_query = "UPDATE medical_technologist SET Status='Rejected' WHERE NID='$nid'";
    if (mysqli_query($conn, $update_query)) {
        header("Location: managed_mt.php?status=rejected");
        exit();
    }
}

// শুধুমাত্র 'Approved' মেডিকেল টেকনোলজিস্টদের ডাটা তুলে আনা
$query = "SELECT * FROM medical_technologist WHERE Status='Approved' ORDER BY Reg_Date DESC";
$result = mysqli_query($conn, $query);

include '../includes/header_link.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Managed Approved MT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<main class="container my-4">
    
    <?php if (isset($_GET['status']) && $_GET['status'] == 'rejected'): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> Technologist Status Updated to Rejected & Removed from Approved List!
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-secondary fw-bold mb-0"><i class="bi bi-shield-plus text-success"></i> Approved Medical Technologist Management</h2>
        <a href="pending_approved_request.php" class="btn btn-dark shadow-sm"><i class="bi bi-arrow-left"></i> Back to Requests</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white py-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-check-circle-fill"></i> Active & Verified Technologists</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle dynamicTable w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>NID</th>
                            <th>Lab Name</th>
                            <th>Phone</th>
                            <th>Reg Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { $row['Role'] = 'Medical Technologist'; ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['Full_Name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['NID']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['Lab_Name'] ?: 'N/A') ?></span></td>
                            <td><?= htmlspecialchars($row['Phone']) ?></td>
                            <td><?= date('d-M-Y', strtotime($row['Reg_Date'])) ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary me-1 autologin-btn" data-nid="<?= htmlspecialchars($row['NID']) ?>">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </button>
                                
                                <button class="btn btn-sm btn-info text-white view-user me-1" data-info='<?= json_encode($row) ?>'>
                                    <i class="bi bi-eye"></i> View
                                </button>
                                
                                <a href="managed_mt.php?action=reject&NID=<?= urlencode($row['NID']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to reject this technologist?')">
                                    <i class="bi bi-slash-circle"></i> Reject
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="userViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold" id="modalRole"><i class="bi bi-person-vcard"></i> Technologist Profile Details</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeModal()"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('.dynamicTable').DataTable({
        "pageLength": 10,
        "language": { "emptyTable": "No approved medical technologists found." }
    });

    // ওয়ান-টাইম সিকিউর ইনস্ট্যান্ট অটো লগইন লজিক
    $(document).on('click', '.autologin-btn', function() {
        var mtNid = $(this).data('nid');
        var clickedBtn = $(this);
        
        clickedBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Connecting...');

        $.ajax({
            url: 'managed_mts.php',
            type: 'POST',
            data: { action: 'generate_token', nid: mtNid },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    // admin_autologin.php ফাইলে রিডাইরেক্ট করবে mt রোল নিয়ে
                    var loginUrl = "admin_autologin.php?role=mt&NID=" + encodeURIComponent(mtNid) + "&token=" + res.token;
                    window.open(loginUrl, '_blank');
                } else {
                    alert('Error: ' + res.message);
                }
                clickedBtn.prop('disabled', false).html('<i class="bi bi-box-arrow-in-right"></i> Login');
            },
            error: function(xhr, status, error) {
                alert('Server side issue! Please try again.');
                clickedBtn.prop('disabled', false).html('<i class="bi bi-box-arrow-in-right"></i> Login');
            }
        });
    });

    // টেকনোলজিস্টের ডাটা মডালে দেখানোর লজিক
    $(document).on('click', '.view-user', function() {
        var data = $(this).data('info');
        var htmlContent = `
            <table class="table table-bordered table-striped">
                <tr><th style="width: 35%;">Full Name</th><td>${data.Full_Name || 'N/A'}</td></tr>
                <tr><th>NID Number</th><td>${data.NID}</td></tr>
                <tr><th>Lab / Institution</th><td><span class="badge bg-secondary">${data.Lab_Name || 'N/A'}</span></td></tr>
                <tr><th>Phone</th><td>${data.Phone || 'N/A'}</td></tr>
                <tr><th>Reg Date</th><td>${data.Reg_Date}</td></tr>
                <tr><th>Current Status</th><td><span class="badge bg-success">${data.Status}</span></td></tr>
            </table>`;
        $('#modalBody').html(htmlContent);
        $('#userViewModal').modal('show');
    });
});

function closeModal(){
    $('#userViewModal').modal('hide');
}
</script>
</body>
</html>