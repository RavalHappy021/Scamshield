<?php 
include "navbar.php";
include "stats_helper.php";
$stats = getStats($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScamShield | Professional AI Job Protection</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --primary-bg: #0f2027;
            --accent-blue: #00d2ff;
            --accent-gradient: linear-gradient(135deg, #00d2ff, #3a7bd5);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: var(--primary-bg);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        /* --- HERO SECTION --- */
        .hero-section {
            min-height: 90vh;
            display: flex;
            align-items: center;
            background: radial-gradient(circle at top right, rgba(0, 210, 255, 0.1), transparent),
                        radial-gradient(circle at bottom left, rgba(58, 123, 213, 0.1), transparent);
            position: relative;
        }

        .hero-title {
            font-size: 4.5rem;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -2px;
            margin-bottom: 20px;
        }

        .text-gradient {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-lead {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 35px;
            max-width: 600px;
        }

        .btn-premium {
            background: var(--accent-gradient);
            color: white;
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(0, 210, 255, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .btn-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 210, 255, 0.4);
            color: white;
        }

        /* --- STATS CARD --- */
        .stats-grid {
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-blue);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: block;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        /* --- FEATURES SECTION --- */
        .section-padding {
            padding: 100px 0;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 50px;
            text-align: center;
        }

        .feature-box {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            height: 100%;
            transition: all 0.3s;
        }

        .feature-box:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: scale(1.02);
        }

        .feature-icon-wrapper {
            width: 70px;
            height: 70px;
            background: rgba(0, 210, 255, 0.1);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
        }

        .feature-icon-wrapper i {
            font-size: 30px;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* --- INFO SECTION --- */
        .info-img-wrapper {
            position: relative;
        }

        .info-img-wrapper img {
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .info-badge {
            position: absolute;
            bottom: 30px;
            right: 30px;
            background: var(--accent-gradient);
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        /* --- FOOTER --- */
        footer {
            background: #0a1114;
            padding: 60px 0 30px;
            border-top: 1px solid var(--glass-border);
        }

        .footer-logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
            display: block;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: color 0.3s;
            display: block;
            margin-bottom: 10px;
        }

        .footer-link:hover {
            color: var(--accent-blue);
        }

        .social-btn {
            width: 40px;
            height: 40px;
            background: var(--glass-bg);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 10px;
            transition: all 0.3s;
        }

        .social-btn:hover {
            background: var(--accent-gradient);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>

    <!-- HERO SECTION -->
    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 animate__animated animate__fadeInLeft">
                    <h1 class="hero-title">Secure Your <br><span class="text-gradient">Professional</span> Future.</h1>
                    <p class="hero-lead">AI-powered job scam detection designed to protect you from fraudulent offers and career traps.</p>
                    <div class="d-flex gap-3">
                        <a href="check_job.php" class="btn-premium">Scan a Job Offer</a>
                        <a href="tips.php" class="btn btn-outline-light rounded-pill px-4 py-3 fw-bold">Learn Safety Tips</a>
                    </div>
                </div>
                <div class="col-lg-5 offset-lg-1 d-none d-lg-block animate__animated animate__zoomIn">
                    <div class="info-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Security" class="img-fluid">
                        <div class="info-badge">
                            <span class="fw-bold">AI Active ✅</span>
                            <div class="small">Real-time analysis</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- STATS GRID -->
    <section class="container stats-grid">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card animate__animated animate__fadeInUp">
                    <span class="stat-number text-gradient"><?php echo number_format($stats['total_checks']); ?></span>
                    <span class="stat-label">Jobs Analyzed</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card animate__animated animate__fadeInUp animate__delay-1s">
                    <span class="stat-number text-gradient text-danger"><?php echo number_format($stats['total_scams']); ?></span>
                    <span class="stat-label">Scams Detected</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card animate__animated animate__fadeInUp animate__delay-2s">
                    <span class="stat-number text-gradient text-success"><?php echo number_format($stats['total_real']); ?></span>
                    <span class="stat-label">Safe Offers Verified</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card animate__animated animate__fadeInUp animate__delay-3s">
                    <span class="stat-number text-gradient"><?php echo number_format($stats['total_users']); ?></span>
                    <span class="stat-label">Protected Users</span>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="section-padding">
        <div class="container">
            <h2 class="section-title">Why Choose <span class="text-gradient">ScamShield?</span></h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon-wrapper">
                            <i class="fa-solid fa-brain"></i>
                        </div>
                        <h4>AI Analysis</h4>
                        <p class="text-white-50">Advanced pattern recognition trained on thousands of confirmed scam and legitimate job offers.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon-wrapper">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h4>Instant Results</h4>
                        <p class="text-white-50">Just paste the text and get a prediction in seconds with a clear confidence score from our ML model.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon-wrapper">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <h4>Full History</h4>
                        <p class="text-white-50">Keep track of every job you've scanned. View your personal check history anytime in a clean dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="section-padding bg-dark">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">Ready to scan your first job?</h2>
            <p class="lead text-white-50 mb-5">Join thousands of users who have secured their career path with our AI detection.</p>
            <a href="register.php" class="btn-premium px-5">Get Started for Free</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <a href="#" class="footer-logo">🛡 ScamShield</a>
                    <p class="text-white-50">ScamShield is an open-source initiative dedicated to reducing recruitment fraud through artificial intelligence.</p>
                    <div class="mt-4">
                        <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold text-uppercase mb-4">Quick Links</h6>
                    <a href="index.php" class="footer-link">Home</a>
                    <a href="check_job.php" class="footer-link">Check Job</a>
                    <a href="history.php" class="footer-link">Scan History</a>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h6 class="fw-bold text-uppercase mb-4">Resources</h6>
                    <a href="tips.php" class="footer-link">Safety Guidelines</a>
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms of Service</a>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h6 class="fw-bold text-uppercase mb-4">Support</h6>
                    <a href="#" class="footer-link">Help Center</a>
                    <a href="#" class="footer-link">Contact Us</a>
                    <a href="#" class="footer-link">API Documentation</a>
                </div>
            </div>
            <div class="text-center mt-5 pt-4 border-top border-secondary">
                <p class="small text-white-50 mb-0">© 2026 ScamShield. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
