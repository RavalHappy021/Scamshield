<?php 
include "navbar.php";
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password != $confirm) {
        $message = "Passwords do not match!";
    } else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT * FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $message = "Email already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");
            $stmt->bind_param("sss", $username, $email, $hash);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $message = "Registration failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ScamShield Register</title>

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
            padding: 50px 0;
        }
        .login-card {
            width: 450px;
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
            padding: 30px;
            text-align: center;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px;
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
        .btn-register {
            border-radius: 12px;
            padding: 12px 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }
        .btn-register:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #00d2ff;
        }
    </style>
</head>

<div class="page-wrapper">
<div class="card login-card animate__animated animate__fadeIn">

    <div class="login-header">
        <h4>🛡 ScamShield Register</h4>
        <small>Create your secure account</small>
    </div>

    <div class="card-body p-4 text-white">

        <?php if($message!=""): ?>
            <div class="alert alert-danger">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="post">

            <div class="mb-3">
                <label class="form-label text-white">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Create password" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="login.php" class="btn btn-outline-primary btn-register">
                    👤 Login here
                </a>

                <button type="submit" class="btn btn-login shadow">
                    ➕ Register
                </button>
            </div>

        </form>

    </div>
</div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
