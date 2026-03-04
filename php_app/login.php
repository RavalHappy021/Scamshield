<?php 
include "navbar.php";
include "db.php";

$msg = "";

if(isset($_POST['login'])){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // username OR email se check karo
    $query = mysqli_query($conn,
        "SELECT * FROM users WHERE name='$username' OR email='$username'"
    );

    if(mysqli_num_rows($query) == 1){

        $row = mysqli_fetch_assoc($query);

        // password verify
        if(password_verify($password, $row['password'])){

            // ✅ Store user data in session
            $_SESSION['user_id'] = $row['id'];      // important for history
            $_SESSION['user'] = $row['name'];       // for display
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];       // Store user role

            // ✅ Redirect based on role
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();

        } else {
            $msg = "Invalid Password!";
        }

    } else {
        $msg = "User not found!";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>ScamShield - Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            padding: 80px 0;
        }
        .login-card {
            width: 420px;
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
<body>

<div class="page-wrapper">
    <div class="card shadow login-card animate__animated animate__fadeIn">

        <div class="login-header">
            <h3>🛡 ScamShield Login</h3>
            <p class="mb-0">Secure access to your account</p>
        </div>

        <div class="card-body p-4 text-white">

            <?php if($msg!=""){ ?>
                <div class="alert alert-danger text-center">
                    <?php echo $msg; ?>
                </div>
            <?php } ?>

            <form method="post">

                <div class="mb-3">
                    <label class="form-label text-white">Username or Email</label>
                    <input type="text" name="username" class="form-control"
                           placeholder="Enter your username or email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Password</label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Enter your password" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">

                    <a href="register.php" class="btn btn-outline-primary btn-register">
                        <i class="fa-solid fa-user-plus"></i> Register
                    </a>

                    <button type="submit" name="login" class="btn btn-login text-white shadow">
                        <i class="fa-solid fa-right-to-bracket"></i> Login
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

</body>
</html>
