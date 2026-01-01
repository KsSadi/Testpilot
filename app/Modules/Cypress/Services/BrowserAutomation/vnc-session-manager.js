#!/usr/bin/env node

/**
 * VNC Session Manager
 * Manages virtual displays and VNC connections for remote browser access
 * Each recording session gets its own display and VNC port
 */

import { spawn, exec } from 'child_process';
import { promisify } from 'util';

const execAsync = promisify(exec);

class VNCSessionManager {
    constructor() {
        // Track active VNC sessions: sessionId -> { display, vncPort, noVncPort, processes }
        this.sessions = new Map();
        
        // Port ranges
        this.displayStart = 99;      // :99, :100, :101, etc.
        this.vncPortStart = 5900;    // 5999, 6000, 6001, etc.
        this.noVncPortStart = 6080;  // 6080, 6081, 6082, etc.
        
        // Track used ports
        this.usedDisplays = new Set();
        this.usedVncPorts = new Set();
        this.usedNoVncPorts = new Set();
    }

    /**
     * Get next available display number
     */
    getNextDisplay() {
        let display = this.displayStart;
        while (this.usedDisplays.has(display)) {
            display++;
        }
        this.usedDisplays.add(display);
        return display;
    }

    /**
     * Get next available VNC port
     */
    getNextVncPort() {
        let port = this.vncPortStart;
        while (this.usedVncPorts.has(port)) {
            port++;
        }
        this.usedVncPorts.add(port);
        return port;
    }

    /**
     * Get next available noVNC port
     */
    getNextNoVncPort() {
        let port = this.noVncPortStart;
        while (this.usedNoVncPorts.has(port)) {
            port++;
        }
        this.usedNoVncPorts.add(port);
        return port;
    }

    /**
     * Start a new VNC session for a recording
     * Returns: { display, vncPort, noVncPort, vncUrl }
     */
    async startSession(sessionId) {
        if (this.sessions.has(sessionId)) {
            return this.sessions.get(sessionId);
        }

        const display = this.getNextDisplay();
        const vncPort = this.getNextVncPort();
        const noVncPort = this.getNextNoVncPort();
        const displayEnv = `:${display}`;

        console.log(`[VNC] Starting session ${sessionId} on display ${displayEnv}, VNC port ${vncPort}, noVNC port ${noVncPort}`);

        const processes = [];

        try {
            // 1. Start Xvfb (virtual display)
            const xvfb = spawn('Xvfb', [
                displayEnv,
                '-screen', '0', '1920x1080x24',
                '-ac'  // Disable access control
            ], {
                stdio: 'ignore',
                detached: true
            });
            processes.push({ name: 'xvfb', process: xvfb });
            
            // Wait for Xvfb to start
            await this.sleep(1000);

            // 2. Start Fluxbox window manager
            const fluxbox = spawn('fluxbox', [], {
                env: { ...process.env, DISPLAY: displayEnv },
                stdio: 'ignore',
                detached: true
            });
            processes.push({ name: 'fluxbox', process: fluxbox });

            // Wait for fluxbox
            await this.sleep(500);

            // 3. Start x11vnc (VNC server)
            const x11vnc = spawn('x11vnc', [
                '-display', displayEnv,
                '-forever',          // Don't exit after first client disconnects
                '-nopw',             // No password (add -passwd for security)
                '-shared',           // Allow multiple connections
                '-rfbport', vncPort.toString(),
                '-xkb',              // Use XKEYBOARD extension
                '-noxrecord',
                '-noxfixes',
                '-noxdamage',
                '-wait', '5',
                '-defer', '5'
            ], {
                stdio: 'ignore',
                detached: true
            });
            processes.push({ name: 'x11vnc', process: x11vnc });

            // Wait for VNC server
            await this.sleep(1000);

            // 4. Start websockify (noVNC proxy)
            const websockify = spawn('websockify', [
                '--web=/usr/share/novnc/',
                noVncPort.toString(),
                `localhost:${vncPort}`
            ], {
                stdio: 'ignore',
                detached: true
            });
            processes.push({ name: 'websockify', process: websockify });

            // Wait for websockify
            await this.sleep(500);

            const sessionData = {
                sessionId,
                display,
                displayEnv,
                vncPort,
                noVncPort,
                processes,
                startTime: Date.now()
            };

            this.sessions.set(sessionId, sessionData);

            console.log(`[VNC] Session ${sessionId} started successfully`);
            
            return {
                display,
                displayEnv,
                vncPort,
                noVncPort,
                success: true
            };

        } catch (error) {
            console.error(`[VNC] Failed to start session ${sessionId}:`, error);
            
            // Cleanup any started processes
            for (const { process } of processes) {
                try {
                    process.kill('SIGTERM');
                } catch (e) {}
            }

            // Release ports
            this.usedDisplays.delete(display);
            this.usedVncPorts.delete(vncPort);
            this.usedNoVncPorts.delete(noVncPort);

            throw error;
        }
    }

    /**
     * Stop a VNC session
     */
    async stopSession(sessionId) {
        const session = this.sessions.get(sessionId);
        if (!session) {
            console.log(`[VNC] Session ${sessionId} not found`);
            return;
        }

        console.log(`[VNC] Stopping session ${sessionId}`);

        // Kill all processes
        for (const { name, process } of session.processes) {
            try {
                process.kill('SIGTERM');
                console.log(`[VNC] Killed ${name} for session ${sessionId}`);
            } catch (e) {
                console.error(`[VNC] Error killing ${name}:`, e.message);
            }
        }

        // Also kill by port (in case processes were orphaned)
        try {
            await execAsync(`fuser -k ${session.vncPort}/tcp 2>/dev/null || true`);
            await execAsync(`fuser -k ${session.noVncPort}/tcp 2>/dev/null || true`);
        } catch (e) {}

        // Release ports
        this.usedDisplays.delete(session.display);
        this.usedVncPorts.delete(session.vncPort);
        this.usedNoVncPorts.delete(session.noVncPort);

        this.sessions.delete(sessionId);
        console.log(`[VNC] Session ${sessionId} stopped`);
    }

    /**
     * Get session info
     */
    getSession(sessionId) {
        return this.sessions.get(sessionId);
    }

    /**
     * Get all active sessions
     */
    getAllSessions() {
        return Array.from(this.sessions.values()).map(s => ({
            sessionId: s.sessionId,
            display: s.display,
            vncPort: s.vncPort,
            noVncPort: s.noVncPort,
            uptime: Date.now() - s.startTime
        }));
    }

    /**
     * Stop all sessions (cleanup)
     */
    async stopAllSessions() {
        console.log(`[VNC] Stopping all ${this.sessions.size} sessions`);
        for (const sessionId of this.sessions.keys()) {
            await this.stopSession(sessionId);
        }
    }

    /**
     * Helper: Sleep
     */
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// Export singleton instance
export const vncManager = new VNCSessionManager();
export default VNCSessionManager;
