#!/bin/bash

#############################################
# VPS Setup Script for Browser Automation
# Remote Browser Recording with noVNC
#############################################

set -e  # Exit on error

echo "╔════════════════════════════════════════════════════════════╗"
echo "║  VPS Setup for Browser Automation with noVNC               ║"
echo "║  This script will install all required dependencies        ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "⚠️  Please run as root (sudo ./setup-vps-recorder.sh)"
    exit 1
fi

# Detect OS
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$NAME
    VER=$VERSION_ID
fi

echo "📌 Detected OS: $OS $VER"
echo ""

#############################################
# Step 1: Update System
#############################################
echo "📦 Step 1: Updating system packages..."
apt-get update
apt-get upgrade -y

#############################################
# Step 2: Install Xvfb (Virtual Display)
#############################################
echo ""
echo "📦 Step 2: Installing Xvfb (Virtual Display)..."
apt-get install -y xvfb

#############################################
# Step 3: Install VNC Server
#############################################
echo ""
echo "📦 Step 3: Installing x11vnc (VNC Server)..."
apt-get install -y x11vnc

#############################################
# Step 4: Install noVNC (Web Client)
#############################################
echo ""
echo "📦 Step 4: Installing noVNC and websockify..."
apt-get install -y novnc websockify python3-websockify

#############################################
# Step 5: Install Window Manager
#############################################
echo ""
echo "📦 Step 5: Installing Fluxbox (Lightweight Window Manager)..."
apt-get install -y fluxbox

#############################################
# Step 6: Install Google Chrome
#############################################
echo ""
echo "📦 Step 6: Installing Google Chrome..."

# Add Google's signing key
wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | apt-key add -

# Add Chrome repository
echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" > /etc/apt/sources.list.d/google-chrome.list

# Install Chrome
apt-get update
apt-get install -y google-chrome-stable

# Verify installation
CHROME_VERSION=$(google-chrome --version)
echo "✅ Chrome installed: $CHROME_VERSION"

#############################################
# Step 7: Install Node.js (if not present)
#############################################
echo ""
echo "📦 Step 7: Checking Node.js..."

if command -v node &> /dev/null; then
    NODE_VERSION=$(node --version)
    echo "✅ Node.js already installed: $NODE_VERSION"
else
    echo "Installing Node.js 20.x..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt-get install -y nodejs
    NODE_VERSION=$(node --version)
    echo "✅ Node.js installed: $NODE_VERSION"
fi

#############################################
# Step 8: Install Additional Dependencies
#############################################
echo ""
echo "📦 Step 8: Installing additional dependencies..."
apt-get install -y \
    fonts-liberation \
    libasound2 \
    libatk-bridge2.0-0 \
    libatk1.0-0 \
    libatspi2.0-0 \
    libcups2 \
    libdbus-1-3 \
    libdrm2 \
    libgbm1 \
    libgtk-3-0 \
    libnspr4 \
    libnss3 \
    libxcomposite1 \
    libxdamage1 \
    libxfixes3 \
    libxkbcommon0 \
    libxrandr2 \
    xdg-utils \
    psmisc

#############################################
# Step 9: Create systemd service (optional)
#############################################
echo ""
echo "📦 Step 9: Creating systemd service..."

# Get the directory of this script
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cat > /etc/systemd/system/browser-recorder.service << EOF
[Unit]
Description=Browser Automation Recorder Service
After=network.target

[Service]
Type=simple
User=root
WorkingDirectory=${SCRIPT_DIR}
Environment=VPS_MODE=true
Environment=USE_VNC=true
Environment=SERVER_HOST=YOUR_SERVER_IP
Environment=PUPPETEER_EXECUTABLE_PATH=/usr/bin/google-chrome-stable
ExecStart=/usr/bin/node ${SCRIPT_DIR}/app/Modules/Cypress/Services/BrowserAutomation/browser-launcher.js
Restart=on-failure
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

echo "✅ Systemd service created at /etc/systemd/system/browser-recorder.service"
echo ""
echo "⚠️  IMPORTANT: Edit the service file to set your SERVER_HOST!"
echo "   Run: sudo nano /etc/systemd/system/browser-recorder.service"
echo "   Change YOUR_SERVER_IP to your actual server IP or domain"

#############################################
# Step 10: Open Firewall Ports
#############################################
echo ""
echo "📦 Step 10: Configuring firewall (if ufw is active)..."

if command -v ufw &> /dev/null; then
    # Check if ufw is active
    if ufw status | grep -q "Status: active"; then
        echo "Opening ports 3031 (API), 6080-6090 (noVNC)..."
        ufw allow 3031/tcp comment 'Browser Recorder API'
        ufw allow 6080:6090/tcp comment 'noVNC Web Clients'
        echo "✅ Firewall ports opened"
    else
        echo "⚠️  UFW is not active, skipping firewall configuration"
    fi
else
    echo "⚠️  UFW not found, skipping firewall configuration"
    echo "   Make sure to open ports 3031 and 6080-6090 manually"
fi

#############################################
# Summary
#############################################
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║  ✅ Installation Complete!                                 ║"
echo "╠════════════════════════════════════════════════════════════╣"
echo "║                                                            ║"
echo "║  Installed Components:                                     ║"
echo "║  • Xvfb (Virtual Display)                                  ║"
echo "║  • x11vnc (VNC Server)                                     ║"
echo "║  • noVNC (Web VNC Client)                                  ║"
echo "║  • Fluxbox (Window Manager)                                ║"
echo "║  • Google Chrome                                           ║"
echo "║  • Node.js                                                 ║"
echo "║                                                            ║"
echo "╠════════════════════════════════════════════════════════════╣"
echo "║  Next Steps:                                               ║"
echo "║                                                            ║"
echo "║  1. Set your server IP in .env or environment:             ║"
echo "║     export SERVER_HOST=your-server-ip                      ║"
echo "║                                                            ║"
echo "║  2. Install Node.js dependencies:                          ║"
echo "║     npm install                                            ║"
echo "║                                                            ║"
echo "║  3. Start the recorder service:                            ║"
echo "║     VPS_MODE=true USE_VNC=true \\                          ║"
echo "║     SERVER_HOST=your-ip \\                                 ║"
echo "║     node browser-launcher.js                               ║"
echo "║                                                            ║"
echo "║  OR use systemd:                                           ║"
echo "║     sudo systemctl daemon-reload                           ║"
echo "║     sudo systemctl enable browser-recorder                 ║"
echo "║     sudo systemctl start browser-recorder                  ║"
echo "║                                                            ║"
echo "╚════════════════════════════════════════════════════════════╝"
