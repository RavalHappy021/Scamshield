<?php
include "navbar.php";
include "db.php";
include "stats_helper.php";

// Secure access: only for Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle User Deletion
$delete_msg = "";
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    // Prevent admin from deleting themselves
    if ($id == $_SESSION['user_id']) {
        $delete_msg = "<div class='alert alert-warning'>You cannot delete your own account!</div>";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $delete_msg = "<div class='alert alert-success'>User deleted successfully!</div>";
        } else {
            $delete_msg = "<div class='alert alert-danger'>Error deleting user.</div>";
        }
    }
}

// Search Logic
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$query = "SELECT * FROM users";
if ($search != "") {
    $query .= " WHERE name LIKE ? OR email LIKE ?";
    $stmt = $conn->prepare($query);
    $search_param = "%$search%";
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query .= " ORDER BY created_at DESC";
    $result = $conn->query($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | ScamShield Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --primary-bg: #0a0f12;
            --accent-blue: #00f2fe;
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
        }

        body {
            background-color: var(--primary-bg);
            color: #d1d5db;
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
        }

        .admin-section { padding: 40px 0; }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 30px;
        }

        .search-bar {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 50px;
            padding: 12px 25px;
            color: white;
            transition: all 0.3s;
        }

        .search-bar:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-blue);
            box-shadow: 0 0 15px rgba(0, 242, 254, 0.2);
            color: white;
            outline: none;
        }

        .table-responsive { border-radius: 20px; }

        .table {
            --bs-table-bg: transparent !important;
            --bs-table-color: #d1d5db !important;
            border-collapse: separate;
            border-spacing: 0 12px;
            margin: 0;
        }

        .table thead th {
            border: none;
            color: var(--accent-blue);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 15px 25px;
            font-weight: 700;
        }

        .table tbody tr {
            background-color: #12181b !important; /* Dark solid fallback */
            background: rgba(255, 255, 255, 0.03) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.07) !important;
            transform: translateY(-2px);
        }

        .table td {
            border: none !important;
            padding: 20px 25px;
            vertical-align: middle;
            color: #ffffff !important;
        }

        .table td:first-child { border-radius: 16px 0 0 16px; }
        .table td:last-child { border-radius: 0 16px 16px 0; }

        .user-avatar {
            width: 44px;
            height: 44px;
            background: var(--accent-gradient);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: white;
            margin-right: 15px;
        }

        .email-text {
            color: var(--accent-blue) !important;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .date-text {
            color: #9ca3af !important;
            font-size: 0.85rem;
        }

        .btn-delete {
            width: 38px;
            height: 38px;
            background: rgba(255, 75, 43, 0.1);
            color: #ff4b2b;
            border: 1px solid rgba(255, 75, 43, 0.2);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-delete:hover {
            background: #ff4b2b;
            color: white;
            box-shadow: 0 0 15px rgba(255, 75, 43, 0.4);
        }

        .text-gradient {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }
    </style>
</head>
<body>

    <div class="admin-section">
        <div class="container text-white">
            
            <div class="mb-4 d-flex justify-content-between align-items-center animate__animated animate__fadeInDown">
                <div>
                    <a href="admin_dashboard.php" class="text-white-50 text-decoration-none small mb-2 d-block">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
                    </a>
                    <h1 class="fw-bold m-0 text-gradient">User Management</h1>
                </div>
                
                <form class="d-flex" action="admin_users.php" method="GET">
                    <div class="position-relative">
                        <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 20px; top: 15px; color: rgba(255,255,255,0.3);"></i>
                        <input type="text" name="search" class="search-bar ps-5" placeholder="Search name or email..." value="<?php echo htmlspecialchars($search); ?>">
                        <?php if($search != ""): ?>
                            <a href="admin_users.php" class="position-absolute" style="right: 20px; top: 12px; color: rgba(255,255,255,0.5);"><i class="fa-solid fa-xmark"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php echo $delete_msg; ?>

            <div class="glass-card animate__animated animate__fadeInUp">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email Address</th>
                                <th>Role</th>
                                <th>Registration Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($user = $result->fetch_assoc()): ?>
                                <tr>
                                     <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar">
                                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-white"><?php echo htmlspecialchars($user['name']); ?></div>
                                                <div class="small text-white-50">ID: #<?php echo $user['id']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="email-text"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['role'] == 'admin' ? 'bg-danger' : 'bg-info'; ?> bg-opacity-10 text-<?php echo $user['role'] == 'admin' ? 'danger' : 'info'; ?> border border-<?php echo $user['role'] == 'admin' ? 'danger' : 'info'; ?> border-opacity-25 rounded-pill px-3">
                                            <i class="fa-solid <?php echo $user['role'] == 'admin' ? 'fa-crown' : 'fa-user'; ?> me-1 small"></i>
                                            <?php echo strtoupper($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="date-text">
                                        <div class="fw-bold text-white"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                                        <div style="font-size: 0.75rem; color: #9ca3af;"><?php echo date('h:i A', strtotime($user['created_at'])); ?></div>
                                    </td>
                                    <td class="text-end">
                                        <button onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo addslashes($user['name']); ?>')" class="btn-delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-white-50">
                                        <i class="fa-solid fa-user-slash d-block mb-3 fs-1 opacity-25"></i>
                                        No users found matching "<b><?php echo htmlspecialchars($search); ?></b>"
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
    function confirmDelete(id, name) {
        if (confirm("Are you sure you want to delete user '" + name + "'? This action cannot be undone.")) {
            window.location.href = "admin_users.php?delete_id=" + id;
        }
    }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
