<?php
session_start();
if (!isset($_SESSION['nid'])) {
    header("Location: ../login.php");
    exit();
}
include '../config/dbconnect.php';

$patient_nid = $_SESSION['nid'];

// ১. ডেটা আপডেট করার পিএইচপি লজিক
if (isset($_POST['update_profile'])) {
    $full_name   = mysqli_real_escape_string($conn, $_POST['full_name']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $mother_name = mysqli_real_escape_string($conn, $_POST['mother_name']);
    $birth_date  = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $phone       = mysqli_real_escape_string($conn, $_POST['phone']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);

    $update_sql = "UPDATE patient SET 
                    Full_Name = '$full_name', 
                    Father_Name = '$father_name', 
                    Mother_Name = '$mother_name', 
                    Birth_Date = '$birth_date', 
                    Blood_Group = '$blood_group', 
                    Phone = '$phone', 
                    Email = '$email' 
                  WHERE NID = '$patient_nid'";

    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['success_msg'] = "Profile updated successfully!";
        header("Location: " . $_SERVER['PHP_SELF']); // পেজ রিফ্রেশ করে নতুন ডেটা দেখানোর জন্য
        exit();
    } else {
        $error_msg = "Error updating profile: " . mysqli_error($conn);
    }
}

// ফ্ল্যাশ মেসেজ রিড করা
$success_msg = '';
if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}

// ২. ডাটাবেজ থেকে পেশেন্টের কারেন্ট তথ্য নিয়ে আসা
$sql = "SELECT * FROM patient WHERE NID = '$patient_nid'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

include '../includes/patient_header.php';
?>

<div style="max-width: 600px; margin: 30px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); font-family: Arial, sans-serif;">
    
    <!-- মেসেজ নোটিফিকেশন -->
    <?php if(!empty($success_msg)): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #c3e6cb; text-align: center; font-weight: bold;">
            <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>
    <?php if(isset($error_msg)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #f5c6cb; text-align: center;">
            <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <!-- ভিউ মোড (Profile View) -->
    <div id="profile_view_mode">
        <h2 style="text-align: center; color: #333; margin-bottom: 5px;">My Personal Health Profile</h2>
        <p style="text-align: center; color: #777; font-size: 14px; margin-top: 0;">Review your personal and medical identification details</p>
        <hr><br>

        <table cellpadding="10" cellspacing="0" width="100%" style="border-collapse: collapse;">
            <tr style="background: #f9f9f9;">
                <td style="font-weight: bold; width: 40%;">National ID (NID):</td>
                <td><span style="background: #eee; padding: 2px 6px; border-radius: 3px; font-weight: bold;"><?php echo htmlspecialchars($user['NID']); ?></span></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Full Name:</td>
                <td><?php echo htmlspecialchars($user['Full_Name']); ?></td>
            </tr>
            <tr style="background: #f9f9f9;">
                <td style="font-weight: bold;">Father's Name:</td>
                <td><?php echo htmlspecialchars($user['Father_Name'] ?? 'Not Provided'); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Mother's Name:</td>
                <td><?php echo htmlspecialchars($user['Mother_Name'] ?? 'Not Provided'); ?></td>
            </tr>
            <tr style="background: #f9f9f9;">
                <td style="font-weight: bold;">Date of Birth:</td>
                <td><?php echo !empty($user['Birth_Date']) ? date('d-M-Y', strtotime($user['Birth_Date'])) : 'Not Provided'; ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Blood Group:</td>
                <td style="color: red; font-weight: bold;"><?php echo htmlspecialchars($user['Blood_Group'] ?? 'Unknown'); ?></td>
            </tr>
            <tr style="background: #f9f9f9;">
                <td style="font-weight: bold;">Phone Number:</td>
                <td><?php echo htmlspecialchars($user['Phone']); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Email Address:</td>
                <td><?php echo htmlspecialchars($user['Email']); ?></td>
            </tr>
            <tr style="background: #f9f9f9;">
                <td style="font-weight: bold;">Account Status:</td>
                <td>
                    <span style="background: <?php echo (strtolower($user['Status']) == 'active') ? 'green' : 'orange'; ?>; color: white; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold;">
                        <?php echo htmlspecialchars($user['Status']); ?>
                    </span>
                </td>
            </tr>
        </table>

        <br><br>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <a href="dashboard.php" style="background: #555; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-size: 14px;">Back to Dashboard</a>
            <button type="button" onclick="toggleEditMode(true)" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: bold;">Edit Profile Info</button>
        </div>
    </div>

    <!-- এডিট মোড (Profile Edit Form) -->
    <div id="profile_edit_mode" style="display: none;">
        <h2 style="text-align: center; color: #007bff; margin-bottom: 5px;">Update Profile Information</h2>
        <p style="text-align: center; color: #777; font-size: 14px; margin-top: 0;">Make sure your data matches your official documents</p>
        <hr><br>

        <form method="POST" action="">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">National ID (NID) <small style="color:#999;">(Cannot be changed)</small></label>
                <input type="text" value="<?php echo htmlspecialchars($user['NID']); ?>" disabled style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; background: #f1f1f1; box-sizing: border-box; font-weight: bold;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['Full_Name']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Father's Name</label>
                <input type="text" name="father_name" value="<?php echo htmlspecialchars($user['Father_Name'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Mother's Name</label>
                <input type="text" name="mother_name" value="<?php echo htmlspecialchars($user['Mother_Name'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Date of Birth</label>
                <input type="date" name="birth_date" value="<?php echo htmlspecialchars($user['Birth_Date'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Blood Group</label>
                <select name="blood_group" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background: #fff;">
                    <option value="">Select Blood Group</option>
                    <?php 
                    $bg_options = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                    foreach($bg_options as $bg) {
                        $selected = ($user['Blood_Group'] == $bg) ? 'selected' : '';
                        echo "<option value='$bg' $selected>$bg</option>";
                    }
                    ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['Phone']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #444;">Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <hr><br>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <button type="button" onclick="toggleEditMode(false)" style="background: #e0e0e0; color: #333; padding: 10px 20px; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">Cancel</button>
                <button type="submit" name="update_profile" style="background: #28a745; color: white; padding: 10px 25px; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: bold;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- জাভাস্ক্রিপ্ট ভিউ এবং এডিট মোড টগল করার জন্য -->
<script>
function toggleEditMode(isEdit) {
    var viewMode = document.getElementById('profile_view_mode');
    var editMode = document.getElementById('profile_edit_mode');
    
    if (isEdit) {
        viewMode.style.display = 'none';
        editMode.style.display = 'block';
    } else {
        viewMode.style.display = 'block';
        editMode.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>