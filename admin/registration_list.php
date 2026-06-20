<?php
session_start();
if (!isset($_SESSION['central_admin']) && !isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}
include '../config/dbconnect.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Verification History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<?php include '../includes/header_link.php';?>

<main class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-secondary fw-bold mb-0"><i class="bi bi-journal-text"></i> Verification History List</h2>
        <a href="registration.php" class="btn btn-outline-primary shadow-sm"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white py-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-check-all"></i> Processed Accounts (Approved / Rejected)</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="historyTable" class="table table-striped table-bordered align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>Role</th>
                            <th>Name</th>
                            <th>NID</th>
                            <th>Reg Date</th>
                            <th>Status</th>
                            <th>Action / Change Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $history_query = "
                        SELECT 'Patient' as Role, Full_Name, NID, Reg_Date, Status, 'patient' as type FROM patient WHERE Status IN ('Approved', 'Rejected')
                        UNION ALL
                        SELECT 'Doctor' as Role, Full_Name, NID, Reg_Date, Status, 'doctor' as type FROM doctor WHERE Status IN ('Approved', 'Rejected')
                        UNION ALL
                        SELECT 'MT' as Role, Full_Name, NID, Reg_Date, Status, 'mt' as type FROM medical_technologist WHERE Status IN ('Approved', 'Rejected')
                        ORDER BY Reg_Date DESC";
                    
                    $history_res = mysqli_query($conn, $history_query);
                    while($row = mysqli_fetch_assoc($history_res)) { 
                        $status_badge = ($row['Status'] == 'Approved') ? 'bg-success' : 'bg-danger';
                        $nid_search = $row['NID'];
                        $t_name = $row['type'] == 'mt' ? 'medical_technologist' : $row['type'];
                        $full_info_res = mysqli_query($conn, "SELECT * FROM $t_name WHERE NID='$nid_search'");
                        $full_info = mysqli_fetch_assoc($full_info_res);
                        $full_info['Role'] = $row['Role'];
                    ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= $row['Role'] ?></span></td>
                            <td><strong><?= htmlspecialchars($row['Full_Name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['NID']) ?></td>
                            <td><?= date('d-M-Y h:i A', strtotime($row['Reg_Date'])) ?></td>
                            <td><span class="badge <?= $status_badge ?>"><?= $row['Status'] ?></span></td>
                            <td>
                                <button class="btn btn-xs btn-outline-info btn-sm view-user" data-info='<?= json_encode($full_info) ?>'><i class="bi bi-eye"></i> View Info</button>
                                
                                <?php if($row['Status'] == 'Approved'): ?>
                                    <a href="reject.php?NID=<?= urlencode($row['NID']) ?>&type=<?= $row['type'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Change status to Reject?')"><i class="bi bi-shield-x"></i> Reject Now</a>
                                <?php else: ?>
                                    <?php 
                                        $app_page = "approve.php";
                                        if($row['type'] == 'doctor') $app_page = "approve_doctor.php";
                                        if($row['type'] == 'mt') $app_page = "approve_mt.php";
                                    ?>
                                    <a href="<?= $app_page ?>?NID=<?= urlencode($row['NID']) ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-shield-check"></i> Approve Now</a>
                                <?php endif; ?>
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
                <h5 class="modal-title fw-bold" id="modalRole">User Profile Details</h5>
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
    $('#historyTable').DataTable({
        "order": [[ 3, "desc" ]],
        "pageLength": 10
    });

    $(document).on('click', '.view-user', function() {
        var data = $(this).data('info');
        var htmlContent = `
            <table class="table table-bordered table-striped">
                <tr><th>Role</th><td><span class="badge bg-primary text-white fw-bold">${data.Role}</span></td></tr>
                <tr><th>Full Name</th><td>${data.Full_Name || 'N/A'}</td></tr>
                <tr><th>NID Number</th><td>${data.NID}</td></tr>
                <tr><th>Phone</th><td>${data.Phone || 'N/A'}</td></tr>
                <tr><th>Email</th><td>${data.Email || 'N/A'}</td></tr>
                ${data.Specialization ? `<tr><th>Specialization</th><td>${data.Specialization}</td></tr>` : ''}
                ${data.Lab_Name ? `<tr><th>Lab Name</th><td>${data.Lab_Name}</td></tr>` : ''}
                <tr><th>Reg Date</th><td>${data.Reg_Date}</td></tr>
                <tr><th>Current Status</th><td><strong>${data.Status}</strong></td></tr>
            </table>
        `;
        
        $('#modalRole').html('<i class="bi bi-person-vcard"></i> ' + data.Role + ' Profile Details');
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
