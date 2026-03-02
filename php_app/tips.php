<?php 
include "navbar.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safety Tips | ScamShield</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #0f2027;
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
        }
        .tips-hero {
            padding: 80px 0;
            background: linear-gradient(rgba(15, 32, 39, 0.8), rgba(15, 32, 39, 0.8)),
                        url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
        }
        .tip-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
            transition: all 0.3s;
        }
        .tip-card:hover {
            transform: translateX(10px);
            background: rgba(255, 255, 255, 0.08);
            border-color: #00d2ff;
        }
        .tip-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #00d2ff, #3a7bd5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .badge-scam {
            background: rgba(220, 53, 69, 0.2);
            color: #ff4d5e;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <header class="tips-hero text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Job Safety <span style="color: #00d2ff;">Guidelines</span></h1>
            <p class="lead text-white-50">Learn how to identify and avoid recruitment fraud like a pro.</p>
        </div>
    </header>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h3 class="mb-4">Common Red Flags 🚩</h3>
                
                <div class="tip-card">
                    <div class="tip-number">1</div>
                    <h5>Request for Payment or Fees</h5>
                    <p class="text-white-50">Legitimate companies will NEVER ask you to pay for training, equipment, or "application processing fees." If they ask for money, it's a scam.</p>
                    <span class="badge-scam">CRITICAL WARNING</span>
                </div>

                <div class="tip-card">
                    <div class="tip-number">2</div>
                    <h5>Generic Email Addresses</h5>
                    <p class="text-white-50">Professional recruiters use company domains (e.g., career@google.com). Be wary of "official" offers from @gmail.com, @outlook.com, or @yahoo.com.</p>
                </div>

                <div class="tip-card">
                    <div class="tip-number">3</div>
                    <h5>Urgent or Pressure Tactics</h5>
                    <p class="text-white-50">Scammers create a false sense of urgency ("Hire immediately," "Only 2 slots left"). Real hiring processes take time for interviews and background checks.</p>
                </div>

                <div class="tip-card">
                    <div class="tip-number">4</div>
                    <h5>WhatsApp-Only Communication</h5>
                    <p class="text-white-50">While some companies use chat for coordination, an entire hiring process conducted solely over WhatsApp or Telegram without a video call or in-person meeting is highly suspicious.</p>
                </div>

                <div class="tip-card">
                    <div class="tip-number">5</div>
                    <h5>Vague Job Descriptions</h5>
                    <p class="text-white-50">Beware of jobs that offer "High pay for little work" or don't require specific skills. If it sounds too good to be true, it usually is.</p>
                </div>

                <div class="mt-5 p-4 rounded-4" style="background: rgba(0, 210, 255, 0.1); border: 1px solid rgba(0, 210, 255, 0.3);">
                    <h4 class="text-info"><i class="fa-solid fa-shield-halved me-2"></i> Always Verify</h4>
                    <p class="mb-0">Before accepting any offer, visit the company's official website directly and check their "Careers" section to ensure the job listing exists.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 text-center border-top border-secondary mt-5">
        <p class="text-white-50 small mb-0">© 2026 ScamShield Project. Stay Safe.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
