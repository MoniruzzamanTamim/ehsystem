<?php
session_start();
if (!isset($_SESSION['doctor'])) {
    header("Location: login.php");
    exit();
}
include '../includes/header.php';
?>

<div style="padding: 20px;">
    <h2>Search Patient Medical History</h2>
    
    <div style="margin-bottom: 20px;">
        <input type="text" id="search_nid" placeholder="Enter Patient NID (e.g., 1234567890)" style="padding: 8px; width: 250px;">
        <button onclick="viewHistory()" style="padding: 8px 15px;">Search History</button>
    </div>

    <div id="history_result">
        <p style="color: gray;">Enter an NID and click search to view medical records.</p>
    </div>
    
    <br><br>
    <a href="dashboard.php">Back to Dashboard</a>
</div>

<script src="../js/all_js.js"></script>

<?php include '../includes/footer.php'; ?>