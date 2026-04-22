<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// No DB logic needed for privacy, but we need session for navbar
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | ScamShield</title>
    
    <?php include "header_assets.php"; ?>
    <style>
        :root {
            --primary-bg: #0f2027;
            --accent-blue: #00d2ff;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        body {
            background-color: var(--primary-bg);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            line-height: 1.6;
        }
        .content-wrap {
            padding: 80px 0;
            background: radial-gradient(circle at top right, rgba(0, 210, 255, 0.03), transparent);
        }
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 50px;
        }
        .policy-section {
            margin-bottom: 40px;
        }
        .text-gradient {
            background: linear-gradient(135deg, #00d2ff, #3a7bd5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        h2 { font-weight: 700; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="content-wrap">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="glass-card animate__animated animate__fadeIn">
                        <h1 class="display-4 fw-bold mb-4 text-gradient">Privacy Policy</h1>
                        <p class="lead text-white-50 mb-5">At ScamShield, we prioritize your digital safety and privacy. This policy outlines how we handle your data.</p>
                        
                        <div class="policy-section">
                            <h2>1. Data Collection</h2>
                            <p>We collect information you provide directly to us, such as job descriptions when you use our AI scanning tool. We also collect basic account information like your name and email when you register.</p>
                        </div>

                        <div class="policy-section">
                            <h2>2. Scan History</h2>
                            <p>For registered users, we store your scan history to help you keep track of your security activity. This data is encrypted and only accessible to you.</p>
                        </div>

                        <div class="policy-section">
                            <h2>3. AI Processing</h2>
                            <p>When you scan a job offer, the text is processed by our AI models. This data is used solely for identifying potential scams and improving our detection accuracy.</p>
                        </div>

                        <div class="policy-section">
                            <h2>4. Data Sharing</h2>
                            <p>We do not sell your personal data to third parties. We may share anonymized, aggregated scan data for security research purposes to improve the global fight against recruitment fraud.</p>
                        </div>

                        <div class="policy-section text-center pt-4">
                            <hr class="opacity-10 mb-4">
                            <p class="small text-white-50">Last updated: April 7, 2026</p>
                            <a href="index.php" class="btn btn-outline-info rounded-pill px-4">Return Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
