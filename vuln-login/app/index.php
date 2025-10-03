<?php
session_start();

// If already logged in, redirect to admin
if (isset($_SESSION['user'])) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Login - Security Training</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>🔓 Security Training Lab</h1>
            <h2>Vulnerable Login System</h2>
            
            <div class="warning">
                ⚠️ <strong>Training Environment Only</strong><br>
                This application contains intentional security vulnerabilities for educational purposes.
            </div>

            <form id="loginForm" action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn">Login</button>
            </form>

            <div class="additional-links">
                <a href="reset-password.php">🔑 Forgot Password?</a>
                <a href="newsletter.php">📧 Subscribe to Newsletter</a>
            </div>

            <div id="errorMessage" class="error"></div>

            <div class="hints">
                <h3>🎯 Training Objectives:</h3>
                <ul>
                    <li>SQL Injection (Login Bypass)</li>
                    <li>Cross-Site Scripting (XSS)</li>
                    <li>User Enumeration</li>
                    <li>Command Injection (Password Reset)</li>
                    <li>SSRF Attack (Newsletter)</li>
                </ul>
                <details>
                    <summary>💡 Hints</summary>
                    <ul>
                        <li><strong>SQLi:</strong> Try common SQL injection payloads in username field</li>
                        <li><strong>XSS:</strong> Error messages might reflect your input</li>
                        <li><strong>Enumeration:</strong> Notice different error messages for valid/invalid users</li>
                        <li><strong>Command Injection:</strong> Check the password reset functionality</li>
                        <li><strong>SSRF:</strong> Newsletter subscription accepts URLs</li>
                        <li><strong>Valid users:</strong> admin, john, sarah, test</li>
                    </ul>
                </details>
            </div>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #ddd;">
                <h3>🔐 Additional Labs:</h3>
                <a href="jwt-lab.php" class="btn" style="margin-top: 10px;">JWT Vulnerability Lab →</a>
            </div>
        </div>
    </div>

    <script>
        // Display error message from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        
        if (error) {
            // VULNERABILITY: XSS - Directly inserting user input into DOM
            document.getElementById('errorMessage').innerHTML = '❌ ' + decodeURIComponent(error);
        }
    </script>
</body>
</html>