<div class="container my-5">
    <div class="text-center mb-4">
        <p class="text-uppercase text-primary fw-bold tracking-wider mb-1" style="font-size: 0.75rem; letter-spacing: 0.1em;">FIND A DOCTOR</p>
        <h1 class="fw-extrabold text-dark display-6">
            Book your appointment, in-person or <span class="text-primary">online</span>
        </h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="input-group shadow-sm rounded-3 overflow-hidden p-1 bg-white border">
                
                <select id="doctor_dept" class="form-select border-0 fw-semibold text-secondary" style="max-width: 180px; box-shadow: none;">
                    <option value="All">All Categories</option>
                    <?php
                    // ডাটাবেজ কানেকশন চেক করে ইউনিক Specialization তুলে আনা
                    if (isset($conn)) {
                        $dept_query = "SELECT DISTINCT Specialization FROM doctor WHERE Specialization IS NOT NULL AND Specialization != '' AND Status='Approved' ORDER BY Specialization ASC";
                        $dept_result = mysqli_query($conn, $dept_query);
                        if ($dept_result && mysqli_num_rows($dept_result) > 0) {
                            while ($dept_row = mysqli_fetch_assoc($dept_result)) {
                                $spec = htmlspecialchars($dept_row['Specialization']);
                                echo "<option value='{$spec}'>{$spec}</option>";
                            }
                        }
                    }
                    ?>
                </select>
                
                <div class="vr my-2 bg-secondary opacity-25"></div>

                <span class="input-group-text bg-white border-0 text-primary pe-1">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" id="search_doctor" class="form-control border-0 py-3 text-dark" 
                       placeholder="Search by specialty, hospital, or doctor name..." 
                       style="box-shadow: none;">

                <button id="find_doctor_btn" class="btn btn-primary px-4 fw-bold text-white rounded-2 m-1" type="button">
                    <i class="fa-solid fa-user-md me-1"></i> Find Doctor
                </button>
                
            </div>
        </div>
    </div>
</div>

<div class="container my-4 d-none" id="doctor_results_container">
    <h4 class="fw-bold text-secondary mb-4"><i class="fa-solid fa-list-ul me-2"></i> Search Results:</h4>
    <div class="row g-4" id="doctor_list">
        </div>
</div>

<div class="modal fade" id="doctorDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 overflow-hidden border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-user-md me-2"></i> Doctor Profile Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modal_doctor_body">
                </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>


<script>
$(document).ready(function() {
    // সার্চ বাটন ক্লিক
    $('#find_doctor_btn').on('click', function() {
        var query = $('#search_doctor').val();
        var category = $('#doctor_dept').val();
        $('#doctor_results_container').removeClass('d-none');
        fetchDoctors(query, category);
    });

    // এন্টার প্রেস করলে সার্চ
    $('#search_doctor').on('keypress', function(e) {
        if(e.which == 13) {
            $('#find_doctor_btn').click();
        }
    });

    function fetchDoctors(query, category) {
        $('#doctor_list').html('<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Searching doctors...</p></div>');
        
        $.ajax({
            url: 'components/HomePage/fetch_doctors.php',
            type: 'POST',
            data: { action: 'filter_doctors', query: query, category: category },
            success: function(response) {
                $('#doctor_list').html(response);
            }
        });
    }

    // কার্ডে ক্লিক করলে এখন আইডি দিয়ে ডাটাবেজ থেকে ফ্রেশ ডাটা আসবে
    $(document).on('click', '.view-doctor-details', function() {
        var doctorId = $(this).attr('data-id'); // ডক্টরের আইডি নেওয়া হলো
        
        $.ajax({
            url: 'components/HomePage/fetch_doctors.php',
            type: 'POST',
            data: { action: 'get_doctor_details', id: doctorId },
            dataType: 'json',
            success: function(doctorData) {
                
                var profilePic = doctorData.Profile_Pic ? 'uploads/doctors/' + doctorData.Profile_Pic : 'https://via.placeholder.com/150';
                
                var detailsHtml = `
                    <div class="row g-4">
                        <div class="col-md-4 text-center border-end">
                            <img src="${profilePic}" class="img-fluid rounded-circle img-thumbnail mb-3 shadow-sm" style="width: 140px; height: 140px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/150'">
                            <h4 class="fw-bold text-dark mb-1">${doctorData.Full_Name}</h4>
                            <p class="text-primary font-semibold small mb-2">${doctorData.Title || ''}</p>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 small fw-bold">${doctorData.Specialization}</span>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle">
                                    <tr class="border-bottom">
                                        <th class="py-2 text-muted" style="width: 35%;">Education</th>
                                        <td class="py-2 text-dark fw-medium">${doctorData.Education || 'N/A'}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="py-2 text-muted">Experience</th>
                                        <td class="py-2 text-dark fw-medium">${doctorData.Experience || '0'} Years</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="py-2 text-muted">Chamber</th>
                                        <td class="py-2 text-dark fw-medium">${doctorData.Chamber || 'N/A'}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="py-2 text-muted">Available Time</th>
                                        <td class="py-2 text-primary fw-bold">${doctorData.Available_Time || 'N/A'}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="py-2 text-muted">Contact Info</th>
                                        <td class="py-2 text-dark small">${doctorData.Email}<br>${doctorData.Phone || ''}</td>
                                    </tr>
                                    <tr>
                                        <th class="py-2 text-muted">Bio / Statement</th>
                                        <td class="py-2 text-secondary small">${doctorData.Bio || 'No biography available.'}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#modal_doctor_body').html(detailsHtml);
                $('#doctorDetailsModal').modal('show'); // মডাল ওপেন হবে
            },
            error: function(xhr, status, error) {
                console.error("Error fetching doctor details: ", error);
            }
        });
    });
});
</script>