<?php 
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
        $intended_role = $_POST['login_role'] ?? 'user';

        // Check if user is trying to login through Admin Portal but is not an admin
        if ($intended_role === 'admin' && $row['role'] !== 'admin') {
            $msg = "Access Denied: You do not have administrator privileges!";
        } 
        // Check if admin is trying to login through User Access
        else if ($intended_role === 'user' && $row['role'] === 'admin') {
            $msg = "Admins must use the Admin Portal tab!";
        }
        // Password verify
        else if(password_verify($password, $row['password'])){

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScamShield - Login</title>
    
    <?php include "header_imports.php"; ?>

    <style>
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
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-register:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #00d2ff;
        }

        /* Admin Mode Styling */
        .admin-mode .login-header {
            background: linear-gradient(45deg, #ff007c, #ff8c00);
        }
        .admin-mode .btn-login {
            background: linear-gradient(45deg, #ff007c, #ff8c00);
        }
        .admin-mode .form-control:focus {
            border-color: #ff007c;
        }

        .login-tabs {
            display: flex;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .login-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.4);
            transition: all 0.3s;
        }
        .login-tab.active {
            color: white;
            background: rgba(255,255,255,0.02);
            border-bottom: 2px solid #00d2ff;
        }
        .admin-mode .login-tab.active {
            border-bottom-color: #ff007c;
        }
    </style>

</head>
<body>
<?php include "navbar.php"; ?>

<div class="page-wrapper" id="loginWrapper">
    <div class="card shadow login-card animate__animated animate__fadeIn" id="loginCard">

        <div class="login-tabs">
            <div class="login-tab active" onclick="setLoginMode('user')">User Access</div>
            <div class="login-tab" onclick="setLoginMode('admin')">Admin Portal</div>
        </div>

        <div class="login-header">
            <h3 id="loginTitle">🛡 ScamShield Login</h3>
            <p class="mb-0" id="loginSubtitle">Secure access to your account</p>
        </div>

        <div class="card-body p-4 text-white">

            <?php if($msg!=""){ ?>
                <div class="alert alert-danger text-center">
                    <?php echo $msg; ?>
                </div>
            <?php } ?>

            <form method="post">
                <!-- Hidden field to track User vs Admin mode -->
                <input type="hidden" name="login_role" id="loginRole" value="user">

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

                    <a href="register.php" class="btn-register">
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

    <script>
        function setLoginMode(mode) {
            const card = document.getElementById('loginCard');
            const title = document.getElementById('loginTitle');
            const subtitle = document.getElementById('loginSubtitle');
            const tabs = document.querySelectorAll('.login-tab');
            const roleInput = document.getElementById('loginRole');

            tabs.forEach(t => t.classList.remove('active'));

            if (mode === 'admin') {
                card.classList.add('admin-mode');
                title.innerHTML = '<i class="fa-solid fa-crown me-2"></i>Admin Login';
                subtitle.innerText = 'Platform Management Access';
                tabs[1].classList.add('active');
                roleInput.value = 'admin';
            } else {
                card.classList.remove('admin-mode');
                title.innerHTML = '🛡 ScamShield Login';
                subtitle.innerText = 'Secure access to your account';
                tabs[0].classList.add('active');
                roleInput.value = 'user';
            }
        }
    </script>
</body>
</html>
