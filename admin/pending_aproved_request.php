<?php
session_start();
if (!isset($_SESSION['central_admin']) && !isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}
include '../config/dbconnect.php';
include '../includes/header_link.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Registration Control Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .nav-tabs .nav-link { color: #495057; font-weight: 600; }
        .nav-tabs .nav-link.active { background-color: #fff; border-bottom-color: transparent; color: #0d6efd; }
    </style>
</head>
<body>

<main class="container my-4">
    
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> Successfully Approved!<button class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php elseif ($_GET['status'] == 'rejected'): ?>
            <div class="alert alert-warning alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill"></i> Request Status Updated to Rejected!<button class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-secondary fw-bold mb-0"><i class="bi bi-shield-check"></i> Registration Dashboard</h2>
        <a href="registration_list.php" class="btn btn-dark shadow-sm"><i class="bi bi-journal-text"></i> View Verification History</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-3 bg-white border-start border-primary border-4">
                <div class="d-flex align-items-center">
                    <div class="fs-1 text-primary me-3"><i class="bi bi-people"></i></div>
                    <div>
                        <?php $p_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM patient WHERE Status='Pending'")); ?>
                        <h6 class="text-muted mb-1">Pending Patients</h6>
                        <h3 class="fw-bold mb-0"><?= $p_count['total'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 bg-white border-start border-success border-4">
                <div class="d-flex align-items-center">
                    <div class="fs-1 text-success me-3"><i class="bi bi-person-badge"></i></div>
                    <div>
                        <?php $d_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM doctor WHERE Status='Pending'")); ?>
                        <h6 class="text-muted mb-1">Pending Doctors</h6>
                        <h3 class="fw-bold mb-0"><?= $d_count['total'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 bg-white border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <div class="fs-1 text-warning me-3"><i class="bi bi-heart-pulse"></i></div>
                    <div>
                        <?php $m_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM medical_technologist WHERE Status='Pending'")); ?>
                        <h6 class="text-muted mb-1">Pending MTs</h6>
                        <h3 class="fw-bold mb-0"><?= $m_count['total'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header bg-primary text-white py-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-clock-history"></i> Pending Approval Requests</h4>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="pendingTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="patient-tab" data-bs-toggle="tab" data-bs-target="#patient-pending" type="button" role="tab"><i class="bi bi-people"></i> Patients</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="doctor-tab" data-bs-toggle="tab" data-bs-target="#doctor-pending" type="button" role="tab"><i class="bi bi-person-badge"></i> Doctors</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="mt-tab" data-bs-toggle="tab" data-bs-target="#mt-pending" type="button" role="tab"><i class="bi bi-heart-pulse"></i> Medical Technologists</button>
                </li>
            </ul>

            <div class="tab-content p-3 border border-top-0 bg-white rounded-bottom" id="pendingTabsContent">
                
                <div class="tab-pane fade show active" id="patient-pending" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle dynamicTable w-100">
                            <thead class="table-light"><tr><th>Name</th><th>NID</th><th>Reg Date</th><th>Actions</th></tr></thead>
                            <tbody>
                            <?php
                            $res = mysqli_query($conn, "SELECT * FROM patient WHERE Status='Pending' ORDER BY Reg_Date DESC");
                            while($row = mysqli_fetch_assoc($res)) { 
                                $row['Role'] = 'Patient'; ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['Full_Name']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['NID']) ?></td>
                                    <td><?= date('d-M-Y h:i A', strtotime($row['Reg_Date'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white view-user" data-info='<?= json_encode($row) ?>'><i class="bi bi-eye"></i> View</button>
                                        <a href="approve.php?NID=<?= urlencode($row['NID']) ?>&type=patient" class="btn btn-sm btn-success"><i class="bi bi-check"></i> Approve</a>
                                        <a href="reject.php?NID=<?= urlencode($row['NID']) ?>&type=patient" class="btn btn-sm btn-danger" onclick="return confirm('Reject this patient?')"><i class="bi bi-x"></i> Reject</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="doctor-pending" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle dynamicTable w-100">
                            <thead class="table-light"><tr><th>Name</th><th>NID</th><th>Specialization</th><th>Reg Date</th><th>Actions</th></tr></thead>
                            <tbody>
                            <?php
                            $res = mysqli_query($conn, "SELECT * FROM doctor WHERE Status='Pending' ORDER BY Reg_Date DESC");
                            while($row = mysqli_fetch_assoc($res)) { 
                                $row['Role'] = 'Doctor'; ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['Full_Name']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['NID']) ?></td>
                                    <td><?= htmlspecialchars($row['Specialization']) ?></td>
                                    <td><?= date('d-M-Y h:i A', strtotime($row['Reg_Date'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white view-user" data-info='<?= json_encode($row) ?>'><i class="bi bi-eye"></i> View</button>
                                        <a href="approve.php?NID=<?= urlencode($row['NID']) ?>&type=doctor" class="btn btn-sm btn-success"><i class="bi bi-check"></i> Approve</a>
                                        <a href="reject.php?NID=<?= urlencode($row['NID']) ?>&type=doctor" class="btn btn-sm btn-danger" onclick="return confirm('Reject this doctor?')"><i class="bi bi-x"></i> Reject</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="mt-pending" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle dynamicTable w-100">
                            <thead class="table-light"><tr><th>Name</th><th>NID</th><th>Lab Name</th><th>Reg Date</th><th>Actions</th></tr></thead>
                            <tbody>
                            <?php
                            $res = mysqli_query($conn, "SELECT * FROM medical_technologist WHERE Status='Pending' ORDER BY Reg_Date DESC");
                            while($row = mysqli_fetch_assoc($res)) { 
                                $row['Role'] = 'MT'; ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['Full_Name']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['NID']) ?></td>
                                    <td><?= htmlspecialchars($row['Lab_Name']) ?></td>
                                    <td><?= date('d-M-Y h:i A', strtotime($row['Reg_Date'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white view-user" data-info='<?= json_encode($row) ?>'><i class="bi bi-eye"></i> View</button>
                                        <a href="approve.php?NID=<?= urlencode($row['NID']) ?>&type=mt" class="btn btn-sm btn-success"><i class="bi bi-check"></i> Approve</a>
                                        <a href="reject.php?NID=<?= urlencode($row['NID']) ?>&type=mt" class="btn btn-sm btn-danger" onclick="return confirm('Reject this MT?')"><i class="bi bi-x"></i> Reject</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

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
    $('.dynamicTable').DataTable({
        "pageLength": 5,
        "language": { "emptyTable": "No pending requests available." }
    });

    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
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