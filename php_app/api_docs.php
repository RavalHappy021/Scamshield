<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// No DB logic needed for API docs, but we need session for navbar
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation | ScamShield</title>
    
    <?php include "header_assets.php"; ?>
    <style>
        :root {
            --primary-bg: #0f2027;
            --accent-blue: #00d2ff;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --code-bg: #1a1a1a;
        }
        body {
            background-color: var(--primary-bg);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
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
            margin-bottom: 40px;
        }
        code, pre {
            background: var(--code-bg);
            color: var(--accent-blue);
            padding: 5px 10px;
            border-radius: 8px;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
        }
        pre {
            padding: 20px;
            overflow-x: auto;
            border-left: 4px solid var(--accent-blue);
        }
        .endpoint-badge {
            background: rgba(0, 210, 255, 0.1);
            color: var(--accent-blue);
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.8rem;
            margin-right: 15px;
        }
        .method-badge {
            background: #2ecc71;
            color: white;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 800;
            margin-right: 15px;
        }
        h2 { font-weight: 700; margin-bottom: 25px; }
        .text-gradient {
            background: linear-gradient(135deg, #00d2ff, #3a7bd5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="content-wrap">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="glass-card animate__animated animate__fadeIn">
                        <h1 class="display-4 fw-bold mb-4 text-gradient">API Documentation</h1>
                        <p class="lead text-white-50 mb-5">Integrate ScamShield's powerful detection capabilities into your own applications via our RESTful API.</p>
                        
                        <div class="api-section mb-5">
                            <h2>1. Detect Scam in Job Text</h2>
                            <div class="d-flex align-items-center mb-3">
                                <span class="method-badge">POST</span>
                                <span class="endpoint-badge">/predict</span>
                            </div>
                            <p>Submit job description text for real-time analysis against our scam detection models.</p>
                            <h6 class="fw-bold mt-4 mb-2 small text-uppercase">Request Body</h6>
<pre>
{
  "text": "Paste your job description text here..."
}
</pre>
                            <h6 class="fw-bold mt-4 mb-2 small text-uppercase">Response Example</h6>
<pre>
{
  "prediction": "Fake",
  "confidence_score": 98.4,
  "detected_patterns": ["High salary, low requirements", "External link request"]
}
</pre>
                        </div>

                        <div class="api-section mb-5">
                            <h2>2. OCR Job Scanning</h2>
                            <div class="d-flex align-items-center mb-3">
                                <span class="method-badge">POST</span>
                                <span class="endpoint-badge">/ocr</span>
                            </div>
                            <p>Scan a job offer directly from an image. Our OCR parses text and then performs the scam check.</p>
                            <h6 class="fw-bold mt-4 mb-2 small text-uppercase">Request Multipart</h6>
<pre>
curl -F "file=@job_image.jpg" http://scamshield-api.com/ocr
</pre>
                        </div>

                        <div class="section-footer text-center pt-5">
                            <p class="small text-white-50">Looking for custom enterprise integration?</p>
                            <a href="contact.php" class="btn btn-info rounded-pill px-5 py-3 fw-bold">Contact Our API Team</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
