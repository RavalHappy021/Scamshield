<?php
include "navbar.php";
include "db.php";
include "stats_helper.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_stats = getUserStats($conn, $user_id);
$global_stats = getStats($conn);

// Calculate safety score (Example: (Real Jobs / Total Jobs) * 100)
$safety_score = ($user_stats['user_total'] > 0) 
    ? round(($user_stats['user_real'] / $user_stats['user_total']) * 100) 
    : 100;

$safety_class = 'text-success';
if($safety_score < 50) $safety_class = 'text-danger';
elseif($safety_score < 80) $safety_class = 'text-warning';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ScamShield</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --accent-blue: #00d2ff;
            --accent-gradient: linear-gradient(45deg, #00d2ff, #3a7bd5);
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

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
            background: var(--accent-gradient);
            color: white;
            transform: scale(1.02);
        }

        .quick-action-btn i {
            font-size: 1.5rem;
            margin-right: 15px;
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

        .impact-badge {
            background: rgba(0, 210, 255, 0.1);
            color: var(--accent-blue);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container dashboard-wrapper">
    <!-- Welcome Header -->
    <div class="row welcome-section animate__animated animate__fadeInDown">
        <div class="col-md-8">
            <h1 class="fw-bold">Welcome back, <span class="text-info"><?php echo htmlspecialchars($_SESSION['user']); ?></span>! 👋</h1>
            <p class="text-white-50">Here's a summary of your cybersecurity activity and ScamShield performance.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="impact-badge">
                <i class="fa-solid fa-users-rays me-2"></i>
                Community Protected: <b><?php echo $global_stats['total_scams']; ?></b> Scam offers detected
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="glass-card text-center">
                <div class="stat-icon"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
                <div class="stat-value"><?php echo $user_stats['user_total']; ?></div>
                <div class="stat-label">Total Checks</div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="glass-card text-center">
                <div class="stat-icon text-danger"><i class="fa-solid fa-shield-virus"></i></div>
                <div class="stat-value text-danger"><?php echo $user_stats['user_scams']; ?></div>
                <div class="stat-label">Scams Caught</div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="glass-card text-center">
                <div class="stat-icon text-success"><i class="fa-solid fa-circle-check"></i></div>
                <div class="stat-value text-success"><?php echo $user_stats['user_real']; ?></div>
                <div class="stat-label">Verified Jobs</div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="glass-card text-center">
                <div class="stat-icon"><i class="fa-solid fa-bolt"></i></div>
                <div class="stat-value <?php echo $safety_class; ?>"><?php echo $safety_score; ?>%</div>
                <div class="stat-label">Safety Score</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Activity -->
        <div class="col-lg-8 animate__animated animate__fadeInLeft">
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-info"></i>Recent Activity</h5>
                    <a href="history.php" class="btn btn-sm btn-outline-info rounded-pill px-3">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-glass">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Job Description</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($user_stats['recent_activity']->num_rows > 0): ?>
                                <?php while($row = $user_stats['recent_activity']->fetch_assoc()): ?>
                                <tr>
                                    <td class="small text-white-50">
                                        <?php echo date('M d', strtotime($row['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 250px;">
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
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-white-50">No activity yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Community -->
        <div class="col-lg-4 animate__animated animate__fadeInRight">
            <div class="mb-4">
                <h5 class="fw-bold mb-3">Quick Actions</h5>
                <a href="check_job.php" class="quick-action-btn">
                    <i class="fa-solid fa-plus-circle"></i>
                    <div>
                        <div class="fw-bold">Check Job Offer</div>
                        <small class="opacity-75">Start a new AI scan</small>
                    </div>
                </a>
                <a href="tips.php" class="quick-action-btn">
                    <i class="fa-solid fa-lightbulb"></i>
                    <div>
                        <div class="fw-bold">Safety Tips</div>
                        <small class="opacity-75">How to stay safe</small>
                    </div>
                </a>
            </div>

            <div class="glass-card" style="padding: 20px;">
                <h6 class="fw-bold mb-3 text-info">ScamShield Network</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-white-50">Global Users</span>
                    <span class="fw-bold"><?php echo $global_stats['total_users']; ?></span>
                </div>
                <div class="progress mb-3" style="height: 6px; background: rgba(255,255,255,0.05);">
                    <div class="progress-bar bg-info" style="width: 75%"></div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-white-50">Verified Real Jobs</span>
                    <span class="fw-bold"><?php echo $global_stats['total_real']; ?></span>
                </div>
                <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05);">
                    <div class="progress-bar bg-success" style="width: 60%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
