#!/usr/bin/env node

/**
 * Browser Automation Service
 * Similar to Playwright Codegen - Launches browser and captures user interactions
 */

import puppeteer from 'puppeteer';
import express from 'express';
import { WebSocketServer } from 'ws';
import { readFileSync } from 'fs';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const app = express();
app.use(express.json());

const PORT = 3031;
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

        // Launch browser
        const browser = await puppeteer.launch({
            headless: false,
            defaultViewport: null,
            args: [
                '--start-maximized',
                '--disable-blink-features=AutomationControlled',
                '--disable-features=IsolateOrigins,site-per-process'
            ]
        });

        const page = await browser.newPage();

        // Store session
        const session = {
            sessionId,
            testCaseId,
            url,
            browser,
            page,
            events: [],
            startTime: Date.now(),
            ws: null
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

        // Handle page close
        page.on('close', () => {
            console.log(`Page closed for session: ${sessionId} - session will remain active`);
            // Don't stop session automatically - let user stop it manually
            // This prevents session from being deleted when navigating between pages
        });

        // Navigate to URL
        await page.goto(url, { waitUntil: 'domcontentloaded' });

        res.json({
            success: true,
            sessionId,
            message: 'Browser launched successfully',
            wsUrl: `ws://localhost:${PORT}/ws/${sessionId}`
        });

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
    res.json({ status: 'ok', service: 'browser-automation' });
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
    } catch (e) {
        console.error('Error closing browser:', e);
    }

    sessions.delete(sessionId);
    console.log(`Session ${sessionId} stopped and cleaned up`);
}

// Cleanup old stopped sessions every minute
setInterval(() => {
    const now = Date.now();
    const FIVE_MINUTES = 5 * 60 * 1000;
    
    for (const [sessionId, session] of sessions.entries()) {
        if (session.stopped && (now - session.stoppedAt) > FIVE_MINUTES) {
            sessions.delete(sessionId);
            console.log(`Cleaned up old session: ${sessionId}`);
        }
    }
}, 60000);

// Handle WebSocket upgrade
const server = app.listen(PORT, () => {
    console.log(`
╔═══════════════════════════════════════════════════════╗
║   Browser Automation Service (Codegen Mode)           ║
║   Similar to Playwright Codegen                       ║
╠═══════════════════════════════════════════════════════╣
║   HTTP API: http://localhost:${PORT}                    ║
║   WebSocket: ws://localhost:${PORT}/ws/{sessionId}      ║
╠═══════════════════════════════════════════════════════╣
║   Endpoints:                                          ║
║   POST /start   - Start recording                     ║
║   POST /stop    - Stop recording                      ║
║   GET  /events/:sessionId - Get captured events       ║
║   GET  /sessions - List active sessions               ║
║   GET  /health  - Health check                        ║
╚═══════════════════════════════════════════════════════╝
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
    
    for (const [sessionId, session] of sessions.entries()) {
        await stopSession(sessionId);
    }
    
    server.close(() => {
        console.log('Server closed');
        process.exit(0);
    });
});
