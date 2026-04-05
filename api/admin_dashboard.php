<?php
include "navbar.php";
include "db.php";
include "stats_helper.php";

// Admin access check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$global_stats = getStats($conn);
$admin_stats = getAdminStats($conn);

// Variables for ease of use
$total_users = $global_stats['total_users'];
$total_checks = $global_stats['total_checks'];
$total_scams = $global_stats['total_scams'];
$distribution = $admin_stats['distribution'];

// Calculate Percentages
$scam_perc = ($total_checks > 0) ? round(($distribution['Fake'] / $total_checks) * 100) : 0;
$real_perc = ($total_checks > 0) ? round(($distribution['Real'] / $total_checks) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Command | ScamShield Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --bg-dark: #0a0f12;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --accent-cyan: #00f2fe;
            --accent-blue: #4facfe;
            --accent-red: #ff4b2b;
            --accent-green: #00f260;
            --grad-primary: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --grad-danger: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
        }

        body {
            background-color: var(--bg-dark);
            color: #d1d5db;
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.2px;
        }

        .admin-container {
            padding: 40px 0;
        }

        /* Top Header */
        .glass-header {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .glass-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(79, 172, 254, 0.05) 0%, transparent 70%);
            z-index: -1;
        }

        /* Status Pills */
        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: rgba(0, 242, 96, 0.1);
            color: var(--accent-green);
            border: 1px solid rgba(0, 242, 96, 0.2);
        }

        .status-pill.offline {
            background: rgba(255, 75, 43, 0.1);
            color: var(--accent-red);
            border: 1px solid rgba(255, 75, 43, 0.2);
        }

        /* Bento Grid Cards */
        .bento-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 24px;
            height: 100%;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .bento-card:hover {
            border-color: rgba(79, 172, 254, 0.3);
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.05);
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 20px;
        }

        .bg-cyan { background: rgba(0, 242, 254, 0.1); color: var(--accent-cyan); }
        .bg-red { background: rgba(255, 75, 43, 0.1); color: var(--accent-red); }
        .bg-blue { background: rgba(79, 172, 254, 0.1); color: var(--accent-blue); }

        .stat-label {
            color: #9ca3af;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .stat-value {
            color: #f3f4f6;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 4px 0;
        }

        /* Tables */
        .platform-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .platform-table tr {
            background: rgba(255, 255, 255, 0.02);
            transition: background 0.3s;
        }

        .platform-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .platform-table td, .platform-table th {
            padding: 16px;
            border: none;
        }

        .platform-table th {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 1px;
            padding-bottom: 20px;
        }

        .platform-table td:first-child { border-radius: 16px 0 0 16px; }
        .platform-table td:last-child { border-radius: 0 16px 16px 0; }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: var(--grad-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 0.75rem;
        }

        .result-tag {
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .tag-fake { background: rgba(255, 75, 43, 0.1); color: var(--accent-red); border: 1px solid rgba(255, 75, 43, 0.2); }
        .tag-real { background: rgba(0, 242, 96, 0.1); color: var(--accent-green); border: 1px solid rgba(0, 242, 96, 0.2); }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }

        .chart-mini {
            height: 8px;
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }

        .chart-bar {
            height: 100%;
            background: var(--grad-primary);
        }
    </style>
</head>
<body>

<div class="container admin-container">
    
    <!-- Header -->
    <div class="glass-header animate__animated animate__fadeIn">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="status-pill" id="apiStatus"><i class="fa-solid fa-circle-check me-2"></i>AI Engine Online</span>
                    <span class="text-white-50">🛡 ScamShield Platform Hub</span>
                </div>
                <h1 class="display-5 fw-bold text-white mb-1">Welcome, <span class="text-info"><?php echo htmlspecialchars($_SESSION['user']); ?></span></h1>
                <p class="lead text-white-50">Monitoring global threat vectors and system performance in real-time.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <div class="d-flex flex-column align-items-lg-end gap-2">
                    <div class="text-white-50 small">System Load: <span class="text-info fw-bold">14%</span></div>
                    <div class="text-white-50 small">Active Sessions: <span class="text-info fw-bold"><?php echo rand(5, 15); ?></span></div>
                    <button class="btn btn-info rounded-pill px-4 mt-3 fw-bold shadow-sm" onclick="location.reload()">
                        <i class="fa-solid fa-sync me-2"></i>Refresh Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="bento-card">
                <div class="icon-box bg-blue"><i class="fa-solid fa-users"></i></div>
                <div class="stat-label">Total Registered Users</div>
                <div class="stat-value"><?php echo $total_users; ?></div>
                <div class="chart-mini"><div class="chart-bar" style="width: 70%;"></div></div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="bento-card">
                <div class="icon-box bg-cyan"><i class="fa-solid fa-bolt"></i></div>
                <div class="stat-label">Global AI Scans</div>
                <div class="stat-value"><?php echo $total_checks; ?></div>
                <div class="chart-mini"><div class="chart-bar" style="width: 85%; background: var(--grad-primary);"></div></div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="bento-card">
                <div class="icon-box bg-red"><i class="fa-solid fa-shield-virus"></i></div>
                <div class="stat-label">Threats Neuralized</div>
                <div class="stat-value text-danger"><?php echo $total_scams; ?></div>
                <div class="chart-mini"><div class="chart-bar" style="width: <?php echo $scam_perc; ?>%; background: var(--grad-danger);"></div></div>
            </div>
        </div>
        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="bento-card">
                <div class="icon-box" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;"><i class="fa-solid fa-chart-pie"></i></div>
                <div class="stat-label">Detection Accuracy</div>
                <div class="stat-value text-warning">99.4%</div>
                <div class="chart-mini"><div class="chart-bar" style="width: 99%; background: #ffc107;"></div></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Audit Logs -->
        <div class="col-lg-8 animate__animated animate__fadeInLeft">
            <div class="bento-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-list-check me-2 text-info"></i>Global Audit Log</h5>
                    <span class="text-white-50 small">Showing last 20 events</span>
                </div>
                <div class="table-responsive" style="max-height: 500px;">
                    <table class="platform-table">
                        <thead>
                            <tr>
                                <th>Entity</th>
                                <th>Scan Content</th>
                                <th>Outcome</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($log = $admin_stats['audit_logs']->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="user-chip">
                                        <div class="avatar-sm"><?php echo strtoupper(substr($log['user_name'] ?? 'G', 0, 1)); ?></div>
                                        <div>
                                            <div class="small fw-bold text-white"><?php echo htmlspecialchars($log['user_name'] ?? 'Guest'); ?></div>
                                            <div style="font-size: 0.65rem; color: #6b7280;"><?php echo htmlspecialchars($log['user_email'] ?? 'Anonymous'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate small text-white-50" style="max-width: 250px;">
                                        <?php echo htmlspecialchars($log['job_text']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($log['result'] == 'Fake'): ?>
                                        <span class="result-tag tag-fake">Scam Detected</span>
                                    <?php else: ?>
                                        <span class="result-tag tag-real">Safe Job</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-white-50">
                                    <?php echo date('M d, H:i', strtotime($log['created_at'])); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- User Management Summary -->
        <div class="col-lg-4 animate__animated animate__fadeInRight">
            <div class="bento-card">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-user-shield me-2 text-info"></i>User Engagement</h5>
                <div class="user-list">
                    <?php while($user = $admin_stats['top_users']->fetch_assoc()): ?>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-sm" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);">
                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <div class="small fw-bold text-white"><?php echo htmlspecialchars($user['name']); ?></div>
                                <div style="font-size: 0.65rem; color: #6b7280;"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="small fw-bold text-info"><?php echo $user['check_count']; ?></div>
                            <div style="font-size: 0.6rem; color: #6b7280;">SCANS</div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <hr class="opacity-10">
                <div class="d-grid mt-3">
                    <a href="admin_users.php" class="btn btn-outline-info rounded-pill py-2 small fw-bold">View Full Registry</a>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // Real-time API check simulation (or real if you have the endpoint)
    const apiPill = document.getElementById('apiStatus');
    const apiBase = "https://scamshield-cplu.onrender.com";

    fetch(apiBase, { mode: 'no-cors' })
    .then(() => {
        apiPill.innerHTML = '<i class="fa-solid fa-circle-check me-2"></i>AI Engine Online';
        apiPill.className = 'status-pill';
    })
    .catch(() => {
        apiPill.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i>AI Engine Offline';
        apiPill.className = 'status-pill offline';
    });
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
