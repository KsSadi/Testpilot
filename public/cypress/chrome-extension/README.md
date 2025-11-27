# Testpilot Event Capture - Chrome Extension

A Chrome extension that automatically captures user events on any website for automated testing with Testpilot.

## Installation

1. Open Chrome and go to `chrome://extensions/`
2. Enable "Developer mode" (toggle in top-right corner)
3. Click "Load unpacked"
4. Select this folder (`public/cypress/chrome-extension`)
5. The extension icon will appear in your toolbar

## Setup

1. **Create a Test Case**
   - Login to Testpilot: http://127.0.0.1:8000
   - Navigate to Projects → Select/Create Project
   - Create a new Test Case
   - Each test case has a unique permanent Session ID

2. **Get Session ID**
   - Open your test case page
   - Click "Setup Instructions" button
   - Copy the Session ID (format: `tc_timestamp_id`)

3. **Configure Extension**
   - Click the extension icon in Chrome toolbar
   - Enter Server URL: `http://127.0.0.1:8000`
   - Paste the Test Case Session ID
   - Click "💾 Save Settings"

4. **Enable Capturing**
   - Click "Enable" button
   - Visit any website
   - Events will be captured automatically!

5. **View Events**
   - Return to your test case page
   - Click "Start Live Capture" to see events in real-time
   - Click "Save Events" to persist them
   - Click "Clear Unsaved" to remove unwanted events

## Features

✅ **Auto-capture on all pages** - No manual clicking needed
✅ **Test case specific sessions** - Each test case has its own session
✅ **Persistent sessions** - Session ID never changes for a test case
✅ **Easy enable/disable** - Toggle capturing on/off
✅ **Works everywhere** - Google, Facebook, any website!
✅ **Real-time monitoring** - See events appear live on test case page

## How It Works

1. Extension injects capture script on every page load
2. Events (clicks, inputs, forms) are sent to Testpilot server
3. Events are stored in `test_case_events` table mapped by session_id
4. View all events in real-time on the test case page
5. Save events to mark them as permanent
6. Export events as JSON for test automation

## Session ID Format

Test case session IDs follow this format:
```
tc_1732689123_abc123def456
```
- Prefix: `tc_`
- Timestamp: When test case was created
- Unique ID: Random identifier

## Troubleshooting

**Events not showing in dashboard?**
- Make sure extension is "Enabled" (check popup)
- Verify Server URL is `http://127.0.0.1:8000`
- Check Session ID starts with `tc_` (not old format)
- Ensure you copied Session ID from test case page
- Verify Testpilot server is running
- Click "Start Live Capture" on test case page

**Wrong session ID format?**
- Old bookmarklet sessions won't work
- Must use Session ID from test case page
- Format must be: `tc_timestamp_uniqueid`

**Extension not loading?**
- Make sure you're using Chrome/Edge (Chromium-based)
- Check Developer mode is enabled
- Try removing and re-adding the extension

## Dashboard

View captured events at: http://127.0.0.1:3030/cypress/bookmarklet

## Support

For issues or questions, check the dashboard documentation.
