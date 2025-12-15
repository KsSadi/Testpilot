# 🚀 Quick Start - Browser Automation (Codegen)

## ⚡ 3-Step Setup

### Step 1: Install Dependencies
```bash
npm install
```

### Step 2: Start the Recorder Service
```bash
npm run recorder
```

Keep this terminal running!

### Step 3: Start Laravel
```bash
php artisan serve
```

## 🎯 Usage

1. Go to: `http://127.0.0.1:8000/projects/{project}/modules/{module}/test-cases/{testCase}`
2. Find the **"Auto Recorder (Codegen)"** section
3. Enter website URL (e.g., `https://www.google.com`)
4. Click **"Start Recording"**
5. Browser opens automatically - interact with it!
6. Click **"Stop Recording"**
7. Get Cypress code instantly! 🎉

## ✅ Features

- ✨ **Auto-launch browser** - No manual setup
- 🎥 **Real-time capture** - See events as you interact
- 🧠 **Smart selectors** - Optimal CSS/XPath generation
- 💾 **Save directly** - Code saved to test case
- 📥 **Download** - Export as `.cy.js` file

## 🔧 Troubleshooting

**Service Offline?**
```bash
# Make sure recorder is running
npm run recorder
```

**Browser not launching?**
```bash
# Reinstall dependencies
npm install puppeteer --save
```

## 📖 Full Guide

See [BROWSER_AUTOMATION_SETUP.md](BROWSER_AUTOMATION_SETUP.md) for complete documentation.

---

**That's it! You're ready to auto-generate Cypress tests! 🎊**
