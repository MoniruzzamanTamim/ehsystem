<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'config/dbconnect.php';
include 'includes/header.php';
?>

<?php
include_once 'Components/Slider.php';
renderHomePageSlider(__DIR__ . '/images');
?>

<?php
// Sayfa üzerindeki dinamik verileri diziler (Arrays) içinde tanımlayalım.
// Bu yapı ileride veritabanı (Database) bağlantısı yapmanızı çok kolaylaştırır.

$specialties = [
    ['title' => 'Primary Care & General Medicine', 'icon' => 'fa-stethoscope', 'bg' => 'bg-blue-50', 'text' => 'text-indigo-500'],
    ['title' => "OB-GYN's & Women's Health", 'icon' => 'fa-person-pregnant', 'bg' => 'bg-pink-50', 'text' => 'text-pink-500'],
    ['title' => 'Pediatrics', 'icon' => 'fa-baby', 'bg' => 'bg-green-50', 'text' => 'text-green-500'],
    ['title' => 'Diabetes & Endocrinology', 'icon' => 'fa-droplet', 'bg' => 'bg-amber-50', 'text' => 'text-amber-500'],
    ['title' => 'Eye & Vision Doctor', 'icon' => 'fa-eye', 'bg' => 'bg-purple-50', 'text' => 'text-purple-500'],
    ['title' => 'Heart & Cardiology', 'icon' => 'fa-heart', 'bg' => 'bg-red-50', 'text' => 'text-red-500'],
    ['title' => 'Skin & Dermatology', 'icon' => 'fa-hand-holding-medical', 'bg' => 'bg-teal-50', 'text' => 'text-teal-500'],
    ['title' => 'Brain & Nerves', 'icon' => 'fa-brain', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-500'],
];

$conditions = [
    ['title' => 'Acne & Eczema', 'emoji' => '🧴'],
    ['title' => 'Allergic Rhinitis', 'emoji' => '🤧'],
    ['title' => 'Arthritis & Gout', 'emoji' => '🦴'],
    ['title' => 'Colds & Flu', 'emoji' => '🤒'],
    ['title' => 'High Blood Pressure', 'emoji' => '❤️'],
    ['title' => 'Diabetes', 'emoji' => '🩸'],
];

$services = [
    ['category' => 'Service', 'title' => "Children's Vaccinations", 'border' => 'border-l-blue-500'],
    ['category' => 'Documentation', 'title' => 'Medical Certificates', 'border' => 'border-l-indigo-500'],
    ['category' => 'Surgery', 'title' => 'LASIK Eye Surgery', 'border' => 'border-l-purple-500'],
    ['category' => 'Care', 'title' => 'IUDs & Birth Control', 'border' => 'border-l-pink-500'],
];

$hospitals = [
    "St. Luke's Medical Center - Global City",
    "St. Luke's Medical Center - Quezon City",
    "Asian Hospital and Medical Center",
    "Cardinal Santos Medical Center",
    "The Medical City",
    "Makati Medical Center"
];

$hmos = ["MAXICARE", "INTELLICARE", "MEDICARD", "AVEGA", "PHILCARE"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NowServing Clone - PHP Version</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <main class="max-w-6xl mx-auto px-4 py-12 space-y-16">

        <!-- #Find Doctor Section====================================> -->
        <?php 
             // কম্পোনেন্টটি ইনক্লুড করা হলো
            include 'components/HomePage/searchDoctor.php'; 
        ?>
        <!-- #Find Doctor Section====================================> -->

        <section class="max-w-3xl mx-auto bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
            <div class="space-y-4 text-center md:text-left">
                <div>
                    <p class="text-xs font-bold tracking-wider text-indigo-500 uppercase mb-1">Video Consultation</p>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Need urgent medical advice?</h2>
                    <p class="text-gray-600 mt-1">Speak with a doctor in <span class="font-bold text-gray-900">15 minutes</span></p>
                </div>
                <button class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-6 py-3 rounded-full text-sm shadow-md hover:shadow-lg transition-all uppercase tracking-wide">
                    CONSULT NOW
                </button>
            </div>
            <div class="relative w-32 h-32 md:w-40 md:h-40 flex items-center justify-center bg-white rounded-full shadow-inner border border-indigo-100 flex-shrink-0">
                <i class="fa-solid fa-user-doctor text-5xl text-indigo-400"></i>
                <div class="absolute -bottom-2 -right-2 bg-pink-500 text-white p-2 rounded-xl text-xs font-bold shadow animate-bounce">
                    <i class="fa-solid fa-comment-dots"></i> Live
                </div>
            </div>
        </section>

        <section class="space-y-6">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Popular Specialties</h2>
                    <p class="text-sm text-gray-500">Most searched medical specialties.</p>
                </div>
                <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-wider">View All</a>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <?php foreach ($specialties as $specialty): ?>
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 flex flex-col items-center text-center justify-center shadow-sm hover:shadow-md transition-all cursor-pointer group">
                        <div class="w-12 h-12 rounded-xl <?php echo $specialty['bg']; ?> flex items-center justify-center <?php echo $specialty['text']; ?> mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid <?php echo $specialty['icon']; ?> text-xl"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 group-hover:text-indigo-600 transition-colors">
                            <?php echo $specialty['title']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="space-y-6">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Common Conditions</h2>
                    <p class="text-sm text-gray-500">Find doctors who treat these conditions easily.</p>
                </div>
                <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-wider">View All</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <?php foreach ($conditions as $condition): ?>
                    <div class="bg-white border border-gray-100 rounded-xl p-3 flex items-center space-x-3 shadow-sm hover:border-indigo-200 transition-all cursor-pointer">
                        <span class="text-xl"><?php echo $condition['emoji']; ?></span>
                        <span class="text-sm font-medium text-gray-700"><?php echo $condition['title']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="space-y-6">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Popular Services</h2>
                    <p class="text-sm text-gray-500">Popular medical services and treatments.</p>
                </div>
                <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-wider">View All</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <?php foreach ($services as $service): ?>
                    <div class="bg-white border border-gray-100 rounded-xl p-4 flex flex-col justify-between shadow-sm hover:shadow-md transition-all cursor-pointer border-l-4 <?php echo $service['border']; ?>">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider"><?php echo $service['category']; ?></span>
                        <span class="text-sm font-semibold text-gray-800 mt-2"><?php echo $service['title']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="space-y-6">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Institutions and Hospitals</h2>
                    <p class="text-sm text-gray-500">Doctors at leading healthcare institutions.</p>
                </div>
                <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-wider">View All</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <?php foreach ($hospitals as $hospital): ?>
                    <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center space-x-3 hover:bg-gray-50 transition-all cursor-pointer shadow-sm">
                        <div class="text-indigo-500 bg-indigo-50 p-2.5 rounded-lg flex-shrink-0"><i class="fa-solid fa-hospital-user text-lg"></i></div>
                        <span class="text-sm font-medium text-gray-800 line-clamp-1"><?php echo $hospital; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="space-y-6">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Partner Institutions & Insurance</h2>
                    <p class="text-sm text-gray-500">Filter doctors who accept these institutions.</p>
                </div>
                <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-wider">View All</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                <?php foreach ($hmos as $hmo): ?>
                    <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center justify-center text-center font-bold text-gray-500 tracking-wide hover:border-indigo-400 transition-all cursor-pointer shadow-sm">
                        <?php echo $hmo; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>






<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script>
$(document).ready(function() {
    // প্রথমবার পেজ লোড হলে fetchDoctors() অটোমেটিক কল হবে না, লিস্ট হাইড থাকবে।

    // ডানপাশের "Find Doctor" বাটনে ক্লিক করলে সার্চ হবে এবং লিস্ট শো করবে
    $('#find_doctor_btn').on('click', function() {
        var query = $('#search_doctor').val();
        var category = $('#doctor_dept').val();
        
        // রেজাল্ট কন্টেইনার আনহাইড (Show) করা
        $('#doctor_results_container').removeClass('d-none');
        
        fetchDoctors(query, category);
    });

    // তুমি চাইলে ইউজার এন্টার (Enter) চাপলেও যেন সার্চ বাটন ট্রিগার হয় তার ব্যবস্থা:
    $('#search_doctor').on('keypress', function(e) {
        if(e.which == 13) { // 13 হলো Enter কী
            $('#find_doctor_btn').click();
        }
    });

    // ডাটাবেজ থেকে ডাটা আনার AJAX ফাংশন
    function fetchDoctors(query, category) {
        // লোডিং ইফেক্ট দেখানোর জন্য
        $('#doctor_list').html('<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Searching doctors...</p></div>');
        
        $.ajax({
            url: 'fetch_doctors.php',
            type: 'POST',
            data: { action: 'filter_doctors', query: query, category: category },
            success: function(response) {
                $('#doctor_list').html(response);
            }
        });
    }

    // লিস্টের কার্ডে ক্লিক করলে মডাল ওপেন হবে
    $(document).on('click', '.view-doctor-details', function() {
        var doctorData = $(this).data('info');
        var profilePic = doctorData.Profile_Pic ? '../uploads/' + doctorData.Profile_Pic : 'https://via.placeholder.com/150';
        
        var detailsHtml = `
            <div class="row g-4">
                <div class="col-md-4 text-center border-end">
                    <img src="${profilePic}" class="img-fluid rounded-circle img-thumbnail mb-3 shadow-sm" style="width: 140px; height: 140px; object-fit: cover;">
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
        $('#doctorDetailsModal').modal('show');
    });
});
</script>




<?php include 'includes/footer.php'; ?>
</body>
</html>