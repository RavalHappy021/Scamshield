<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once "db.php";
include_once "stats_helper.php";

// Access Control: Only 'happy raval' with 'admin' role
if (!isset($_SESSION['user']) || strtolower($_SESSION['user']) !== 'happy raval' || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$global_stats = getStats($conn);

// Fetch recent users
$recent_users_query = $conn->query("SELECT * FROM users ORDER BY id DESC LIMIT 5");

// Fetch recent scans
$table = getHistoryTable($conn);
$recent_scans_query = $conn->query("SELECT j.*, u.name as user_name FROM $table j LEFT JOIN users u ON j.user_id = u.id ORDER BY j.created_at DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | ScamShield</title>
    
    <?php include "header_assets.php"; ?>
    <style>
        :root {
            --primary-bg: #0f2027;
            --accent-blue: #00d2ff;
            --accent-gradient: linear-gradient(135deg, #00d2ff, #3a7bd5);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --card-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        body {
            background-color: var(--primary-bg);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(0, 210, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(58, 123, 213, 0.05) 0%, transparent 50%);
        }

        .admin-container {
            padding: 40px 0;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 25px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--accent-blue);
            box-shadow: 0 15px 40px rgba(0, 210, 255, 0.2);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-value {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 5px;
            letter-spacing: -1px;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.5);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1.5px;
        }

        .table-custom {
            color: white !important;
            background: #111 !important;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table-custom thead th {
            border-bottom: 1px solid var(--glass-border);
            color: var(--accent-blue);
            text-transform: uppercase;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 15px;
            letter-spacing: 1px;
        }

        .table-custom tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s;
            color: white !important;
        }
        
        .table-custom td {
            color: white !important;
            background: transparent !important;
            padding: 18px 15px;
            vertical-align: middle;
        }

        .badge-admin {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-action {
            background: var(--accent-gradient);
            color: white;
            padding: 12px 25px;
            border-radius: 15px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 10px 20px rgba(0, 210, 255, 0.2);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0, 210, 255, 0.3);
            color: white;
        }

        .admin-header h1 {
            font-weight: 800;
            letter-spacing: -1.5px;
            margin-bottom: 10px;
            font-size: 2.5rem;
        }

        .admin-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.1rem;
        }

        .text-gradient {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>
    <?php include "navbar.php"; ?>

<div class="container admin-container">
    <!-- Header -->
    <div class="admin-header mb-5 animate__animated animate__fadeIn">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1>Welcome, Master Admin 👑</h1>
                <p>Hello <span class="text-info fw-bold">Happy Raval</span>, you have full control over the ScamShield Ecosystem.</p>
            </div>
            <div class="col-md-5 text-md-end">
                <a href="admin_messages.php" class="btn-action">
                    <i class="fa-solid fa-envelope-open-text me-2"></i> View Inbox Messages
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 animate__animated animate__zoomIn" style="animation-delay: 0.1s;">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-value"><?php echo $global_stats['total_users']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__zoomIn" style="animation-delay: 0.2s;">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <div class="stat-value"><?php echo $global_stats['total_checks']; ?></div>
                <div class="stat-label">All-time Scans</div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__zoomIn" style="animation-delay: 0.3s;">
            <div class="stat-card">
                <div class="stat-icon text-danger"><i class="fa-solid fa-virus-slash"></i></div>
                <div class="stat-value text-danger"><?php echo $global_stats['total_scams']; ?></div>
                <div class="stat-label">Scams Caught</div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__zoomIn" style="animation-delay: 0.4s;">
            <div class="stat-card">
                <div class="stat-icon text-success"><i class="fa-solid fa-handshake-angle"></i></div>
                <div class="stat-value text-success"><?php echo $global_stats['total_real']; ?></div>
                <div class="stat-label">Real Jobs Verified</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Scans -->
        <div class="col-lg-8">
            <div class="glass-panel animate__animated animate__fadeInLeft">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="m-0 fw-bold"><i class="fa-solid fa-bolt me-2 text-info"></i>Global Recent Activity</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-custom">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Job Brief</th>
                                <th>Scan Result</th>
                                                            <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($scan = $recent_scans_query->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold text-info"><?php echo htmlspecialchars($scan['user_name'] ?? 'Guest'); ?></td>
                                <td>
                                    <div class="text-truncate text-white-50" style="max-width: 250px;">
                                        <?php echo !empty($scan['job_text']) ? htmlspecialchars($scan['job_text']) : '<i class="opacity-50">No text provided</i>'; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($scan['result'] == 'Fake'): ?>
                                        <span class="badge bg-danger badge-admin">FAKE</span>
                                    <?php else: ?>
                                        <span class="badge bg-success badge-admin">REAL</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-info fw-bold">
                                    <i class="fa-regular fa-clock me-1 opacity-50"></i>
                                    <?php echo date('h:i A', strtotime($scan['created_at'])); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- System Overview -->
        <div class="col-lg-4">
            <div class="glass-panel animate__animated animate__fadeInRight">
                <h4 class="mb-4 fw-bold"><i class="fa-solid fa-users-gear me-2 text-info"></i>New Users</h4>
                <ul class="list-unstyled">
                    <?php while($user = $recent_users_query->fetch_assoc()): ?>
                    <li class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fa-solid fa-user text-white"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($user['name']); ?></h6>
                            <small class="text-white-50"><?php echo htmlspecialchars($user['email']); ?></small>
                        </div>
                        <div class="ms-auto">
                            <?php if($user['role'] == 'admin'): ?>
                                <span class="badge bg-warning text-dark badge-admin">Admin</span>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <hr class="my-4 opacity-10">
                <div class="text-center">
                    <p class="text-white-50 small mb-3">System status: <span class="text-success"><i class="fa-solid fa-circle-check me-1"></i>Operational</span></p>
                    <a href="index.php" class="text-info text-decoration-none small">Return to Homepage</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
