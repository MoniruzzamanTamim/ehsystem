<?php 
include '../config/dbconnect.php';
$ticket_no = $_GET['ticket'] ?? '';

$test_names = [];
$test_options = '';
$result = $conn->query("SELECT test_name FROM lab_tests");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $test_names[] = $row['test_name'];
    }
}
foreach ($test_names as $name) {
    $escapedName = htmlspecialchars($name, ENT_QUOTES);
    $test_options .= "<option value=\"{$escapedName}\">{$escapedName}</option>";
}
include '../includes/header_link.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .header-brand { font-weight: 700; letter-spacing: .05em; }
        .page-header { background: #0d6efd; color: #fff; padding: 2rem 0; }
        .page-header p { color: rgba(255,255,255,.9); }
        .card { border: none; border-radius: 1rem; }
        footer { background: #212529; color: #adb5bd; padding: 1.25rem 0; }
        footer a { color: #adb5bd; text-decoration: none; }
        footer a:hover { color: #fff; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand header-brand" href="#">EH System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Reports</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Support</a></li>
            </ul>
        </div>
    </div>
</nav>
<header class="page-header mb-4">
    <div class="container text-center">
        <h1 class="display-6">Upload Final Report</h1>
        <p class="lead mb-0">Submit pictures and text for ticket #<?php echo htmlspecialchars($ticket_no); ?>.</p>
    </div>
</header>
<div class="container mb-5">
    <div class="card shadow p-4">
        <h4>Upload Report for: <?php echo $ticket_no; ?></h4>
        <form action="save_report.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="ticket_no" value="<?php echo $ticket_no; ?>">
            
            <div id="dynamicFields">
                <div class="row mb-2">
                    <div class="col-md-5">
                        <select name="pic_name[]" class="form-control">
                            <option value="">Select Test Name</option>
                            <?php echo $test_options; ?>
                        </select>
                    </div>
                    <div class="col-md-5"><input type="file" name="pic_file[]" class="form-control"></div>
                </div>
            </div>
            
            <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addField()">+ Add Another Pic</button>
            
            <textarea name="text_report" class="form-control" rows="4" placeholder="Or write report text here..."></textarea>
            <button type="submit" class="btn btn-success mt-3 w-100">Submit Final Report</button>
        </form>
    </div>
</div>

<script>
const testOptions = `<?php echo str_replace('`', '&#96;', $test_options); ?>`;
function addField() {
    let div = document.createElement('div');
    div.innerHTML = `<div class="row mb-2"><div class="col-md-5"><select name="pic_name[]" class="form-control"><option value="">Select Test Name</option>${testOptions}</select></div><div class="col-md-5"><input type="file" name="pic_file[]" class="form-control"></div></div>`;
    document.getElementById('dynamicFields').appendChild(div);
}
</script>