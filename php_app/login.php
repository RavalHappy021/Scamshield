<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once "db.php";

$msg = "";

if (!$conn) {
    $msg = "Database connection failed. Please check your credentials.";
}

if(isset($_POST['login']) && $conn){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $login_type = isset($_POST['login_type']) ? $_POST['login_type'] : 'user';

    // Restriction for Admin Login
    if ($login_type === 'admin' && strtolower($username) !== 'happy raval') {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: 'Only Admin can use this!',
                    background: '#1a1a1a',
                    color: '#ffffff',
                    confirmButtonColor: '#00d2ff'
                });
            });
        </script>";
    } else {
        // username OR email se check karo
        $query = mysqli_query($conn,
            "SELECT * FROM users WHERE name='$username' OR email='$username'"
        );

        if($query && mysqli_num_rows($query) == 1){

            $row = mysqli_fetch_assoc($query);

            // password verify
            if(password_verify($password, $row['password'])){

                // ✅ Store user data in session
                $_SESSION['user_id'] = $row['id'];      // important for history
                $_SESSION['user'] = $row['name'];       // for display
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];       // Store user role

                if ($login_type === 'admin') {
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
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScamShield - Login</title>
    
    <?php include "header_assets.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        
        /* Tabs Styling */
        .login-tabs {
            display: flex;
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 600;
            border-bottom: 2px solid transparent;
        }
        .login-tab.active {
            color: #00d2ff;
            border-bottom: 2px solid #00d2ff;
            background: rgba(0, 210, 255, 0.05);
        }
        .login-tab:hover:not(.active) {
            color: white;
            background: rgba(255, 255, 255, 0.02);
        }
    </style>

</head>
<body>
    <?php include "navbar.php"; ?>

<div class="page-wrapper">
    <div class="card shadow login-card animate__animated animate__fadeIn">

        <div class="login-header">
            <h3 id="form-title">🛡 ScamShield Login</h3>
            <p id="form-subtitle" class="mb-0">Secure access to your account</p>
        </div>

        <div class="login-tabs">
            <div class="login-tab active" onclick="switchLogin('user')">
                <i class="fa-solid fa-user me-2"></i>User
            </div>
            <div class="login-tab" onclick="switchLogin('admin')">
                <i class="fa-solid fa-user-shield me-2"></i>Admin
            </div>
        </div>

        <div class="card-body p-4 text-white">

            <?php if($msg!=""){ ?>
                <div class="alert alert-danger text-center">
                    <?php echo $msg; ?>
                </div>
            <?php } ?>

            <form method="post" id="loginForm">
                <input type="hidden" name="login_type" id="login_type" value="user">

                <div class="mb-3">
                    <label class="form-label text-white" id="username-label">Username or Email</label>
                    <input type="text" name="username" class="form-control"
                           placeholder="Enter your username or email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Password</label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Enter your password" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">

                    <a href="register.php" class="btn btn-outline-primary btn-register" id="register-btn">
                        <i class="fa-solid fa-user-plus"></i> Register
                    </a>

                    <button type="submit" name="login" class="btn btn-login text-white shadow">
                        <i class="fa-solid fa-right-to-bracket"></i> Login
                    </button>

                </div>

            </form>
            
            <script>
                function switchLogin(type) {
                    const tabs = document.querySelectorAll('.login-tab');
                    const formTitle = document.getElementById('form-title');
                    const formSubtitle = document.getElementById('form-subtitle');
                    const loginTypeInput = document.getElementById('login_type');
                    const registerBtn = document.getElementById('register-btn');
                    const usernameLabel = document.getElementById('username-label');

                    tabs.forEach(tab => tab.classList.remove('active'));
                    
                    if (type === 'admin') {
                        tabs[1].classList.add('active');
                        formTitle.innerHTML = '🛡 Admin Access';
                        formSubtitle.innerHTML = 'Restricted to Administrative login only';
                        loginTypeInput.value = 'admin';
                        registerBtn.style.display = 'none';
                        usernameLabel.innerHTML = 'Admin Username';
                    } else {
                        tabs[0].classList.add('active');
                        formTitle.innerHTML = '🛡 ScamShield Login';
                        formSubtitle.innerHTML = 'Secure access to your account';
                        loginTypeInput.value = 'user';
                        registerBtn.style.display = 'inline-block';
                        usernameLabel.innerHTML = 'Username or Email';
                    }
                }
            </script>

        </div>

    </div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

</body>
</html>
