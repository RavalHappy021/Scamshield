<?php
include "db.php";

function getStats($conn) {
    $stats = [];
    
    // Total jobs checked
    $res = $conn->query("SELECT COUNT(*) as total FROM job_history_v2");
    $stats['total_checks'] = $res->fetch_assoc()['total'] ?? 0;
    
    // Total scams identified
    $res = $conn->query("SELECT COUNT(*) as scams FROM job_history_v2 WHERE result = 'Fake'");
    $stats['total_scams'] = $res->fetch_assoc()['scams'] ?? 0;
    
    // Total real jobs verified
    $res = $conn->query("SELECT COUNT(*) as real_jobs FROM job_history_v2 WHERE result = 'Real'");
    $stats['total_real'] = $res->fetch_assoc()['real_jobs'] ?? 0;
    
    // Total registered users
    $res = $conn->query("SELECT COUNT(*) as users FROM users");
    $stats['total_users'] = $res->fetch_assoc()['users'] ?? 0;
    
    return $stats;
}

function getUserStats($conn, $user_id) {
    if (!$user_id) return null;
    $stats = [];
    
    // Total jobs checked by user
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM job_history_v2 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['user_total'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    
    // Total scams caught by user
    $stmt = $conn->prepare("SELECT COUNT(*) as scams FROM job_history_v2 WHERE user_id = ? AND result = 'Fake'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['user_scams'] = $stmt->get_result()->fetch_assoc()['scams'] ?? 0;
    
    // Total real jobs verified by user
    $stmt = $conn->prepare("SELECT COUNT(*) as real_jobs FROM job_history_v2 WHERE user_id = ? AND result = 'Real'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['user_real'] = $stmt->get_result()->fetch_assoc()['real_jobs'] ?? 0;
    
    // Recent activity (last 5)
    $stmt = $conn->prepare("SELECT * FROM job_history_v2 WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['recent_activity'] = $stmt->get_result();
    
    return $stats;
}

function getAdminStats($conn) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') return null;
    
    $stats = [];
    
    // Distribution for chart/progress bars
    $dist = ['Fake' => 0, 'Real' => 0];
    $res = $conn->query("SELECT result, COUNT(*) as count FROM job_history_v2 GROUP BY result");
    if($res){
        while($r = $res->fetch_assoc()) {
            $dist[$r['result']] = $r['count'];
        }
    }
    $stats['distribution'] = $dist;

    // Audit Logs (last 20 scans with user details)
    $res = $conn->query("SELECT jh.*, u.name as user_name, u.email as user_email 
                         FROM job_history_v2 jh 
                         LEFT JOIN users u ON jh.user_id = u.id 
                         ORDER BY jh.created_at DESC LIMIT 20");
    $stats['audit_logs'] = $res;

    // Top Users for Engagement (excluding admins)
    $res = $conn->query("SELECT u.id, u.name, u.email, COUNT(jh.id) as check_count 
                         FROM users u 
                         INNER JOIN job_history_v2 jh ON u.id = jh.user_id 
                         WHERE u.role != 'admin' 
                         GROUP BY u.id 
                         ORDER BY check_count DESC 
                         LIMIT 5");
    $stats['top_users'] = $res;

    // Total Contact Messages Count
    $res = $conn->query("SELECT COUNT(*) as msgs FROM contact_messages");
    $stats['total_messages'] = $res->fetch_assoc()['msgs'] ?? 0;
    
    return $stats;
}
?>
