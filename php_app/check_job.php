<?php 
include "navbar.php";
include "db.php";

// Guest access allowed
$result = "";
$resultClass = "";
?>

<!DOCTYPE html>
<html>
<head>
<title>ScamShield - Check Job</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body {
            background-color: #0f2027;
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
        }
        .page-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 50px;
        }
        .login-card {
            width: 500px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }
        .login-header {
            background: linear-gradient(45deg, #00d2ff, #3a7bd5);
            color: white;
            padding: 25px;
            text-align: center;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #00d2ff;
            color: white;
            box-shadow: none;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }
        .btn-login {
            background: linear-gradient(45deg, #00d2ff, #3a7bd5);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 210, 255, 0.3);
            color: white;
        }
        .alert {
            word-wrap: break-word;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        #ajaxResult {
            transition: all 0.3s ease;
        }
        #reasonValue {
            color: rgba(255, 255, 255, 0.7) !important;
        }
    </style>
</head>

<div class="page-wrapper">
<div class="card login-card animate__animated animate__zoomIn">
    <div class="login-header">
        <h4>🛡 ScamShield - Job Checker</h4>
        <small>AI powered job offer verification</small>
    </div>

    <div class="card-body p-4 text-white">

        <form id="jobForm">
            <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4" id="pills-text-tab" data-bs-toggle="pill" data-bs-target="#pills-text" type="button" role="tab">📝 Text Analysis</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="pills-image-tab" data-bs-toggle="pill" data-bs-target="#pills-image" type="button" role="tab">🖼 Image Analysis</button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <!-- Text Tab -->
                <div class="tab-pane fade show active" id="pills-text" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label text-white">Paste Job Offer Text</label>
                        <textarea id="job_text_input" name="job_text" class="form-control" rows="5" placeholder="Enter job details here..."></textarea>
                    </div>
                </div>

                <!-- Image Tab -->
                <div class="tab-pane fade" id="pills-image" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label text-white">Upload Job Poster / Screenshot</label>
                        <input type="file" id="job_image_input" name="job_image" class="form-control" accept="image/*">
                        <div id="imagePreview" class="mt-3 text-center" style="display:none;">
                            <img id="previewImg" src="" class="img-fluid rounded-3 border border-info" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="index.php" class="btn btn-outline-primary" style="border-radius:10px;">⬅ Back</a>
                <button type="button" id="checkBtn" class="btn btn-login shadow">
                    🔍 Start Analysis
                </button>
            </div>
        </form>

        <div id="ajaxResult" class="mt-4" style="display:none;">
            <div id="resultAlert" class="alert shadow-sm border-0">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="mb-2" id="resultLabel">Result: <span id="resultValue"></span></h5>
                        
                        <!-- Extracted Text (For Image Analysis) -->
                        <div id="extractedArea" class="mt-2 pt-2 border-top border-white border-opacity-10" style="display:none;">
                            <strong><i class="fa-solid fa-file-lines me-1"></i> Extracted Text:</strong>
                            <p class="mb-2 small text-white-50 fst-italic" id="extractedValue" style="max-height: 100px; overflow-y: auto;"></p>
                        </div>

                        <div id="reasonArea" class="mt-2 pt-2 border-top border-white border-opacity-10">
                            <strong><i class="fa-solid fa-circle-info me-1"></i> Why detected:</strong> 
                            <p class="mb-0 small text-white-50" id="reasonValue"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

<script>
// Image Preview Logic
document.getElementById('job_image_input').addEventListener('change', function(event) {
    const reader = new FileReader();
    const previewArea = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    reader.onload = function() {
        previewImg.src = reader.result;
        previewArea.style.display = 'block';
    }
    
    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
});

function triggerCheck() {
    const text = document.getElementById('job_text_input').value.trim();
    const imageInput = document.getElementById('job_image_input');
    const resultDiv = document.getElementById('ajaxResult');
    const alertDiv = document.getElementById('resultAlert');
    const resultValue = document.getElementById('resultValue');
    const reasonValue = document.getElementById('reasonValue');
    const reasonArea = document.getElementById('reasonArea');
    const extractedArea = document.getElementById('extractedArea');
    const extractedValue = document.getElementById('extractedValue');

    const activeTab = document.querySelector('.nav-link.active').id;
    const formData = new FormData();

    if (activeTab === 'pills-image-tab') {
        if (!imageInput.files[0]) {
            alert('Please select an image first!');
            return;
        }
        formData.append('image', imageInput.files[0]);
    } else {
        if (text.length < 20) {
            alert('Please enter more details (at least 20 characters)...');
            return;
        }
        formData.append('job_text', text);
    }

    // Reset UI
    resultDiv.style.display = 'block';
    alertDiv.className = 'alert alert-info shadow-sm border-0';
    resultValue.innerText = 'Analyzing...';
    reasonArea.style.display = 'none';
    extractedArea.style.display = 'none';

    fetch('process_check.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            resultDiv.style.display = 'block';
            resultValue.innerText = data.result + (data.result === 'Fake' ? ' ❌' : ' ✅');
            reasonValue.innerText = data.reason;
            reasonArea.style.display = 'block';

            if(data.extracted_text) {
                extractedArea.style.display = 'block';
                extractedValue.innerText = data.extracted_text;
            } else {
                extractedArea.style.display = 'none';
            }
            
            alertDiv.className = 'alert shadow-sm border-0 ' + (data.result === 'Fake' ? 'alert-danger' : 'alert-success');
        } else {
            resultDiv.style.display = 'block';
            alertDiv.className = 'alert alert-warning shadow-sm border-0';
            resultValue.innerText = data.message;
            reasonArea.style.display = 'none';
            extractedArea.style.display = 'none';
        }
    })
    .catch(err => {
        console.error(err);
        resultDiv.style.display = 'block';
        alertDiv.className = 'alert alert-danger shadow-sm border-0';
        resultValue.innerText = 'Service Unavailable';
        reasonArea.style.display = 'none';
        extractedArea.style.display = 'none';
    });
}

document.getElementById('checkBtn').addEventListener('click', triggerCheck);
</script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
