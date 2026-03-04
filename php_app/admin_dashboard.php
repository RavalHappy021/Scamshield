<?php
include "navbar.php";
include "db.php";
include "stats_helper.php";

// Admin access check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php"); // Redirect regular users to their own dashboard
    exit();
}

$global_stats = getStats($conn);
$admin_stats = getAdminStats($conn);

// Total checks and scams for the main cards
$total_checks = $global_stats['total_checks'];
$total_scams = $global_stats['total_scams'];
$total_real = $global_stats['total_real'];
$total_users = $global_stats['total_users'];

// Calculate scam rate for the whole platform
$scam_rate = ($total_checks > 0) 
    ? round(($total_scams / $total_checks) * 100) 
    : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | ScamShield</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --accent-blue: #00d2ff;
            --accent-gradient: linear-gradient(45deg, #00d2ff, #3a7bd5);
            --admin-accent: #ff007c; /* Pinkish accent for admin distinguishability */
            --admin-gradient: linear-gradient(45deg, #ff007c, #ff8c00);
        }

        body {
            background-color: #0c151b;
            color: #e0e0e0;
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        .dashboard-wrapper {
            padding: 40px 0;
        }

        .welcome-section {
            margin-bottom: 40px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            border-color: rgba(0, 210, 255, 0.3);
        }

        .admin-card {
            border-color: rgba(255, 0, 124, 0.2);
        }

        .admin-card:hover {
             border-color: rgba(255, 0, 124, 0.4);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-icon.admin-icon {
            background: var(--admin-gradient);
            -webkit-background-clip: text;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: rgba(255,255,255,0.5);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        .table-glass {
            background: transparent;
            color: #e0e0e0;
        }

        .table-glass thead th {
            border-bottom: 1px solid var(--glass-border);
            color: var(--accent-blue);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 15px;
        }

        .table-glass tbody td {
            border-bottom: 1px solid rgba(255,255,255,0.03);
            padding: 15px;
            vertical-align: middle;
        }

        .badge-status {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-fake { background: rgba(231, 76, 60, 0.2); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.3); }
        .badge-real { background: rgba(46, 204, 113, 0.2); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.3); }

        .admin-badge {
            background: rgba(255, 0, 124, 0.1);
            color: var(--admin-accent);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            border: 1px solid rgba(255, 0, 124, 0.2);
        }

        .quick-action-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: white;
            border-radius: 15px;
            padding: 15px 25px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            margin-bottom: 15px;
        }

        .quick-action-btn:hover {
            background: var(--admin-gradient);
            color: white;
            transform: scale(1.02);
            border-color: transparent;
        }

        .quick-action-btn i {
            font-size: 1.5rem;
            margin-right: 15px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: var(--accent-gradient);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: 700;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container dashboard-wrapper">
    <!-- Admin Header -->
    <div class="row welcome-section animate__animated animate__fadeInDown">
        <div class="col-md-8">
            <h1 class="fw-bold"><i class="fa-solid fa-crown text-info me-2"></i>Admin <span class="text-info">Overview</span></h1>
            <p class="text-white-50">Global oversight of ScamShield activity, users, and performance.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="admin-badge">
                <i class="fa-solid fa-lock me-2"></i>
                Administrative Control Mode
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="glass-card text-center admin-card">
                <div class="stat-icon admin-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-value"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="glass-card text-center">
                <div class="stat-icon"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
                <div class="stat-value"><?php echo $total_checks; ?></div>
                <div class="stat-label">Global Checks</div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="glass-card text-center">
                <div class="stat-icon text-danger"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="stat-value text-danger"><?php echo $total_scams; ?></div>
                <div class="stat-label">Scams Detected</div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="glass-card text-center">
                <div class="stat-icon text-warning"><i class="fa-solid fa-chart-line"></i></div>
                <div class="stat-value text-warning"><?php echo $scam_rate; ?>%</div>
                <div class="stat-label">Scam Threat Level</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Global Recent Activity -->
        <div class="col-lg-8 animate__animated animate__fadeInLeft">
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-globe me-2 text-info"></i>Global Recent Activity</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-glass">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Job Preview</th>
                                <th>Result</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($admin_stats['global_activity']->num_rows > 0): ?>
                                <?php while($row = $admin_stats['global_activity']->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar text-white"><?php echo strtoupper(substr($row['user_name'] ?? 'G', 0, 1)); ?></div>
                                            <span class="small fw-bold"><?php echo htmlspecialchars($row['user_name'] ?? 'Guest'); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate small opacity-75" style="max-width: 200px;">
                                            <?php echo htmlspecialchars($row['job_text']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($row['result'] == 'Fake'): ?>
                                            <span class="badge-status badge-fake">FAKE</span>
                                        <?php else: ?>
                                            <span class="badge-status badge-real">REAL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small opacity-50">
                                        <?php echo date('M d, H:i', strtotime($row['created_at'])); ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-white-50">No activity recorded.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Admin Quick Actions & Rankings -->
        <div class="col-lg-4 animate__animated animate__fadeInRight">
            <div class="mb-4">
                <h5 class="fw-bold mb-3">Admin Actions</h5>
                <a href="admin_messages.php" class="quick-action-btn">
                    <i class="fa-solid fa-envelope-open-text"></i>
                    <div>
                        <div class="fw-bold">Review Messages</div>
                        <small class="opacity-75">Customer support inbox</small>
                    </div>
                </a>
               
            </div>

            <div class="glass-card" style="padding: 20px;">
                <h6 class="fw-bold mb-3 text-info"><i class="fa-solid fa-medal me-2"></i>Most Active Protectors</h6>
                <div class="ranking-list">
                    <?php 
                    $rank = 1;
                    while($user = $admin_stats['top_users']->fetch_assoc()): 
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <span class="me-3 fw-bold opacity-50">#<?php echo $rank++; ?></span>
                            <span class="small"><?php echo htmlspecialchars($user['name']); ?></span>
                        </div>
                        <span class="badge bg-info-subtle text-info rounded-pill px-3"><?php echo $user['check_count']; ?> checks</span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
