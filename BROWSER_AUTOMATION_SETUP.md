# 🎬 Browser Automation (Codegen) - Setup & Usage Guide

## 🚀 What Is This?

A **Playwright Codegen-style** browser automation system that:
- ✅ Automatically launches a real Chrome browser
- ✅ Records ALL user interactions (clicks, typing, navigation)
- ✅ Generates Cypress test code in real-time
- ✅ No manual setup or browser extensions needed
- ✅ User-friendly - just enter URL and click "Record"

---

## 📋 Prerequisites

- Node.js (v16 or higher)
- npm or yarn
- Chrome/Chromium browser

---

## 🔧 Installation Steps

### 1. Install Dependencies

```bash
# Install Node.js packages
npm install
```

This installs:
- `puppeteer` - Browser automation
- `express` - HTTP server
- `ws` - WebSocket support

### 2. Start Browser Automation Service

Open a **new terminal** and run:

```bash
npm run recorder
```

You should see:

```
╔═══════════════════════════════════════════════════════╗
║   Browser Automation Service (Codegen Mode)           ║
║   Similar to Playwright Codegen                       ║
╠═══════════════════════════════════════════════════════╣
║   HTTP API: http://localhost:3031                     ║
║   WebSocket: ws://localhost:3031/ws/{sessionId}       ║
╠═══════════════════════════════════════════════════════╣
║   Endpoints:                                          ║
║   POST /start   - Start recording                     ║
║   POST /stop    - Stop recording                      ║
║   GET  /events/:sessionId - Get captured events       ║
║   GET  /sessions - List active sessions               ║
║   GET  /health  - Health check                        ║
╚═══════════════════════════════════════════════════════╝
```

**Important:** Keep this terminal running!

### 3. Start Laravel Development Server

In another terminal:

```bash
php artisan serve
```

---

## 🎯 How to Use

### Step 1: Navigate to Test Case

Go to: `http://127.0.0.1:8000/projects/{project}/modules/{module}/test-cases/{testCase}`

### Step 2: Check Service Status

At the top of the page, look for the **Auto Recorder (Codegen)** section.

You should see: **Service Online** (green indicator)

If you see **Service Offline** (red), make sure the recorder service is running:
```bash
npm run recorder
```

### Step 3: Start Recording

1. Enter the website URL (e.g., `https://www.google.com`)
2. Click **Start Recording**
3. Browser will launch automatically
4. Interact with the website (click, type, navigate)
5. See events captured in real-time on the page

### Step 4: Stop Recording & Generate Code

1. Click **Stop Recording**
2. Cypress code is automatically generated
3. Review the generated code
4. **Copy**, **Download**, or **Save to Test Case**

---

## 🎨 Features

### ✨ Real-time Event Capture

- **Clicks** - All button/link clicks
- **Input** - Text typing in fields
- **Change** - Checkbox, radio, select changes
- **Navigation** - Page transitions

### 🎯 Smart Selector Generation

Priority order:
1. `data-testid` attributes
2. `id` attributes
3. `name` attributes
4. `aria-label` attributes
5. Class names
6. XPath (fallback)

### 💡 Visual Feedback

- Red highlight on hovered elements
- Live event feed
- Event counter
- Recording duration timer
- Service status indicator

### 📝 Generated Code Format

```javascript
describe('Test Case Name', () => {
    it('should perform recorded actions', () => {
        // Page Load
        cy.visit('https://example.com');
        
        // Interactions
        cy.get('#username').type('user@example.com');
        cy.get('#password').type('password123');
        cy.get('button[type="submit"]').click();
        
        // Assertions (you can add manually)
        cy.url().should('include', '/dashboard');
    });
});
```

---

## 🔄 Architecture

```
┌─────────────────┐
│  Laravel App    │
│  (Frontend UI)  │
└────────┬────────┘
         │ HTTP/WebSocket
         ▼
┌─────────────────┐
│  Node.js Server │
│  (Port 3031)    │
└────────┬────────┘
         │ Puppeteer API
         ▼
┌─────────────────┐
│  Chrome Browser │
│  (Automated)    │
└─────────────────┘
```

**Flow:**
1. User clicks "Start Recording" in Laravel UI
2. Laravel sends request to Node.js service
3. Node.js launches Chrome with Puppeteer
4. Event capture script is injected into browser
5. User interacts with website
6. Events are captured and sent back to Laravel via WebSocket
7. Laravel generates Cypress code from events

---

## 🛠️ Troubleshooting

### Service Offline Error

**Problem:** Red "Service Offline" indicator

**Solution:**
```bash
# Start the recorder service
npm run recorder
```

### Browser Not Launching

**Problem:** Click "Start Recording" but browser doesn't open

**Possible causes:**
1. Recorder service not running
2. Puppeteer not installed
3. Chrome not found

**Solutions:**
```bash
# Reinstall dependencies
npm install

# Check if Puppeteer installed correctly
npm list puppeteer

# Manually install Puppeteer
npm install puppeteer --save
```

### Connection Refused

**Problem:** Error connecting to `localhost:3031`

**Solution:**
1. Check if port 3031 is available
2. Check firewall settings
3. Try different port in `config/services.php`:

```php
'browser_automation' => [
    'url' => env('BROWSER_AUTOMATION_URL', 'http://localhost:3032'),
],
```

Then update `browser-launcher.js`:
```javascript
const PORT = 3032;
```

### No Events Captured

**Problem:** Browser opens but no events appear

**Solution:**
1. Check browser console for errors (F12)
2. Make sure you're interacting with the page
3. Some elements may be ignored - try different elements
4. Check WebSocket connection in Network tab

---

## ⚙️ Configuration

### Change Port

Edit [`app/Modules/Cypress/Services/BrowserAutomation/browser-launcher.js`](app/Modules/Cypress/Services/BrowserAutomation/browser-launcher.js):

```javascript
const PORT = 3031; // Change this
```

### Browser Options

Edit browser launch options:

```javascript
const browser = await puppeteer.launch({
    headless: false, // Set to true for headless mode
    defaultViewport: { width: 1920, height: 1080 }, // Custom viewport
    args: [
        '--start-maximized',
        '--window-size=1920,1080'
    ]
});
```

### Timeout Settings

Edit [`config/services.php`](config/services.php):

```php
'browser_automation' => [
    'url' => env('BROWSER_AUTOMATION_URL', 'http://localhost:3031'),
    'timeout' => env('BROWSER_AUTOMATION_TIMEOUT', 60), // Increase timeout
],
```

---

## 📚 API Endpoints

### Start Recording
```
POST http://localhost:3031/start
Body: { sessionId, url, testCaseId }
```

### Stop Recording
```
POST http://localhost:3031/stop
Body: { sessionId }
```

### Get Events
```
GET http://localhost:3031/events/:sessionId
```

### List Sessions
```
GET http://localhost:3031/sessions
```

### Health Check
```
GET http://localhost:3031/health
```

---

## 🎓 Best Practices

### 1. Clean Test URLs
Use clean, stable URLs:
✅ `https://example.com/login`
❌ `https://example.com/login?session=abc123&temp=xyz`

### 2. Stable Selectors
Add `data-testid` attributes to your app:
```html
<button data-testid="login-button">Login</button>
```

Generated code:
```javascript
cy.get('[data-testid="login-button"]').click();
```

### 3. Add Assertions Manually
Generated code captures interactions only. Add assertions:
```javascript
cy.get('#username').type('user@example.com');
cy.get('#password').type('password123');
cy.get('[data-testid="login-button"]').click();

// Add assertions
cy.url().should('include', '/dashboard');
cy.get('.welcome-message').should('be.visible');
```

### 4. One Action Per Recording
Record one feature at a time:
- ✅ Login flow
- ✅ Create item flow
- ✅ Delete item flow

Don't record everything in one session.

### 5. Review & Refactor
Always review generated code:
- Remove duplicate actions
- Add meaningful variable names
- Add comments
- Add assertions

---

## 🚦 Production Deployment

### Security Considerations

⚠️ **DO NOT expose port 3031 to the internet!**

The browser automation service should only be accessible:
- Locally (development)
- Via VPN (team access)
- Behind firewall (internal network)

### Deployment Options

**Option 1: Development Only**
- Keep service running on development machines only
- Don't deploy to production

**Option 2: Internal Network**
- Deploy on internal server
- Restrict access via firewall
- Use VPN for remote access

**Option 3: Containerized**
```dockerfile
# Dockerfile
FROM node:18
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY app/Modules/Cypress/Services/BrowserAutomation ./
CMD ["node", "browser-launcher.js"]
```

---

## 📝 Files Created

```
📦 Testpilot/
├── 📄 package.json (updated)
├── 📁 app/Modules/Cypress/
│   ├── 📁 Http/Controllers/
│   │   └── 📄 RecordingController.php (NEW)
│   ├── 📁 Services/
│   │   ├── 📁 BrowserAutomation/
│   │   │   ├── 📄 browser-launcher.js (NEW)
│   │   │   ├── 📄 event-capture.js (NEW)
│   │   │   └── 📄 RecordingSessionService.php (NEW)
│   │   ├── 📄 CodeGeneratorService.php
│   │   └── 📄 SelectorOptimizerService.php
│   ├── 📁 resources/views/test-cases/partials/
│   │   └── 📄 _recording_section.blade.php (NEW)
│   └── 📁 routes/
│       └── 📄 web.php (updated)
├── 📁 config/
│   └── 📄 services.php (updated)
└── 📄 BROWSER_AUTOMATION_SETUP.md (THIS FILE)
```

---

## 🎉 Success!

You now have a fully functional Playwright Codegen-style browser automation system!

**Next Steps:**
1. Start the recorder service: `npm run recorder`
2. Go to any test case page
3. Click "Start Recording"
4. Interact with any website
5. Get Cypress code automatically!

**Need Help?**
- Check the troubleshooting section above
- Review browser console for errors
- Check Node.js service terminal output
- Verify all dependencies are installed

Happy Testing! 🚀
