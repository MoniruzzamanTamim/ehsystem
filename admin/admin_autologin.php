<?php
// admin_autologin.php
include '../config/dbconnect.php';
if (isset($_POST['action']) && $_POST['action'] == 'filter_doctors') {
    $query = mysqli_real_escape_string($conn, $_POST['query']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    // শুধুমাত্র Approved ডাক্তারদের খোঁজা হবে
    $sql = "SELECT * FROM doctor WHERE Status='Approved'";

    // ড্রপডাউন ফিল্টার ("All" না হলে নির্দিষ্ট ক্যাটাগরি যুক্ত হবে)
    if ($category !== 'All') {
        $sql .= " AND Specialization = '$category'";
    }

    // সার্চ ইনপুট ফিল্টার
    if (!empty($query)) {
        $sql .= " AND (Full_Name LIKE '%$query%' OR Specialization LIKE '%$query%' OR Chamber LIKE '%$query%' OR Education LIKE '%$query%')";
    }

    $sql .= " ORDER BY Full_Name ASC";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $json_data = json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT);
            $profile_pic = !empty($row['Profile_Pic']) ? '../uploads/' . $row['Profile_Pic'] : 'https://via.placeholder.com/150';
            
            echo '
            <div class="col-xl-3 col-lg-4 col-md-6 mb-2">
                <div class="card h-100 shadow-sm border border-light-subtle rounded-4 text-center p-4 view-doctor-details" data-info=\'' . $json_data . '\' style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;">
                    <img src="' . $profile_pic . '" class="rounded-circle mx-auto mb-3 object-cover border" style="width: 90px; height: 90px; object-fit: cover;">
                    <span class="badge bg-primary-subtle text-primary rounded-pill mb-2 px-3 py-1 small">' . htmlspecialchars($row['Specialization']) . '</span>
                    <h5 class="card-title fw-bold text-dark text-truncate mb-1">' . htmlspecialchars($row['Full_Name']) . '</h5>
                    <p class="text-muted small mb-3">' . htmlspecialchars($row['Title'] ?? 'Medical Specialist') . '</p>
                    
                    <div class="border-top pt-2 text-start text-muted small mt-auto">
                        <p class="mb-1 text-truncate"><i class="fa-solid fa-graduation-cap me-1"></i> ' . htmlspecialchars($row['Education'] ?? 'N/A') . '</p>
                        <p class="mb-0 text-truncate"><i class="fa-solid fa-location-dot me-2"></i> ' . htmlspecialchars($row['Chamber'] ?? 'Chamber not added') . '</p>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo '
        <div class="col-12 text-center py-5 text-muted">
            <i class="fa-solid fa-user-slash display-4 mb-3 text-body-tertiary"></i>
            <p class="lead">No doctors found matching your criteria.</p>
        </div>';
    }
    exit();
}
?>