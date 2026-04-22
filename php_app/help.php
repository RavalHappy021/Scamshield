<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// No DB logic needed for help, but we need session for navbar
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center | ScamShield</title>
    
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
        }
        .hero-section {
            padding: 100px 0 60px;
            background: radial-gradient(circle at center, rgba(0, 210, 255, 0.05), transparent);
            text-align: center;
        }
        .faq-section {
            padding-bottom: 80px;
        }
        .accordion-item {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px !important;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .accordion-button {
            background: transparent;
            color: white;
            padding: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            box-shadow: none !important;
        }
        .accordion-button:not(.collapsed) {
            background: rgba(0, 210, 255, 0.05);
            color: var(--accent-blue);
        }
        .accordion-button::after {
            filter: brightness(0) invert(1);
        }
        .accordion-body {
            color: rgba(255, 255, 255, 0.7);
            padding: 0 25px 25px;
        }
        .help-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
        }
        .help-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-blue);
        }
        .help-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #00d2ff, #3a7bd5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>
    <?php include "navbar.php"; ?>

    <div class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3 animate__animated animate__fadeInDown">How can we <span class="text-info">help?</span></h1>
            <p class="lead text-white-50 animate__animated animate__fadeInUp">Search our guide or browse frequently asked questions below.</p>
        </div>
    </div>

    <div class="container faq-section">
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="help-card animate__animated animate__fadeInLeft">
                    <div class="help-icon"><i class="fa-solid fa-rocket"></i></div>
                    <h4>Getting Started</h4>
                    <p class="text-white-50">New to ScamShield? Learn how to scan your first job offer.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="help-card animate__animated animate__fadeInUp">
                    <div class="help-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <h4>Security Guide</h4>
                    <p class="text-white-50">Deep dive into common scams and how our AI detects them.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="help-card animate__animated animate__fadeInRight">
                    <div class="help-icon"><i class="fa-solid fa-user-gear"></i></div>
                    <h4>Account Help</h4>
                    <p class="text-white-50">Manage your profile, history, and notification settings.</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-4 text-center">Frequently Asked Questions</h2>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                                How accurate is the scam detection?
                            </button>
                        </h2>
                        <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Our AI models are trained on thousands of confirmed scam and legitimate job offers, achieving over 95% accuracy in real-world testing. However, we always recommend caution and common sense.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                                What data do you need for a scan?
                            </button>
                        </h2>
                        <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Simply copy and paste the job description text into our scanner. Our AI analyzes patterns in wording, requirements, and contact information to identify potential fraud.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
                                Is my scan history private?
                            </button>
                        </h2>
                        <div id="q3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes. For registered users, all scan history is encrypted and linked only to your account. We use anonymized data only for improving our global security models.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    <p class="text-white-50 mb-4">Still have questions?</p>
                    <a href="contact.php" class="btn btn-info rounded-pill px-5 py-3 fw-bold">Contact Support</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
