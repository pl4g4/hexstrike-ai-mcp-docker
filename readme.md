# HexStrike AI - PentestMCP - Model Context Protocol for Security Testing - 

This repository contains Docker configurations for running security testing tools using Kali and HexStrike AI with VS Code and GitHub Copilot.

## Components

### 1. Docker Compose Configuration (`docker-compose.yml`)

The `docker-compose.yml` file sets up three services:

- **server**: Main HexStrike AI server
  - Runs on port 8888
  - IP: 172.25.0.10
  - Built from Dockerfile.server
  - Provides core functionality

- **mcp**: MCP (Model Context Protocol) service
  - IP: 172.25.0.20
  - Uses same image as server
  - Provides command interface

- **log4j**: Test environment for Log4j
  - Port: 4712
  - IP: 172.25.0.30
  - Uses vulnerable Log4j version for testing

- **webmin**: Test environment for Webmin
  - Port: 10000
  - IP: 172.25.0.40
  - Uses Webmin 1.910 for vulnerability testing

### 2. Server Configuration (`Dockerfile.server`)

The `Dockerfile.server` builds a Kali Linux-based image with:
- Python environment setup
- Security testing tools (nmap, nuclei)
- HexStrike AI installation
- Root access for security tools (required for nmap, etc.)

### 3. MCP Configuration (`mcp.json`)

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

## Usage with GitHub Copilot

1. Open VS Code with the project
2. Ensure MCP configuration is loaded
3. Use Copilot commands to interact with the security testing environment
4. Execute tests through the MCP interface

## Security Note

This environment contains intentionally vulnerable services for testing. Do not expose to public networks.

## Network Configuration

Custom network `hexnet` (172.25.0.0/24) for isolation and controlled testing.
