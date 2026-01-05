#!/usr/bin/env node

/**
 * Browser Automation Service
 * Similar to Playwright Codegen - Launches browser and captures user interactions
 * Supports VPS deployment with noVNC for remote browser access
 */

import puppeteer from 'puppeteer';
import express from 'express';
import { WebSocketServer } from 'ws';
import { readFileSync } from 'fs';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';
import { vncManager } from './vnc-session-manager.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const app = express();
app.use(express.json());

// Enable CORS for frontend access
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    if (req.method === 'OPTIONS') {
        return res.sendStatus(200);
    }
    next();
});

const PORT = process.env.RECORDER_PORT || 3031;

// Platform detection - Windows doesn't have DISPLAY variable, so we need explicit check
const IS_WINDOWS = process.platform === 'win32';

// Environment detection for VPS/Server deployment
// On Windows: Never use VPS mode or VNC unless explicitly set
// On Linux: Check DISPLAY variable for VPS detection
const IS_VPS = process.env.VPS_MODE === 'true' || (!IS_WINDOWS && (process.env.DISPLAY === undefined || process.env.DISPLAY === ''));
const USE_VNC = process.env.USE_VNC === 'true' && !IS_WINDOWS;  // VNC only works on Linux
const USE_HEADLESS = process.env.HEADLESS === 'true' && !USE_VNC;  // Headless only if VNC is disabled
const SERVER_HOST = process.env.SERVER_HOST || 'localhost';  // Your VPS IP or domain

console.log(`[CONFIG] Platform: ${IS_WINDOWS ? 'Windows' : 'Linux/Unix'}, VPS Mode: ${IS_VPS}, Use VNC: ${USE_VNC}, Headless: ${USE_HEADLESS}, Server Host: ${SERVER_HOST}`);
const sessions = new Map();

// WebSocket server for real-time event streaming
const wss = new WebSocketServer({ noServer: true });

wss.on('connection', (ws, sessionId) => {
    console.log(`WebSocket connected for session: ${sessionId}`);
    
    if (sessions.has(sessionId)) {
        const session = sessions.get(sessionId);
        session.ws = ws;
    }
});

// Load event capture script
const captureScript = readFileSync(join(__dirname, 'event-capture.js'), 'utf-8');

/**
 * Start recording session
 * POST /start
 * Body: { sessionId, url, testCaseId }
 */
app.post('/start', async (req, res) => {
    const { sessionId, url, testCaseId } = req.body;

    if (!sessionId || !url) {
        return res.status(400).json({ error: 'sessionId and url are required' });
    }

    try {
        console.log(`Starting recording session: ${sessionId} for ${url}`);

        let vncSession = null;
        let displayEnv = process.env.DISPLAY;

        // Start VNC session for remote viewing on VPS
        if (USE_VNC) {
            console.log(`[VNC] Starting VNC session for remote browser access...`);
            try {
                vncSession = await vncManager.startSession(sessionId);
                displayEnv = vncSession.displayEnv;
                console.log(`[VNC] Session ready - Display: ${displayEnv}, noVNC Port: ${vncSession.noVncPort}`);
            } catch (vncError) {
                console.error(`[VNC] Failed to start VNC session:`, vncError.message);
                return res.status(500).json({
                    error: 'Failed to start VNC session',
                    message: vncError.message,
                    hint: 'Make sure Xvfb, x11vnc, and websockify are installed'
                });
            }
        }

        // Browser launch options based on environment
        const browserOptions = {
            headless: USE_HEADLESS ? 'new' : false,
            defaultViewport: USE_VNC ? { width: 1920, height: 1080 } : null,
            executablePath: process.env.PUPPETEER_EXECUTABLE_PATH || undefined,
            args: [
                '--start-maximized',
                '--disable-blink-features=AutomationControlled',
                '--disable-features=IsolateOrigins,site-per-process',
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
                // Window size for VNC
                ...(USE_VNC ? ['--window-size=1920,1080'] : [])
            ],
            // Set DISPLAY environment for VNC
            env: USE_VNC ? { ...process.env, DISPLAY: displayEnv } : process.env
        };

        console.log(`[CONFIG] Launching browser with options:`, {
            headless: browserOptions.headless,
            display: displayEnv,
            executablePath: browserOptions.executablePath || 'default',
            argsCount: browserOptions.args.length
        });

        // Launch browser
        const browser = await puppeteer.launch(browserOptions);

        const page = await browser.newPage();

        // Store session with VNC info
        const session = {
            sessionId,
            testCaseId,
            url,
            browser,
            page,
            events: [],
            startTime: Date.now(),
            ws: null,
            // VNC session info
            vncSession: vncSession ? {
                display: vncSession.display,
                vncPort: vncSession.vncPort,
                noVncPort: vncSession.noVncPort,
                viewerUrl: `http://${SERVER_HOST}:${vncSession.noVncPort}/vnc.html?autoconnect=true`
            } : null
        };
        sessions.set(sessionId, session);

        // Set user agent to avoid detection
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

        // PREVENT NEW TABS: Intercept new page creation and redirect to main page
        browser.on('targetcreated', async (target) => {
            if (target.type() === 'page') {
                try {
                    const newPage = await target.page();
                    if (newPage && newPage !== session.page) {
                        const newUrl = target.url();
                        console.log(`[RECORDER] New tab detected, redirecting main page to: ${newUrl}`);
                        
                        // Navigate the main page to this URL instead
                        if (newUrl && newUrl !== 'about:blank') {
                            await session.page.goto(newUrl, { waitUntil: 'domcontentloaded' }).catch(err => {
                                console.error('Error navigating main page:', err);
                            });
                        }
                        
                        // Close the new tab immediately
                        await newPage.close().catch(err => {
                            console.error('Error closing new tab:', err);
                        });
                        
                        console.log(`[RECORDER] New tab closed, navigation handled in main page`);
                    }
                } catch (e) {
                    console.error('Error handling new target:', e);
                }
            }
        });

        // Inject event capture script on every page (including cross-origin navigations)
        await page.evaluateOnNewDocument(captureScript);

        // Track all frames and inject script into iframes/cross-origin frames
        page.on('framenavigated', async (frame) => {
            try {
                // Re-inject script on every frame navigation (including cross-origin)
                await frame.evaluate(captureScript).catch(err => {
                    // Silently fail for cross-origin frames (expected)
                    console.log(`Cannot inject into frame (likely cross-origin): ${frame.url()}`);
                });
                console.log(`Frame navigated: ${frame.url()}`);
            } catch (e) {
                // Expected for cross-origin frames
            }
        });

        // Also inject on every page load/reload
        page.on('load', async () => {
            try {
                await page.evaluate(captureScript);
                console.log(`Script re-injected after page load: ${page.url()}`);
            } catch (e) {
                console.error('Failed to re-inject script:', e.message);
            }
        });

        // Listen for console messages from injected script
        page.on('console', async (msg) => {
            const text = msg.text();
            
            // Log all recorder messages for debugging
            if (text.startsWith('[RECORDER]')) {
                console.log(text);
            }
            
            if (text.startsWith('[EVENT_CAPTURED]')) {
                try {
                    const eventData = JSON.parse(text.replace('[EVENT_CAPTURED]', ''));
                    session.events.push(eventData);

                    // Send to WebSocket if connected
                    if (session.ws && session.ws.readyState === 1) {
                        session.ws.send(JSON.stringify({
                            type: 'event',
                            data: eventData
                        }));
                    }

                    console.log(`✓ Event captured: ${eventData.type} on ${eventData.selector || eventData.url}`);
                } catch (e) {
                    console.error('Failed to parse event:', e);
                }
            }
        });

        // Handle browser close (when user manually closes the browser)
        browser.on('disconnected', async () => {
            console.log(`Browser closed manually for session: ${sessionId}`);
            session.browserClosed = true;
            session.stopped = true;
            session.stoppedAt = Date.now();
            
            // Stop VNC session if active
            if (USE_VNC && session.vncSession) {
                await vncManager.stopSession(sessionId);
            }
            
            // Notify via WebSocket if connected
            if (session.ws && session.ws.readyState === 1) {
                session.ws.send(JSON.stringify({
                    type: 'browser_closed',
                    message: 'Browser was closed manually'
                }));
            }
        });

        // Handle page close
        page.on('close', () => {
            console.log(`Page closed for session: ${sessionId} - session will remain active`);
            // Don't stop session automatically - let user stop it manually
            // This prevents session from being deleted when navigating between pages
        });

        // Navigate to URL
        await page.goto(url, { waitUntil: 'domcontentloaded' });

        // Build response with VNC viewer URL if applicable
        const response = {
            success: true,
            sessionId,
            message: USE_VNC 
                ? 'Browser launched! Open the viewer URL to see and interact with the browser.'
                : 'Browser launched successfully',
            wsUrl: `ws://${SERVER_HOST}:${PORT}/ws/${sessionId}`,
            browserLaunched: true
        };

        // Add VNC viewer info for remote access
        if (session.vncSession) {
            response.vncEnabled = true;
            response.viewerUrl = session.vncSession.viewerUrl;
            response.message = `Browser launched! View and interact at: ${session.vncSession.viewerUrl}`;
        }

        res.json(response);

    } catch (error) {
        console.error('Error starting session:', error);
        res.status(500).json({
            error: 'Failed to start recording',
            message: error.message
        });
    }
});

/**
 * Stop recording session
 * POST /stop
 * Body: { sessionId }
 */
app.post('/stop', async (req, res) => {
    const { sessionId } = req.body;

    if (!sessionId) {
        return res.status(400).json({ error: 'sessionId is required' });
    }

    try {
        const session = sessions.get(sessionId);
        
        if (!session) {
            return res.status(404).json({ error: 'Session not found' });
        }

        const events = session.events;
        
        // Close browser but keep session data for code generation
        try {
            if (session.browser) {
                await session.browser.close();
                session.browser = null;
            }
            if (session.ws) {
                session.ws.close();
                session.ws = null;
            }
            // Stop VNC session if active
            if (USE_VNC && session.vncSession) {
                await vncManager.stopSession(sessionId);
                session.vncSession = null;
            }
        } catch (e) {
            console.error('Error closing browser:', e);
        }

        // Mark session as stopped but don't delete it yet
        session.stopped = true;
        session.stoppedAt = Date.now();
        
        console.log(`Session ${sessionId} stopped, keeping events for code generation`);

        res.json({
            success: true,
            message: 'Recording stopped',
            eventsCount: events.length,
            events: events
        });

    } catch (error) {
        console.error('Error stopping session:', error);
        res.status(500).json({
            error: 'Failed to stop recording',
            message: error.message
        });
    }
});

/**
 * Get session events
 * GET /events/:sessionId
 */
app.get('/events/:sessionId', (req, res) => {
    const { sessionId } = req.params;
    const session = sessions.get(sessionId);

    if (!session) {
        return res.status(404).json({ error: 'Session not found' });
    }

    res.json({
        success: true,
        sessionId,
        eventsCount: session.events.length,
        events: session.events,
        duration: Date.now() - session.startTime
    });
});

/**
 * Get all active sessions
 * GET /sessions
 */
app.get('/sessions', (req, res) => {
    const activeSessions = Array.from(sessions.entries()).map(([id, session]) => ({
        sessionId: id,
        testCaseId: session.testCaseId,
        url: session.url,
        eventsCount: session.events.length,
        duration: Date.now() - session.startTime
    }));

    res.json({
        success: true,
        count: activeSessions.length,
        sessions: activeSessions
    });
});

/**
 * Health check
 * GET /health
 */
app.get('/health', (req, res) => {
    res.json({ 
        status: 'ok', 
        service: 'browser-automation',
        vncEnabled: USE_VNC,
        isVPS: IS_VPS,
        activeSessions: sessions.size
    });
});

/**
 * Get VNC sessions info
 * GET /vnc/sessions
 */
app.get('/vnc/sessions', (req, res) => {
    if (!USE_VNC) {
        return res.json({
            enabled: false,
            message: 'VNC is not enabled on this server'
        });
    }

    const vncSessions = vncManager.getAllSessions();
    res.json({
        enabled: true,
        sessions: vncSessions
    });
});

/**
 * Stop session helper
 */
async function stopSession(sessionId) {
    const session = sessions.get(sessionId);
    if (!session) return;

    try {
        if (session.browser) {
            await session.browser.close();
        }
        if (session.ws) {
            session.ws.close();
        }
        // Stop VNC session
        if (USE_VNC) {
            await vncManager.stopSession(sessionId);
        }
    } catch (e) {
        console.error('Error closing browser:', e);
    }

    sessions.delete(sessionId);
    console.log(`Session ${sessionId} stopped and cleaned up`);
}

// Cleanup old stopped sessions every minute
setInterval(async () => {
    const now = Date.now();
    const FIVE_MINUTES = 5 * 60 * 1000;
    
    for (const [sessionId, session] of sessions.entries()) {
        if (session.stopped && (now - session.stoppedAt) > FIVE_MINUTES) {
            // Also cleanup VNC session
            if (USE_VNC) {
                await vncManager.stopSession(sessionId);
            }
            sessions.delete(sessionId);
            console.log(`Cleaned up old session: ${sessionId}`);
        }
    }
}, 60000);

// Handle WebSocket upgrade
const server = app.listen(PORT, () => {
    console.log(`
╔════════════════════════════════════════════════════════════════╗
║   Browser Automation Service (Codegen Mode)                    ║
║   Similar to Playwright Codegen                                ║
╠════════════════════════════════════════════════════════════════╣
║   Environment: ${IS_VPS ? 'VPS/Server' : 'Local/Desktop'}                                       ║
║   VNC Remote Access: ${USE_VNC ? 'ENABLED ✓' : 'DISABLED'}                              ║
║   Server Host: ${SERVER_HOST.padEnd(42)}║
╠════════════════════════════════════════════════════════════════╣
║   HTTP API: http://${SERVER_HOST}:${PORT}                               ║
║   WebSocket: ws://${SERVER_HOST}:${PORT}/ws/{sessionId}                 ║
╠════════════════════════════════════════════════════════════════╣
║   Endpoints:                                                   ║
║   POST /start   - Start recording (returns VNC viewer URL)     ║
║   POST /stop    - Stop recording                               ║
║   GET  /events/:sessionId - Get captured events                ║
║   GET  /sessions - List active sessions                        ║
║   GET  /vnc/sessions - List VNC sessions                       ║
║   GET  /health  - Health check                                 ║
╚════════════════════════════════════════════════════════════════╝
    `);
});

server.on('upgrade', (request, socket, head) => {
    const sessionId = request.url.replace('/ws/', '');
    
    wss.handleUpgrade(request, socket, head, (ws) => {
        wss.emit('connection', ws, sessionId);
    });
});

// Graceful shutdown
process.on('SIGINT', async () => {
    console.log('\nShutting down gracefully...');
    
    // Stop all VNC sessions first
    if (USE_VNC) {
        console.log('Stopping all VNC sessions...');
        await vncManager.stopAllSessions();
    }
    
    for (const [sessionId, session] of sessions.entries()) {
        await stopSession(sessionId);
    }
    
    server.close(() => {
        console.log('Server closed');
        process.exit(0);
    });
});

// Handle uncaught exceptions
process.on('uncaughtException', async (err) => {
    console.error('Uncaught Exception:', err);
    if (USE_VNC) {
        await vncManager.stopAllSessions();
    }
    process.exit(1);
});

process.on('unhandledRejection', (reason, promise) => {
    console.error('Unhandled Rejection at:', promise, 'reason:', reason);
});
