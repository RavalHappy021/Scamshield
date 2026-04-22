<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

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

        <?php if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){ ?>
        <li class="nav-item">
          <a class="nav-link" href="contact.php">Contact Us</a>
        </li>
        <?php } ?>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){ ?>
        <li class="nav-item">
          <a class="nav-link" href="admin_messages.php"><i class="fa-solid fa-inbox me-1"></i>Inbox</a>
        </li>
        <?php } ?>

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

