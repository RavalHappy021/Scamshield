<?php
include "db.php";

function getStats($conn)
{
    $stats = ['total_checks' => 0, 'total_scams' => 0, 'total_real' => 0, 'total_users' => 0];

    // Detect correct table name (handle v2 vs original)
    $table = "job_history_v2";
    $test = $conn->query("SHOW TABLES LIKE 'job_history_v2'");
    if (!$test || $test->num_rows == 0) {
        $table = "job_history";
    }

    // Total jobs checked
    $res = $conn->query("SELECT COUNT(*) as total FROM $table");
    if ($res)
        $stats['total_checks'] = $res->fetch_assoc()['total'] ?? 0;

    // Total scams identified
    $res = $conn->query("SELECT COUNT(*) as scams FROM $table WHERE result = 'Fake'");
    if ($res)
        $stats['total_scams'] = $res->fetch_assoc()['scams'] ?? 0;

    // Total real jobs verified
    $res = $conn->query("SELECT COUNT(*) as real_jobs FROM $table WHERE result = 'Real'");
    if ($res)
        $stats['total_real'] = $res->fetch_assoc()['real_jobs'] ?? 0;

    // Total registered users
    $res = $conn->query("SELECT COUNT(*) as users FROM users");
    if ($res)
        $stats['total_users'] = $res->fetch_assoc()['users'] ?? 0;

    return $stats;
}

function getUserStats($conn, $user_id)
{
    if (!$user_id)
        return null;
    $stats = ['user_total' => 0, 'user_scams' => 0, 'user_real' => 0, 'recent_activity' => null];

    // Detect correct table name
    $table = "job_history_v2";
    $test = $conn->query("SHOW TABLES LIKE 'job_history_v2'");
    if (!$test || $test->num_rows == 0)
        $table = "job_history";

    // Total jobs checked by user
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM $table WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stats['user_total'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    }

    // Total scams caught by user
    $stmt = $conn->prepare("SELECT COUNT(*) as scams FROM $table WHERE user_id = ? AND result = 'Fake'");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stats['user_scams'] = $stmt->get_result()->fetch_assoc()['scams'] ?? 0;
    }

    // Total real jobs verified by user
    $stmt = $conn->prepare("SELECT COUNT(*) as real_jobs FROM $table WHERE user_id = ? AND result = 'Real'");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stats['user_real'] = $stmt->get_result()->fetch_assoc()['real_jobs'] ?? 0;
    }

    // Recent activity (last 5)
    $stmt = $conn->prepare("SELECT * FROM $table WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stats['recent_activity'] = $stmt->get_result();
    }

    return $stats;
}

function getAdminStats($conn)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')
        return null;

    $stats = ['distribution' => ['Fake' => 0, 'Real' => 0], 'audit_logs' => null, 'top_users' => null, 'total_messages' => 0];

    // Detect correct table names
    $table = "job_history_v2";
    $test = $conn->query("SHOW TABLES LIKE 'job_history_v2'");
    if (!$test || $test->num_rows == 0)
        $table = "job_history";

    $msg_table = "contact_messages";
    $test = $conn->query("SHOW TABLES LIKE 'contact_messages'");
    $has_msgs = ($test && $test->num_rows > 0);

    // Distribution
    $dist = ['Fake' => 0, 'Real' => 0];
    $res = $conn->query("SELECT result, COUNT(*) as count FROM $table GROUP BY result");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $dist[$r['result']] = $r['count'];
        }
    }
    $stats['distribution'] = $dist;

    // Audit Logs
    $res = $conn->query("SELECT jh.*, u.name as user_name, u.email as user_email 
                         FROM $table jh 
                         LEFT JOIN users u ON jh.user_id = u.id 
                         ORDER BY jh.created_at DESC LIMIT 20");
    $stats['audit_logs'] = $res;

    // Top Users
    $res = $conn->query("SELECT u.id, u.name, u.email, COUNT(jh.id) as check_count 
                         FROM users u 
                         INNER JOIN $table jh ON u.id = jh.user_id 
                         WHERE u.role != 'admin' 
                         GROUP BY u.id 
                         ORDER BY check_count DESC 
                         LIMIT 5");
    $stats['top_users'] = $res;

    // Total Contact Messages Count
    if ($has_msgs) {
        $res = $conn->query("SELECT COUNT(*) as msgs FROM contact_messages");
        $stats['total_messages'] = $res->fetch_assoc()['msgs'] ?? 0;
    }

    return $stats;
}
?>
