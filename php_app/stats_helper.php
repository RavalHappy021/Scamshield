<?php
include "db.php";

function getStats($conn) {
    $stats = [];
    
    // Total jobs checked
    $res = $conn->query("SELECT COUNT(*) as total FROM job_history");
    $stats['total_checks'] = $res->fetch_assoc()['total'] ?? 0;
    
    // Total scams identified
    $res = $conn->query("SELECT COUNT(*) as scams FROM job_history WHERE result = 'Fake'");
    $stats['total_scams'] = $res->fetch_assoc()['scams'] ?? 0;
    
    // Total real jobs verified
    $res = $conn->query("SELECT COUNT(*) as real_jobs FROM job_history WHERE result = 'Real'");
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
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM job_history WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['user_total'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    
    // Total scams caught by user
    $stmt = $conn->prepare("SELECT COUNT(*) as scams FROM job_history WHERE user_id = ? AND result = 'Fake'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['user_scams'] = $stmt->get_result()->fetch_assoc()['scams'] ?? 0;
    
    // Total real jobs verified by user
    $stmt = $conn->prepare("SELECT COUNT(*) as real_jobs FROM job_history WHERE user_id = ? AND result = 'Real'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['user_real'] = $stmt->get_result()->fetch_assoc()['real_jobs'] ?? 0;
    
    // Recent activity (last 5)
    $stmt = $conn->prepare("SELECT * FROM job_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['recent_activity'] = $stmt->get_result();
    
    return $stats;
}
?>
