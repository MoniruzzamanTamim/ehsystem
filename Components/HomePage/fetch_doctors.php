<?php
include '../../config/dbconnect.php'; 

if (isset($_POST['action'])) {
    
    // ১. মেইন ডক্টর লিস্ট জেনারেট করার লজিক
    if ($_POST['action'] == 'filter_doctors') {
        $query = mysqli_real_escape_string($conn, $_POST['query']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);

        $sql = "SELECT * FROM doctor WHERE Status='Approved'";

        if ($category !== 'All') {
            $sql .= " AND Specialization = '$category'";
        }

        if (!empty($query)) {
            $sql .= " AND (Full_Name LIKE '%$query%' OR Specialization LIKE '%$query%' OR Chamber LIKE '%$query%' OR Education LIKE '%$query%')";
        }

        $sql .= " ORDER BY Full_Name ASC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $profile_pic = !empty($row['Profile_Pic']) ? 'uploads/doctors/' . $row['Profile_Pic'] : 'https://via.placeholder.com/150';
                
                // এখানে data-id এর ভেতরে ডক্টরের আইডি পাস করা হয়েছে
                echo '
                <div class="col-xl-3 col-lg-4 col-md-6 mb-2">
                    <div class="card h-100 shadow-sm border border-light-subtle rounded-4 text-center p-4 view-doctor-details" data-id="' . $row['id'] . '" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;">
                        <img src="' . $profile_pic . '" class="rounded-circle mx-auto mb-3 border shadow-sm" style="width: 90px; height: 90px; object-fit: cover;" onerror="this.src=\'https://via.placeholder.com/150\'">
                        
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

    // ২. 🆕 নতুন লজিক: মডালের জন্য নির্দিষ্ট আইডির ডক্টর ডাটা নিয়ে আসা
    if ($_POST['action'] == 'get_doctor_details') {
        $doctor_id = mysqli_real_escape_string($conn, $_POST['id']);
        $sql = "SELECT * FROM doctor WHERE id = '$doctor_id'";
        $result = mysqli_query($conn, $sql);
        
        if ($row = mysqli_fetch_assoc($result)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($row);
        }
        exit();
    }
}
?>