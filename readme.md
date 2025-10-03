# HexStrike AI - PentestMCP - Model Context Protocol for Security Testing - 

This repository contains Docker configurations for running security testing tools using Kali and HexStrike AI with VS Code and GitHub Copilot.

## Components

### 1. Docker Compose Configuration (`docker-compose.yml`)

The `docker-compose.yml` file sets up three services:

- **hexstrike server**: Main HexStrike AI server
  - Runs on port 8888
  - IP: 172.25.0.10
  - Built from Dockerfile.server
  - Provides core functionality

- **log4j**: Test environment for Log4j
  - Port: 4712
  - IP: 172.25.0.30
  - Uses vulnerable Log4j version for testing

- **webmin**: Test environment for Webmin
  - Port: 10000
  - IP: 172.25.0.40
  - Uses Webmin 1.910 for vulnerability testing

- **vuln-login**: Vulnerable Login Application
  - Port: 8080 (when running standalone)
  - IP: 172.25.0.40 (in hexnet)
  - Multi-vulnerability web application for security training

### 2. Server Configuration (`Dockerfile.server`)

The `Dockerfile.server` builds a Kali Linux-based image with:
- Python environment setup
- Security testing tools (nmap, nuclei)
- HexStrike AI installation
- Root access for security tools (required for nmap, etc.)

### 3. Vulnerable Login Application (`vuln-login/`)

A comprehensive security training application featuring multiple intentional vulnerabilities:

**Features:**
- **SQL Injection**: Login bypass vulnerabilities
- **Cross-Site Scripting (XSS)**: Reflected XSS in error messages
- **User Enumeration**: Different error messages for valid/invalid users
- **Command Injection**: OS command injection in password reset
- **SSRF (Server-Side Request Forgery)**: Internal network access via newsletter subscription
- **JWT Vulnerabilities**: "none" algorithm attacks, weak secrets, signature bypass

**Application Structure:**
- `app/index.php` - Main login interface with vulnerability hints
- `app/login.php` - Vulnerable login logic (SQL injection)
- `app/admin.php` - Protected admin panel
- `app/reset-password.php` - Command injection vulnerability lab
- `app/newsletter.php` - SSRF vulnerability lab
- `app/jwt-lab.php` - Interactive JWT vulnerability training
- `app/api.php` - Vulnerable JWT API endpoints
- `app/db.php` - Database initialization
- `app/style.css` - Application styling

**Training Objectives:**
- Practice SQL injection techniques for authentication bypass
- Learn XSS exploitation in error handling
- Understand user enumeration attacks
- Explore command injection vulnerabilities
- Test SSRF attacks against internal services
- Master JWT vulnerability exploitation

**Valid Test Credentials:**
- Users: `admin`, `john`, `sarah`, `test`
- Default passwords available in database

**Running the Application:**
```bash
cd vuln-login
docker compose up -d
# Access at http://localhost:8080
```

### 4. MCP Configuration (`mcp.json`)

Example configuration for VS Code MCP integration:

```json
{
    "servers": {
        "hexstrike": {
            "type": "stdio",
            "command": "docker",
            "args": [
                "exec",
                "-i",
                "hexstrike_server",
                "/home/kali/hexstrike-venv/bin/python3",
                "hexstrike-ai/hexstrike_mcp.py",
                "--server",
                "http://172.25.0.10:8888"
            ]
        }
    },
    "inputs": []
}
```

## Quick Start

1. **Build and Start Services**:
```bash
docker compose up -d
```

2. **Configure VS Code**:
- Install GitHub Copilot
- Copy the `mcp.json` configuration to your VS Code settings
- Set up the MCP extension in VS Code

3. **Access Services**:
- HexStrike Server: http://localhost:8888
- Log4j test instance: port 4712
- Webmin test instance: https://localhost:10000
- Vulnerable Login App: http://localhost:8080 (when running standalone)

## Usage with GitHub Copilot

1. Open VS Code with the project
2. Ensure MCP configuration is loaded
3. Use Copilot commands to interact with the security testing environment
4. Execute tests through the MCP interface

## Vulnerability Labs

### SQL Injection Lab
The vuln-login application provides hands-on SQL injection training:
- Practice authentication bypass techniques
- Learn common SQL injection payloads
- Understand parameterized query prevention

### XSS Lab
Explore Cross-Site Scripting vulnerabilities:
- Reflected XSS in error messages
- DOM-based XSS scenarios
- XSS prevention techniques

### Command Injection Lab
Learn OS command injection exploitation:
- Email parameter injection in password reset
- Command chaining and bypass techniques
- Secure coding practices

### SSRF Lab
Server-Side Request Forgery training:
- Internal network enumeration
- Cloud metadata access attempts
- URL filtering bypass techniques

### JWT Security Lab
JSON Web Token vulnerability exploration:
- "none" algorithm attacks
- Signature bypass techniques
- Token manipulation and forgery
- Weak secret exploitation

## Security Note

This environment contains intentionally vulnerable services for educational purposes only. Do not expose to public networks or use in production environments.

## Network Configuration

Custom network `hexnet` (172.25.0.0/24) for isolation and controlled testing.
