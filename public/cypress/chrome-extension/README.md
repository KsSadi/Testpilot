# Cypress Event Capture - Chrome Extension

A Chrome extension that automatically captures user events on any website for Cypress testing.

## Installation

1. Open Chrome and go to `chrome://extensions/`
2. Enable "Developer mode" (toggle in top-right corner)
3. Click "Load unpacked"
4. Select this folder (`public/cypress/chrome-extension`)
5. The extension icon will appear in your toolbar

## Setup

1. **Get Session ID from Dashboard**
   - Open http://127.0.0.1:3030/cypress/bookmarklet
   - Copy the Session ID displayed

2. **Configure Extension**
   - Click the extension icon in Chrome toolbar
   - Enter Server URL: `http://127.0.0.1:3030`
   - Paste the Session ID
   - Click "💾 Save Settings"

3. **Enable Capturing**
   - Click "Enable" button
   - Visit any website
   - Events will be captured automatically!

## Features

✅ **Auto-capture on all pages** - No manual clicking needed
✅ **Persistent sessions** - Same session across all pages
✅ **Easy enable/disable** - Toggle capturing on/off
✅ **Works everywhere** - Google, Facebook, any website!

## How It Works

1. Extension injects capture script on every page load
2. Events (clicks, inputs, forms) are sent to your Laravel server
3. View all events in real-time on the dashboard
4. Export events as JSON for Cypress test generation

## Troubleshooting

**Events not showing in dashboard?**
- Make sure extension is "Enabled" (check popup)
- Verify Server URL is correct
- Check Session ID matches dashboard
- Ensure Laravel server is running

**Extension not loading?**
- Make sure you're using Chrome/Edge (Chromium-based)
- Check Developer mode is enabled
- Try removing and re-adding the extension

## Dashboard

View captured events at: http://127.0.0.1:3030/cypress/bookmarklet

## Support

For issues or questions, check the dashboard documentation.
