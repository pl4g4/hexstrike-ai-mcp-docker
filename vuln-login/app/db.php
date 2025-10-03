<?php
// Database initialization script
$db_file = '/tmp/users.db';

// Create database connection
$db = new PDO('sqlite:' . $db_file);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create users table
$db->exec("DROP TABLE IF EXISTS users");
$db->exec("
    CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'user'
    )
");

// Insert sample users
$users = [
    ['admin', 'Admin@123', 'admin'],
    ['john', 'john123', 'user'],
    ['sarah', 'sarah456', 'user'],
    ['test', 'test', 'user']
];

$stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
foreach ($users as $user) {
    $stmt->execute($user);
}

echo "Database initialized successfully!\n";
?>