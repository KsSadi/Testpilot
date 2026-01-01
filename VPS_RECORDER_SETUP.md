# VPS Browser Recorder Setup Guide

This guide explains how to set up the Browser Automation Recorder with remote browser access on your VPS.

## 📊 System Requirements

| Resource | Minimum | Recommended | Your Server |
|----------|---------|-------------|-------------|
| RAM | 4 GB | 8 GB | ✅ 8 GB |
| CPU | 2 cores | 4 cores | - |
| Disk | 20 GB | 40 GB | - |
| OS | Ubuntu 20.04+ / Debian 11+ | Ubuntu 22.04 | - |

### RAM Usage per Session
- Chrome Browser: ~300-500 MB
- Xvfb (Virtual Display): ~50 MB
- VNC Server + noVNC: ~100 MB
- Node.js Service: ~100 MB
- **Total per session: ~500-700 MB**

**With 8GB RAM, you can run 8-10 concurrent recording sessions.**

---

## 🚀 Quick Start (3 Commands)

```bash
# 1. Run the setup script (installs everything)
sudo chmod +x setup-vps-recorder.sh
sudo ./setup-vps-recorder.sh

# 2. Configure your server IP
export SERVER_HOST=your-vps-ip-address

# 3. Start the service
VPS_MODE=true USE_VNC=true SERVER_HOST=$SERVER_HOST npm run recorder:vps
```

---

## 📋 Step-by-Step Installation

### Step 1: SSH into your VPS
```bash
ssh root@your-server-ip
```

### Step 2: Clone/Upload your project
```bash
cd /var/www  # or your preferred directory
git clone your-repo testpilot
cd testpilot
```

### Step 3: Run the setup script
```bash
sudo chmod +x setup-vps-recorder.sh
sudo ./setup-vps-recorder.sh
```

This installs:
- ✅ Xvfb (Virtual Display)
- ✅ x11vnc (VNC Server)
- ✅ noVNC (Web VNC Client)
- ✅ Fluxbox (Window Manager)
- ✅ Google Chrome
- ✅ Node.js 20.x

### Step 4: Install Node.js dependencies
```bash
npm install
```

### Step 5: Configure environment
```bash
# Copy example config
cp .env.recorder.example .env.recorder

# Edit with your server IP
nano .env.recorder
```

Set `SERVER_HOST` to your VPS IP address or domain.

### Step 6: Start the service
```bash
# Option A: Direct command (for testing)
VPS_MODE=true USE_VNC=true SERVER_HOST=your-ip npm run recorder:vps

# Option B: Using systemd (recommended for production)
sudo systemctl daemon-reload
sudo systemctl enable browser-recorder
sudo systemctl start browser-recorder
```

### Step 7: Open firewall ports
```bash
# For UFW
sudo ufw allow 3031/tcp
sudo ufw allow 6080:6090/tcp

# For iptables
sudo iptables -A INPUT -p tcp --dport 3031 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 6080:6090 -j ACCEPT
```

---

## 🔧 Configuration Options

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `SERVER_HOST` | Your VPS IP/domain | `localhost` |
| `RECORDER_PORT` | API port | `3031` |
| `VPS_MODE` | Enable VPS mode | `auto-detected` |
| `USE_VNC` | Enable remote viewing | `true` on VPS |
| `HEADLESS` | Headless mode (no UI) | `false` |
| `PUPPETEER_EXECUTABLE_PATH` | Chrome path | Auto-detected |

### Running with PM2 (Recommended)
```bash
# Install PM2
npm install -g pm2

# Start with PM2
VPS_MODE=true USE_VNC=true SERVER_HOST=your-ip pm2 start npm --name "recorder" -- run recorder

# Auto-start on reboot
pm2 startup
pm2 save
```

---

## 🌐 How It Works

1. **User clicks "Start Recording"** in your Laravel app
2. **Laravel calls Node.js service** on port 3031
3. **Node.js creates a VNC session**:
   - Starts Xvfb (virtual display :99, :100, etc.)
   - Starts Fluxbox (window manager)
   - Starts x11vnc (VNC server on port 5900+)
   - Starts websockify (noVNC proxy on port 6080+)
4. **Chrome launches** on the virtual display
5. **User gets a viewer URL** like `http://your-ip:6080/vnc.html?autoconnect=true`
6. **User opens the URL** and sees/controls the browser in their web browser
7. **Events are captured** and sent back to Laravel
8. **User clicks "Stop"** → Browser closes, VNC session cleaned up

---

## 📱 User Experience

When a user starts a recording on VPS, they will see:

```json
{
  "success": true,
  "message": "Browser launched! Click the link below to view and interact with the browser.",
  "sessionId": "abc123",
  "vncEnabled": true,
  "viewerUrl": "http://your-ip:6080/vnc.html?autoconnect=true"
}
```

They click the `viewerUrl` and see the browser in a new tab!

---

## 🔒 Security Recommendations

### 1. Use HTTPS with a Reverse Proxy (Nginx)
```nginx
server {
    listen 443 ssl;
    server_name your-domain.com;
    
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    
    # noVNC proxy
    location /vnc/ {
        proxy_pass http://localhost:6080/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }
    
    # Recorder API
    location /recorder/ {
        proxy_pass http://localhost:3031/;
    }
}
```

### 2. Add VNC Password (Optional)
Edit `vnc-session-manager.js` and add `-passwd yourpassword` to x11vnc args.

### 3. Restrict Access by IP
```bash
sudo ufw allow from your-office-ip to any port 6080:6090
```

---

## 🐛 Troubleshooting

### Error: "Cannot connect to browser automation service"
```bash
# Check if service is running
curl http://localhost:3031/health

# Check logs
journalctl -u browser-recorder -f
```

### Error: "Failed to start VNC session"
```bash
# Check if required packages are installed
which Xvfb x11vnc websockify

# Try starting Xvfb manually
Xvfb :99 -screen 0 1920x1080x24 &
```

### Chrome crashes immediately
```bash
# Check Chrome installation
google-chrome --version

# Run Chrome manually to see errors
DISPLAY=:99 google-chrome --no-sandbox --disable-gpu
```

### VNC viewer shows black screen
```bash
# Make sure Fluxbox is running
DISPLAY=:99 fluxbox &
```

---

## 📞 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/start` | Start recording (returns viewerUrl) |
| POST | `/stop` | Stop recording |
| GET | `/events/:sessionId` | Get captured events |
| GET | `/sessions` | List active sessions |
| GET | `/vnc/sessions` | List VNC sessions |
| GET | `/health` | Health check |

---

## 🎉 You're Done!

Your VPS is now configured for remote browser recording. Users can:
1. Click "Start Recording" in your app
2. Open the viewer URL in a new tab
3. Interact with the browser remotely
4. Stop recording and get Cypress code!
