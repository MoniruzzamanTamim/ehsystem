<?php
include '../config/dbconnect.php';
include '../includes/header_link.php';

// ১. ডিলিট লজিক (সবার আগে থাকতে হবে)
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM lab_tests WHERE id = $id");
    // ডিলিট হওয়ার পর পেজ রিফ্রেশ করে দাও যাতে লিস্ট আপডেট হয়
    header("Location: manage_tests.php");
    exit();
}

// ডাটা আপডেট বা সেভ করার লজিক
if(isset($_POST['save_test'])) {
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $rate = (float)$_POST['rate'];
    $room = mysqli_real_escape_string($conn, $_POST['room']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $tech = mysqli_real_escape_string($conn, $_POST['tech']);

    if($id > 0) {
        // Edit করার কুয়েরি
        mysqli_query($conn, "UPDATE lab_tests SET test_name='$name', rate='$rate', test_room='$room', time_required='$time', technologist_name='$tech' WHERE id=$id");
    } else {
        // নতুন Add করার কুয়েরি
        mysqli_query($conn, "INSERT INTO lab_tests (test_name, rate, test_room, time_required, technologist_name) VALUES ('$name', '$rate', '$room', '$time', '$tech')");
    }
    header("Location: manage_tests.php"); // রিফ্রেশ
}

// এডিট বাটনে ক্লিক করলে ডাটা ফেচ করা
$edit_data = ['id' => '', 'test_name' => '', 'rate' => '', 'test_room' => '', 'time_required' => '', 'technologist_name' => ''];
if(isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM lab_tests WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($res);
}

$tests = mysqli_query($conn, "SELECT * FROM lab_tests");
$techs = mysqli_query($conn, "SELECT Full_Name FROM medical_technologist");
?>

<form method="POST" class="row g-2 mb-4 bg-light p-3">
    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
    
    <div class="col"><input type="text" name="name" class="form-control" value="<?php echo $edit_data['test_name']; ?>" placeholder="Test Name" required></div>
    <div class="col"><input type="number" name="rate" class="form-control" value="<?php echo $edit_data['rate']; ?>" placeholder="Rate"></div>
    <div class="col"><input type="text" name="room" class="form-control" value="<?php echo $edit_data['test_room']; ?>" placeholder="Room"></div>
    <div class="col"><input type="text" name="time" class="form-control" value="<?php echo $edit_data['time_required']; ?>" placeholder="Time"></div>
    <div class="col">
        <select name="tech" class="form-control">
            <option value="">Select Technologist</option>
            <?php 
            mysqli_data_seek($techs, 0); // ড্রপডাউন রিসেট
            while($t = mysqli_fetch_assoc($techs)) { 
                $selected = ($edit_data['technologist_name'] == $t['Full_Name']) ? 'selected' : '';
                echo "<option value='{$t['Full_Name']}' $selected>{$t['Full_Name']}</option>";
            } ?>
        </select>
    </div>
    <div class="col">
        <?php if($edit_data['id']): ?>
            <button type="submit" name="save_test" class="btn btn-warning">Update</button>
            <a href="manage_tests.php" class="btn btn-secondary">Cancel</a>
        <?php else: ?>
            <button type="submit" name="save_test" class="btn btn-primary">Add</button>
        <?php endif; ?>
    </div>


    <table class="table table-bordered">
        <thead class="table-dark">
            <tr><th>Name</th><th>Rate</th><th>Room</th><th>Time</th><th>Technologist</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($tests)) { ?>
            <tr>
                <td><?php echo $row['test_name']; ?></td>
                <td><?php echo $row['rate']; ?></td>
                <td><?php echo $row['test_room']; ?></td>
                <td><?php echo $row['time_required']; ?></td>
                <td><?php echo $row['technologist_name']; ?></td>
                <td>
                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>