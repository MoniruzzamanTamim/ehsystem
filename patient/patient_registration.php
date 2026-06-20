<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// পাথ চেক করে নিও, তোমার প্রজেক্ট স্ট্রাকচার অনুযায়ী সঠিক পাথ দেওয়া হয়েছে
include '../config/dbconnect.php'; 

$error = "";
$success = "";

// PHP Back-end Validation (যদি কোনোভাবে ইউজার ফ্রন্টএন্ড JS বাইপাস করে সাবমিট দেয়)
if (isset($_POST['register'])) {
    $nid = mysqli_real_escape_string($conn, trim($_POST['nid']));
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    if (!empty($nid) && !empty($full_name) && !empty($phone) && !empty($email) && !empty($password)) {
        
        // পুনরায় চেক করা হচ্ছে NID ডাটাবেজে অলরেডি আছে কিনা
        $check_nid = "SELECT NID FROM patient WHERE NID = '$nid'";
        $result = mysqli_query($conn, $check_nid);

        if (mysqli_num_rows($result) > 0) {
            $error = "This NID is already registered!";
        } else {
            // সরাসরি পাসওয়ার্ড DB-তে সংরক্ষণ করা হবে
            $sql = "INSERT INTO patient (NID, Full_Name, Phone, Email, Password, Reg_Date) 
                    VALUES ('$nid', '$full_name', '$phone', '$email', '$password', NOW())";

            if (mysqli_query($conn, $sql)) {
                $success = "Registration Successful! You can login now.";
            } else {
                $error = "Something went wrong! Error: " . mysqli_error($conn);
            }
        }
    } else {
        $error = "All fields are required!";
    }
}

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration | eHealth System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f5f6fa; }
        .card { border-radius: 15px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header text-white text-center py-3" style="background-color: #0d6efd;">
                    <h3 class="mb-0"><i class="bi bi-person-plus-fill"></i> Patient Registration</h3>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill"></i> <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" autocomplete="off">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">NID Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-id"></i></span>
                                <input type="text" name="nid" id="patient_nid" onkeyup="checkNID()" class="form-control" placeholder="Enter your NID" required>
                            </div>
                            <div id="nid_status" class="form-text fw-bold mt-1"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="full_name" class="form-control" placeholder="Enter full name" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="phone" class="form-control" placeholder="01XXXXXXXXX" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="register" id="reg_btn" class="btn btn-primary btn-lg shadow-sm">
                                <i class="bi bi-arrow-right-circle"></i> Register Now
                            </button>
                        </div>
                    </form>

                </div>
                <div class="card-footer text-center py-3 bg-light">
                    <p class="mb-0">Already have an account? <a href="../login.php" class="text-decoration-none fw-bold">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkNID() {
    var nid = document.getElementById("patient_nid").value;
    var statusSpan = document.getElementById("nid_status");
    var regBtn = document.getElementById("reg_btn");

    if (nid.length < 5) {
        statusSpan.innerHTML = "";
        regBtn.disabled = false; // NID ছোট বা ফাকা হলে বাটন সচল থাকবে
        regBtn.style.opacity = "1";
        return;
    }

    statusSpan.innerHTML = "<span style='color: gray;'>Checking availability...</span>";

    // AJAX রিকোয়েস্ট তৈরি
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText.trim() === "taken") {
                statusSpan.innerHTML = "❌ This NID is already registered!";
                statusSpan.style.color = "red";
                regBtn.disabled = true; // বাটন লক করে দেওয়া হবে
                regBtn.style.opacity = "0.5";
            } else {
                statusSpan.innerHTML = "✅ NID is available for registration.";
                statusSpan.style.color = "green";
                regBtn.disabled = false; // বাটন আনলক
                regBtn.style.opacity = "1";
            }
        }
    };
    xmlhttp.open("GET", "../ajax/patient_info.php?check_nid=" + nid, true);
    xmlhttp.send();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include '../includes/footer.php'  ?> 
</body>
</html>