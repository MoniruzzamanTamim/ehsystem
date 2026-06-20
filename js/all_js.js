// Patient Queue এবং অন্যান্য ডাটা লাইভ লোড করার AJAX ফাংশন
function loadPatientQueue() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("queue_table").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "../ajax/patient_queue.php", true);
    xmlhttp.send();
}

// প্রতি ৫ সেকেন্ড পর পর কিউ অটো-রিফ্রেশ হবে
if(document.getElementById("queue_table")) {
    setInterval(loadPatientQueue, 5000);
}

// পেশেন্টের মেডিকেল হিস্ট্রি খোঁজার AJAX ফাংশন
function viewHistory() {
    var nid = document.getElementById("search_nid").value;
    if(nid == "") {
        alert("Please enter a Patient NID first!");
        return;
    }
    
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("history_result").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "../ajax/show_patient_medical_history.php?nid=" + nid, true);
    xmlhttp.send();
}

// রিয়েল-টাইম NID ভেরিফিকেশন ফাংশন
function checkNID() {
    var nid = document.getElementById("patient_nid").value;
    var statusSpan = document.getElementById("nid_status");
    var regBtn = document.getElementById("reg_btn");

    if (nid.length < 5) {
        statusSpan.innerHTML = "";
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