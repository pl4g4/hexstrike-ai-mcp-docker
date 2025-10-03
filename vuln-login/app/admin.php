<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: index.php?error=Access denied. Please login first.');
    exit;
}

$username = $_SESSION['user'];
$role = $_SESSION['role'] ?? 'user';
$is_admin = ($role === 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="dashboard">
            <h1>🎉 Access Granted!</h1>
            
            <div class="success-box">
                <h2><?php echo $is_admin ? '👑 Admin Area' : '👤 User Area'; ?></h2>
                <p><strong>Welcome:</strong> <?php echo htmlspecialchars($username); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($role); ?></p>
                <p><strong>Status:</strong> <span class="badge-success">Authenticated</span></p>
            </div>

            <?php if ($is_admin): ?>
            <div class="admin-panel">
                <h3>🔐 Admin Privileges</h3>
                <p>Congratulations! You've successfully accessed the admin panel.</p>
                <ul>
                    <li>✅ SQL Injection vulnerability exploited</li>
                    <li>✅ Authentication bypass achieved</li>
                    <li>✅ Unauthorized access granted</li>
                </ul>
            </div>
            <?php else: ?>
            <div class="info-box">
                <h3>📊 User Dashboard</h3>
                <p>You have limited access. Admin privileges required for full access.</p>
            </div>
            <?php endif; ?>

            <div class="actions">
                <a href="?logout=1" class="btn btn-secondary">Logout</a>
            </div>

            <div class="training-info">
                <h3>🎓 What You Learned:</h3>
                <ul>
                    <li><strong>SQL Injection:</strong> Bypassed authentication using SQL injection</li>
                    <li><strong>Impact:</strong> Gained unauthorized access to protected resources</li>
                    <li><strong>Prevention:</strong> Use prepared statements and parameterized queries</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>