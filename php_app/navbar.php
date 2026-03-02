<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Google Fonts & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
    --nav-bg: rgba(15, 32, 39, 0.85);
    --accent-blue: #00d2ff;
    --accent-gradient: linear-gradient(45deg, #00d2ff, #3a7bd5);
}

body {
    font-family: 'Outfit', sans-serif;
}

.navbar {
    background: var(--nav-bg) !important;
    backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 15px 0;
    transition: all 0.3s;
}

.navbar-brand {
    font-weight: 700;
    font-size: 1.5rem;
    letter-spacing: -0.5px;
    background: var(--accent-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.nav-link {
    font-weight: 500;
    margin: 0 5px;
    transition: color 0.3s;
}

.nav-link:hover {
    color: var(--accent-blue) !important;
}

.welcome-box {
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    background: var(--accent-gradient);
    padding: 8px 18px;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);
    margin-right: 15px;
    cursor: pointer;
    display: inline-block;
}

.welcome-box b {
    color: #ffffff;
    text-shadow: 0 0 5px rgba(255,255,255,0.5);
}

.dropdown-menu {
    background: var(--nav-bg);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    margin-top: 0;
}

.dropdown-item {
    color: white;
    font-weight: 500;
}

.dropdown-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: var(--accent-blue);
}

.btn-auth {
    background: var(--accent-gradient);
    border: none;
    border-radius: 50px;
    padding: 8px 20px;
    font-weight: 600;
    color: white !important;
}
</style>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">🛡 ScamShield</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-center">

        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
        </li>

        <?php if(isset($_SESSION['user'])){ ?>
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">Dashboard</a>
        </li>
        <?php } ?>

        <li class="nav-item">
          <a class="nav-link" href="check_job.php">Check Job</a>
        </li>

        <?php if(isset($_SESSION['user'])){ ?>
        <li class="nav-item">
          <a class="nav-link" href="history.php">History</a>
        </li>
        <?php } ?>

        <li class="nav-item">
          <a class="nav-link" href="tips.php">Safety Tips</a>
        </li>

        <?php if(isset($_SESSION['user'])){ ?>
          <li class="nav-item dropdown ms-lg-3">
            <button class="welcome-box dropdown-toggle border-0" id="userDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              👋 Hi, <b><?php echo htmlspecialchars($_SESSION['user']); ?></b>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">
              <li><a class="dropdown-item py-2" href="dashboard.php"><i class="fa-solid fa-gauge-high me-2 text-info"></i>Dashboard</a></li>
              <li><a class="dropdown-item py-2" href="history.php"><i class="fa-solid fa-list-ul me-2 text-primary"></i>My History</a></li>
              <li><hr class="dropdown-divider opacity-50"></li>
              <li><a class="dropdown-item text-danger py-2" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
            </ul>
          </li>
        <?php } else { ?>
            <li class="nav-item">
              <a class="nav-link" href="login.php">Login</a>
            </li>
            <li class="nav-item ms-lg-2">
              <a class="nav-link btn-auth" href="register.php">Register</a>
            </li>
        <?php } ?>

      </ul>
    </div>
  </div>
</nav>

