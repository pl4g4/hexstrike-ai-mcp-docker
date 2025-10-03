<?php
header('Content-Type: application/json');

// Simple JWT implementation for training purposes
class VulnerableJWT {
    // VULNERABILITY: Weak secret that can be brute-forced
    private static $secret = 'secret123';
    
    public static function encode($payload) {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", self::$secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }
    
    public static function decode($jwt) {
        $parts = explode('.', $jwt);
        
        if (count($parts) !== 3) {
            return ['error' => 'Invalid JWT format'];
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        $header = json_decode(self::base64UrlDecode($headerEncoded), true);
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        // VULNERABILITY 1: Accept "none" algorithm (no signature verification)
        if (isset($header['alg']) && strtolower($header['alg']) === 'none') {
            return $payload;
        }
        
        // VULNERABILITY 2: No signature verification - just decode and return
        // In a secure implementation, we would verify the signature here
        // For training purposes, we accept ANY signature
        
        return $payload;
    }
    
    // VULNERABILITY 3: Algorithm confusion - accepts both HS256 and RS256
    // without proper validation
    public static function decodeWithoutVerification($jwt) {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return ['error' => 'Invalid JWT format'];
        }
        
        $payload = json_decode(self::base64UrlDecode($parts[1]), true);
        return $payload;
    }
    
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}

// Handle different API endpoints
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'generate':
        // Generate a guest token (no authentication required)
        $payload = [
            'user' => $_GET['user'] ?? 'guest',
            'role' => 'user',
            'exp' => time() + 3600,
            'iat' => time()
        ];
        
        $token = VulnerableJWT::encode($payload);
        
        echo json_encode([
            'success' => true,
            'token' => $token,
            'payload' => $payload,
            'hint' => 'Try modifying the token or using "none" algorithm'
        ]);
        break;
        
    case 'verify':
        // Verify and decode a JWT token
        $token = $_GET['token'] ?? $_POST['token'] ?? '';
        
        if (empty($token)) {
            echo json_encode(['error' => 'Token required']);
            exit;
        }
        
        $payload = VulnerableJWT::decode($token);
        
        if (isset($payload['error'])) {
            echo json_encode($payload);
            exit;
        }
        
        // Check if user is admin
        $isAdmin = isset($payload['role']) && $payload['role'] === 'admin';
        
        echo json_encode([
            'success' => true,
            'valid' => true,
            'payload' => $payload,
            'is_admin' => $isAdmin,
            'message' => $isAdmin ? '🎉 Admin access granted!' : 'Regular user access',
            'secret_data' => $isAdmin ? 'FLAG{JWT_N0N3_4LG0R1THM_VULN}' : null
        ]);
        break;
        
    case 'admin-data':
        // Protected endpoint that requires admin JWT
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }
        
        $payload = VulnerableJWT::decode($token);
        
        if (isset($payload['role']) && $payload['role'] === 'admin') {
            echo json_encode([
                'success' => true,
                'admin_data' => [
                    'secret_key' => 'SK_PROD_12345',
                    'database_password' => 'super_secret_pass',
                    'api_keys' => ['key1', 'key2', 'key3'],
                    'flag' => 'FLAG{JWT_VULN3R4B1L1TY_3XPL01T3D}'
                ]
            ]);
        } else {
            echo json_encode(['error' => 'Admin access required']);
        }
        break;
        
    case 'info':
        // Information about the JWT implementation
        echo json_encode([
            'info' => 'Vulnerable JWT API for Security Training',
            'endpoints' => [
                'generate' => 'GET /api.php?action=generate&user=username - Generate a JWT token',
                'verify' => 'GET /api.php?action=verify&token=YOUR_TOKEN - Verify a JWT token',
                'admin-data' => 'GET /api.php?action=admin-data&token=YOUR_TOKEN - Access admin data'
            ],
            'vulnerabilities' => [
                'None Algorithm Attack',
                'Weak Secret (secret123)',
                'No Signature Verification',
                'Algorithm Confusion'
            ],
            'hints' => [
                'Try changing the algorithm to "none" in the header',
                'Try modifying the role in the payload to "admin"',
                'The secret is weak and can be brute-forced',
                'No signature verification is performed'
            ]
        ]);
        break;
        
    default:
        echo json_encode([
            'error' => 'Invalid action',
            'available_actions' => ['generate', 'verify', 'admin-data', 'info']
        ]);
}
?>