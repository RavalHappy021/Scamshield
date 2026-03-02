<?php 
include "navbar.php";
include "db.php";

// Secure access: only for Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle message deletion
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM contact_messages WHERE id = '$id'");
    header("Location: admin_messages.php");
    exit();
}

// Fetch all messages
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inbox | ScamShield</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-bg: #0f2027;
            --accent-blue: #00d2ff;
            --accent-gradient: linear-gradient(135deg, #00d2ff, #3a7bd5);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: var(--primary-bg);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
        }

        .admin-section {
            padding: 60px 0;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .table {
            color: white;
            border-color: var(--glass-border);
        }

        .message-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .message-card:hover {
            border-color: var(--accent-blue);
            background: rgba(255, 255, 255, 0.05);
        }

        .text-gradient {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .badge-date {
            background: rgba(0, 210, 255, 0.1);
            color: var(--accent-blue);
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
        }

        .btn-delete {
            color: #ff4d4d;
            background: rgba(255, 77, 77, 0.1);
            border: none;
            padding: 8px 15px;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-delete:hover {
            background: #ff4d4d;
            color: white;
        }
    </style>
</head>
<body>

    <div class="admin-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h1 class="fw-bold m-0 text-gradient">Support Inbox</h1>
                <span class="badge bg-primary rounded-pill"><?php echo mysqli_num_rows($result); ?> Messages</span>
            </div>

            <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="row">
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <div class="col-12">
                            <div class="message-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($row['subject']); ?></h5>
                                        <div class="text-white-50 small">From: <b><?php echo htmlspecialchars($row['name']); ?></b> (<?php echo htmlspecialchars($row['email']); ?>)</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge-date mb-2"><?php echo date('M d, Y - H:i', strtotime($row['created_at'])); ?></div>
                                        <br>
                                        <a href="admin_messages.php?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this message?')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </div>
                                <hr class="opacity-10">
                                <p class="mb-0 text-white-50" style="white-space: pre-wrap;"><?php echo htmlspecialchars($row['message']); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="glass-card text-center py-5">
                    <i class="fa-solid fa-envelope-open text-white-50 display-4 mb-3"></i>
                    <h4 class="text-white-50">No messages yet.</h4>
                    <p class="mb-0">Your inbox is clean!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
