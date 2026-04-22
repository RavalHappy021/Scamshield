<?php
include_once "db.php";

// Helper function to find which history table exists
function getHistoryTable($conn) {
    if (!$conn) return "job_history_v2"; // fallback
    static $tableName = null;
    if ($tableName !== null) return $tableName;

    $res = @$conn->query("SHOW TABLES LIKE 'job_history_v2'");
    if ($res && $res->num_rows > 0) {
        $tableName = "job_history_v2";
    } else {
        $tableName = "job_history";
    }
    return $tableName;
}

function getStats($conn) {
    $stats = [
        'total_checks' => 0,
        'total_scams' => 0,
        'total_real' => 0,
        'total_users' => 0
    ];
    
    if (!$conn) return $stats;
    $table = getHistoryTable($conn);

    try {
        // Total jobs checked
        $res = @$conn->query("SELECT COUNT(*) as total FROM $table");
        if ($res) $stats['total_checks'] = $res->fetch_assoc()['total'] ?? 0;
        
        // Total scams identified
        $res = @$conn->query("SELECT COUNT(*) as scams FROM $table WHERE result = 'Fake'");
        if ($res) $stats['total_scams'] = $res->fetch_assoc()['scams'] ?? 0;
        
        // Total real jobs verified
        $res = @$conn->query("SELECT COUNT(*) as real_jobs FROM $table WHERE result = 'Real'");
        if ($res) $stats['total_real'] = $res->fetch_assoc()['real_jobs'] ?? 0;
        
        // Total registered users
        $res = @$conn->query("SELECT COUNT(*) as users FROM users");
        if ($res) $stats['total_users'] = $res->fetch_assoc()['users'] ?? 0;
    } catch (Exception $e) {
        // Fail silently
    }
    
    return $stats;
}

function getUserStats($conn, $user_id) {
    if (!$user_id || !$conn) return null;
    $table = getHistoryTable($conn);
    $stats = [
        'user_total' => 0,
        'user_scams' => 0,
        'user_real' => 0,
        'recent_activity' => null
    ];
    
    try {
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
    } catch (Exception $e) {
        // Fail silently
    }
    
    return $stats;
}
?>
