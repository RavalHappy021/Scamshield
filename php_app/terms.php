<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// No DB logic needed for terms, but we need session for navbar
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service | ScamShield</title>
    
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
        .terms-section {
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
                        <h1 class="display-4 fw-bold mb-4 text-gradient">Terms of Service</h1>
                        <p class="lead text-white-50 mb-5">By using ScamShield, you agree to these terms. Please read them carefully.</p>
                        
                        <div class="terms-section">
                            <h2>1. Acceptance of Terms</h2>
                            <p>By accessing or using our platform, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.</p>
                        </div>

                        <div class="terms-section">
                            <h2>2. Lawful Use</h2>
                            <p>You agree to use ScamShield only for lawful purposes related to scanning job offers you have received or are interested in. Prohibited activities include attempting to breach our security or manipulate scan results.</p>
                        </div>

                        <div class="terms-section">
                            <h2>3. AI Results Disclaimer</h2>
                            <p>ScamShield provides AI-driven predictions based on advanced machine learning models. While highly accurate, these results are for informational purposes only. We are not liable for decisions made based on AI predictions.</p>
                        </div>

                        <div class="terms-section">
                            <h2>4. User Accounts</h2>
                            <p>You are responsible for maintaining the confidentiality of your account credentials. You must notify us immediately of any unauthorized use of your account.</p>
                        </div>

                        <div class="terms-section text-center pt-4">
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
