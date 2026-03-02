<?php 
include "navbar.php";
include "db.php";

$message_sent = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
        $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
        if (mysqli_query($conn, $sql)) {
            $message_sent = true;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | ScamShield</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .contact-section {
            padding: 80px 0;
            flex: 1;
            background: radial-gradient(circle at top right, rgba(0, 210, 255, 0.05), transparent),
                        radial-gradient(circle at bottom left, rgba(58, 123, 213, 0.05), transparent);
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: white;
            border-radius: 12px;
            padding: 12px 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-blue);
            box-shadow: 0 0 15px rgba(0, 210, 255, 0.2);
            color: white;
        }

        .btn-premium {
            background: var(--accent-gradient);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
            width: 100%;
            margin-top: 20px;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 210, 255, 0.3);
        }

        .contact-info-item {
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: rgba(0, 210, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: var(--accent-blue);
            font-size: 20px;
        }

        .text-gradient {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>

    <div class="contact-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="row g-5 align-items-center">
                        <div class="col-md-5 animate__animated animate__fadeInLeft">
                            <h1 class="fw-bold mb-4">Get in <span class="text-gradient">Touch</span></h1>
                            <p class="text-white-50 mb-5">Have questions about ScamShield or found a suspicious job offer? Our team is here to help you stay safe.</p>
                            
                            <div class="contact-info-item">
                                <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
                                <div>
                                    <div class="small text-white-50">Email us at</div>
                                    <div class="fw-bold">support@scamshield.com</div>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
                                <div>
                                    <div class="small text-white-50">Main Office</div>
                                    <div class="fw-bold">Global Security Hub, Digital City</div>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-icon"><i class="fa-solid fa-headset"></i></div>
                                <div>
                                    <div class="small text-white-50">Support Hours</div>
                                    <div class="fw-bold">24/7 AI-Powered Support</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7 animate__animated animate__fadeInRight">
                            <div class="glass-card">
                                <?php if($message_sent): ?>
                                    <div class="text-center py-5">
                                        <div class="display-1 text-success mb-4"><i class="fa-solid fa-circle-check"></i></div>
                                        <h3 class="fw-bold">Message Sent!</h3>
                                        <p class="text-white-50">Thank you for reaching out. Our team will get back to you shortly.</p>
                                        <a href="index.php" class="btn btn-outline-light rounded-pill mt-3 px-4">Back to Home</a>
                                    </div>
                                <?php else: ?>
                                    <?php if($error_message): ?>
                                        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 mb-4">
                                            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $error_message; ?>
                                        </div>
                                    <?php endif; ?>

                                    <form action="contact.php" method="POST">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-white-50">Full Name</label>
                                                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-white-50">Email Address</label>
                                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold text-white-50">Subject</label>
                                                <input type="text" name="subject" class="form-control" placeholder="How can we help?" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold text-white-50">Message</label>
                                                <textarea name="message" class="form-control" rows="5" placeholder="Your message here..." required></textarea>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn-premium">Send Message <i class="fa-solid fa-paper-plane ms-2"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
