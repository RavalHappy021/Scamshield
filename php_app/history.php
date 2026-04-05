<?php 
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM job_history_v2 WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity History | ScamShield</title>
    
    <?php include "header_imports.php"; ?>

    <style>
        body {
            background-color: #0c151b;
            color: #e0e0e0;
            font-family: 'Outfit', sans-serif;
        }
        .dashboard-container {
            padding-top: 50px;
            padding-bottom: 50px;
        }
        .history-card {
            background: #16242d;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .table {
            color: #e0e0e0;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        .table thead th {
            border: none;
            color: #00d2ff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1.5px;
            padding: 15px;
            background: transparent;
        }
        .table tbody tr {
            background: rgba(255, 255, 255, 0.03);
            transition: transform 0.2s;
        }
        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: scale(1.01);
        }
        .table tbody td {
            border: none;
            padding: 15px;
            vertical-align: middle;
        }
        .table tbody td:first-child { border-radius: 10px 0 0 10px; }
        .table tbody td:last-child { border-radius: 0 10px 10px 0; }

        .badge-status {
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-block;
            white-space: nowrap;
        }
        .badge-real {
            background: rgba(40, 167, 69, 0.15);
            color: #2ecc71;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        .badge-fake {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }
        .job-text-preview {
            max-width: 250px;
            max-height: 50px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            color: #b0b0b0;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        .reason-text {
            color: #90a4ae;
            font-size: 0.85rem;
            max-width: 300px;
        }
    </style>
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container dashboard-container">
    <div class="row">
        <div class="col-12 mb-5 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold m-0 text-white">Scan History</h2>
                <p class="text-white-50 small mb-0">Review all your previous job offer assessments</p>
            </div>
            <a href="check_job.php" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
                <i class="fa-solid fa-plus me-2"></i>New Scan
            </a>
        </div>
        
        <div class="col-12">
            <div class="history-card">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="20%">Date & Time</th>
                                <th width="30%">Job Details</th>
                                <th width="15%">AI Result</th>
                                <th width="35%">Reason & Analysis</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark small"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?php echo date('h:i A', strtotime($row['created_at'])); ?></div>
                                    </td>
                                    <td>
                                        <div class="job-text-preview" title="<?php echo htmlspecialchars($row['job_text']); ?>">
                                            <?php echo htmlspecialchars($row['job_text']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($row['result'] == "Fake"): ?>
                                            <span class="badge-status badge-fake">FAKE ❌</span>
                                        <?php else: ?>
                                            <span class="badge-status badge-real">REAL ✅</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="reason-text">
                                            <i class="fa-solid fa-quote-left me-1 opacity-50 small"></i>
                                            <?php echo htmlspecialchars($row['reason'] ?? 'Analysis based on job structure and language patterns.'); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076403.png" width="80" class="opacity-25 mb-3" alt="Empty">
                                        <h5 class="text-white">No scans yet</h5>
                                        <p class="text-white-50 small">Your job check history will appear here.</p>
                                        <a href="check_job.php" class="btn btn-sm btn-outline-info rounded-pill px-4 mt-2">Start Scanning</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
