<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Connect to database
$db_file = '/tmp/users.db';
$db = new PDO('sqlite:' . $db_file);

// VULNERABILITY: SQL Injection - Direct string concatenation
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

try {
    $result = $db->query($query);
    $user = $result->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Successful login
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: admin.php');
        exit;
    } else {
        // VULNERABILITY: User Enumeration - Check if username exists
        $check_user = "SELECT * FROM users WHERE username = '$username'";
        $check_result = $db->query($check_user);
        $user_exists = $check_result->fetch(PDO::FETCH_ASSOC);
        
        if ($user_exists) {
            // User exists but wrong password
            $error = "Invalid password for user: " . htmlspecialchars($username);
        } else {
            // User doesn't exist
            $error = "User '" . $username . "' not found in system";
        }
        
        header('Location: index.php?error=' . urlencode($error));
        exit;
    }
} catch (PDOException $e) {
    // VULNERABILITY: Information Disclosure - Exposing SQL errors
    $error = "Database Error: " . $e->getMessage();
    header('Location: index.php?error=' . urlencode($error));
    exit;
}
?>