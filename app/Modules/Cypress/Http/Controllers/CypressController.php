<?php

namespace App\Modules\Cypress\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cypress\Models\TestCaseEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CypressController extends Controller
{
    private $testProcess = null;
    private $testResults = [];
    private $testSessionId = null;

    /**
     * Display the Cypress testing page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Check if being loaded in iframe via embed parameter
        if ($request->has('embed')) {
            return view('Cypress::standalone');
        }

        $data = [
            'pageTitle' => 'Cypress Testing',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Cypress Testing']
            ]
        ];

        return view('Cypress::index', $data);
    }

    /**
     * Display the bookmarklet page.
     *
     * @return \Illuminate\View\View
     */
    public function bookmarklet()
    {
        $data = [
            'pageTitle' => 'Cypress Bookmarklet',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Cypress', 'url' => route('cypress.index')],
                ['title' => 'Bookmarklet']
            ]
        ];

        return view('Cypress::bookmarklet', $data);
    }

    /**
     * Start a new Cypress test session
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function startTest(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        try {
            // Generate unique session ID
            $this->testSessionId = uniqid('cypress_test_', true);

            // Create test results directory
            $testDir = storage_path("app/cypress-tests/{$this->testSessionId}");
            if (!file_exists($testDir)) {
                mkdir($testDir, 0755, true);
            }

            // Initialize test results
            $this->testResults = [
                'session_id' => $this->testSessionId,
                'url' => $request->url,
                'start_time' => Carbon::now()->toISOString(),
                'status' => 'running',
                'events' => [],
                'errors' => []
            ];

            // Save initial test state
            file_put_contents(
                $testDir . '/test_results.json',
                json_encode($this->testResults, JSON_PRETTY_PRINT)
            );

            return response()->json([
                'success' => true,
                'message' => 'Test session started successfully',
                'session_id' => $this->testSessionId,
                'url' => $request->url
            ]);

        } catch (\Exception $e) {
            Log::error('Cypress test start error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to start test session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Capture events from the iframe
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function captureEvent(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'event' => 'required|array'
        ]);

        try {
            $sessionId = $request->session_id;
            $testDir = storage_path("app/cypress-tests/{$sessionId}");
            $testFile = $testDir . '/test_results.json';

            if (!file_exists($testFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test session not found'
                ], 404);
            }

            // Load existing results
            $results = json_decode(file_get_contents($testFile), true);

            // Add new event with timestamp
            $event = $request->event;
            $event['timestamp'] = Carbon::now()->toISOString();
            $event['sequence'] = count($results['events']) + 1;

            $results['events'][] = $event;
            $results['last_updated'] = Carbon::now()->toISOString();

            // Save updated results
            file_put_contents($testFile, json_encode($results, JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'message' => 'Event captured successfully',
                'event_count' => count($results['events'])
            ]);

        } catch (\Exception $e) {
            Log::error('Cypress event capture error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to capture event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stop the current test session
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function stopTest(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $sessionId = $request->session_id;
            $testDir = storage_path("app/cypress-tests/{$sessionId}");
            $testFile = $testDir . '/test_results.json';

            if (!file_exists($testFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test session not found'
                ], 404);
            }

            // Load and update results
            $results = json_decode(file_get_contents($testFile), true);
            $results['status'] = 'completed';
            $results['end_time'] = Carbon::now()->toISOString();
            $results['duration'] = Carbon::parse($results['start_time'])
                ->diffInSeconds(Carbon::now());

            // Save final results
            file_put_contents($testFile, json_encode($results, JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'message' => 'Test session stopped successfully',
                'session_id' => $sessionId,
                'event_count' => count($results['events']),
                'duration' => $results['duration']
            ]);

        } catch (\Exception $e) {
            Log::error('Cypress test stop error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to stop test session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export test results as JSON
     *
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportResults(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $sessionId = $request->session_id;

            // Check both regular and bookmarklet directories
            $testDir = storage_path("app/cypress-tests/{$sessionId}");
            $testFile = $testDir . '/test_results.json';

            $bookmarkletDir = storage_path("app/cypress-bookmarklet/{$sessionId}");
            $bookmarkletFile = $bookmarkletDir . '/events.json';

            if (file_exists($testFile)) {
                $file = $testFile;
                $results = json_decode(file_get_contents($testFile), true);
            } elseif (file_exists($bookmarkletFile)) {
                $file = $bookmarkletFile;
                $results = json_decode(file_get_contents($bookmarkletFile), true);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Test session not found'
                ], 404);
            }

            // Generate filename
            $filename = "cypress_test_results_{$sessionId}.json";

            // Return file download
            return response()->download($file, $filename, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => "attachment; filename=\"{$filename}\""
            ]);

        } catch (\Exception $e) {
            Log::error('Cypress export error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to export results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proxy a website to bypass X-Frame-Options restrictions
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function proxyWebsite(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        try {
            $url = $request->url;

            // Log the request for debugging
            \Log::info('Cypress Proxy Request', [
                'url' => $url,
                'method' => $request->method(),
                'ip' => $request->ip()
            ]);

            // Use cURL to fetch the website content with enhanced settings
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't auto-follow redirects - we'll handle them
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Increase timeout to 60 seconds
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // Connection timeout
            curl_setopt($ch, CURLOPT_ENCODING, ''); // Accept all encodings (gzip, deflate)
            curl_setopt($ch, CURLOPT_AUTOREFERER, true); // Automatically set Referer
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // Max redirects
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // Use HTTP/1.1

            // Handle cookies for OAuth flows
            $cookieFile = storage_path('app/cypress-cookies.txt');
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

            // Get headers to track redirects manually
            curl_setopt($ch, CURLOPT_HEADER, true); // Include headers in output
            curl_setopt($ch, CURLOPT_VERBOSE, false);

            // Add common headers to appear more like a real browser
            $headers = [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.9',
                'Accept-Encoding: gzip, deflate, br',
                'Cache-Control: max-age=0',
                'Upgrade-Insecure-Requests: 1',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Fetch-User: ?1'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Forward the HTTP method (POST, PUT, DELETE, etc.)
            $method = $request->method();
            if ($method !== 'GET') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

                // Forward request body for POST/PUT/PATCH
                if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
                    $postData = $request->all();
                    unset($postData['url']); // Remove our proxy URL parameter

                    // Check if it's JSON request
                    if ($request->isJson()) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Content-Type: application/json',
                            'Accept: application/json'
                        ]);
                    } else {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                    }
                }
            }

            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($content === false) {
                \Log::error('Cypress Proxy Error', [
                    'url' => $url,
                    'httpCode' => $httpCode,
                    'error' => $error
                ]);
                throw new \Exception("Failed to fetch website content. HTTP Code: $httpCode. Error: $error");
            }

            // Split headers and body
            $headers = substr($content, 0, $headerSize);
            $body = substr($content, $headerSize);

            // Check for redirects (3xx status codes)
            if ($httpCode >= 300 && $httpCode < 400) {
                // Extract Location header
                if (preg_match('/Location:\s*(.+)/i', $headers, $matches)) {
                    $redirectUrl = trim($matches[1]);

                    // Make absolute URL if relative
                    if (!parse_url($redirectUrl, PHP_URL_SCHEME)) {
                        $parsedOriginal = parse_url($url);
                        $baseUrl = $parsedOriginal['scheme'] . '://' . $parsedOriginal['host'];
                        if (isset($parsedOriginal['port'])) {
                            $baseUrl .= ':' . $parsedOriginal['port'];
                        }
                        $redirectUrl = $baseUrl . '/' . ltrim($redirectUrl, '/');
                    }

                    \Log::info('Cypress Proxy Redirect', [
                        'from' => $url,
                        'to' => $redirectUrl,
                        'httpCode' => $httpCode
                    ]);

                    // Return JavaScript redirect through proxy
                    $proxyRedirectUrl = route('cypress.proxy') . '?url=' . urlencode($redirectUrl);
                    return response("
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <meta http-equiv='refresh' content='0;url={$proxyRedirectUrl}'>
                            <script>window.location.href = '{$proxyRedirectUrl}';</script>
                        </head>
                        <body>Redirecting...</body>
                        </html>
                    ")->header('Content-Type', 'text/html');
                }
            }

            if ($httpCode >= 400) {
                \Log::error('Cypress Proxy HTTP Error', [
                    'url' => $url,
                    'httpCode' => $httpCode,
                    'error' => $error
                ]);
                throw new \Exception("Failed to fetch website content. HTTP Code: $httpCode. Error: $error");
            }

            \Log::info('Cypress Proxy Success', [
                'url' => $url,
                'httpCode' => $httpCode,
                'contentType' => $contentType,
                'contentLength' => strlen($body)
            ]);

            // Use the original URL for base URL calculation
            $urlToUse = $url;

            // Parse the URL to get the base domain
            $parsedUrl = parse_url($urlToUse);
            $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            if (isset($parsedUrl['port'])) {
                $baseUrl .= ':' . $parsedUrl['port'];
            }

            // Check if this is an HTML page or other content (JSON, XML, etc.)
            $isHtml = stripos($contentType, 'text/html') !== false;

            // Only process HTML content, pass through other content types as-is
            if ($isHtml && !empty($body)) {
                $body = $this->processHtmlContent($body, $baseUrl, $urlToUse);
            }

            // Return the content with appropriate headers
            return response($body)
                ->header('Content-Type', $contentType ?: 'text/html')
                ->header('X-Frame-Options', 'ALLOWALL')
                ->header('Content-Security-Policy', 'frame-ancestors *')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        } catch (\Exception $e) {
            \Log::error('Website proxy error: ' . $e->getMessage(), [
                'url' => $request->url ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            $targetUrl = $request->url ?? 'Unknown URL';

            // Return a detailed error page with fallback options
            $errorHtml = '
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Error Loading Website</title>
                    <style>
                        body {
                            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
                            padding: 40px;
                            background: #f8f9fa;
                            color: #333;
                        }
                        .container {
                            max-width: 700px;
                            margin: 0 auto;
                            background: white;
                            padding: 30px;
                            border-radius: 8px;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        }
                        .error-icon {
                            font-size: 48px;
                            margin-bottom: 20px;
                        }
                        h1 {
                            color: #dc3545;
                            margin-bottom: 20px;
                            font-size: 24px;
                        }
                        .url {
                            color: #666;
                            word-break: break-all;
                            background: #f8f9fa;
                            padding: 10px;
                            border-radius: 4px;
                            margin: 15px 0;
                            font-size: 14px;
                        }
                        .error-details {
                            background: #fff3cd;
                            border-left: 4px solid #ffc107;
                            padding: 15px;
                            margin: 20px 0;
                            text-align: left;
                            font-size: 14px;
                        }
                        .suggestions {
                            text-align: left;
                            margin-top: 20px;
                            padding: 15px;
                            background: #e7f3ff;
                            border-radius: 4px;
                        }
                        .suggestions ul {
                            margin: 10px 0;
                            padding-left: 20px;
                            font-size: 14px;
                        }
                        .suggestions li {
                            margin: 8px 0;
                        }
                        .action-buttons {
                            margin-top: 25px;
                            display: flex;
                            gap: 10px;
                            flex-wrap: wrap;
                        }
                        .btn {
                            padding: 10px 20px;
                            border-radius: 6px;
                            border: none;
                            cursor: pointer;
                            font-weight: 500;
                            text-decoration: none;
                            display: inline-block;
                            transition: all 0.2s;
                        }
                        .btn-primary {
                            background: #2563eb;
                            color: white;
                        }
                        .btn-primary:hover {
                            background: #1d4ed8;
                        }
                        .btn-secondary {
                            background: #6b7280;
                            color: white;
                        }
                        .btn-secondary:hover {
                            background: #4b5563;
                        }
                        .btn-success {
                            background: #16a34a;
                            color: white;
                        }
                        .btn-success:hover {
                            background: #15803d;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="error-icon">⚠️</div>
                        <h1>Unable to Load Website in Proxy</h1>
                        <p>Could not load: <div class="url">' . htmlspecialchars($targetUrl) . '</div></p>

                        <div class="error-details">
                            <strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '
                        </div>

                        <div class="suggestions">
                            <strong>Common causes:</strong>
                            <ul>
                                <li><strong>Security restrictions:</strong> The website blocks iframe embedding or external proxies</li>
                                <li><strong>Authentication required:</strong> The website requires login or has session management</li>
                                <li><strong>Network issues:</strong> The domain may not be accessible from your server</li>
                                <li><strong>SSL/Certificate issues:</strong> Problems with HTTPS connections</li>
                                <li><strong>Rate limiting:</strong> Too many requests to the target website</li>
                            </ul>

                            <strong>💡 Workarounds:</strong>
                            <ul>
                                <li><strong>Open in new tab:</strong> Click the button below to open the website directly</li>
                                <li><strong>Try simpler sites:</strong> Test with example.com or httpbin.org first</li>
                                <li><strong>Use localhost sites:</strong> Internal sites usually work better</li>
                            </ul>
                        </div>

                        <div class="action-buttons">
                            <a href="' . htmlspecialchars($targetUrl) . '" target="_blank" class="btn btn-primary">
                                🔗 Open Website in New Tab
                            </a>
                            <button onclick="window.parent.location.href=\'/cypress\'" class="btn btn-secondary">
                                ↩️ Go Back
                            </button>
                            <button onclick="copyToClipboard(\'' . htmlspecialchars($targetUrl) . '\')" class="btn btn-success">
                                📋 Copy URL
                            </button>
                        </div>
                    </div>

                    <script>
                        function copyToClipboard(text) {
                            navigator.clipboard.writeText(text).then(function() {
                                alert(\'URL copied to clipboard!\');
                            }).catch(function(err) {
                                console.error(\'Could not copy text: \', err);
                            });
                        }

                        // Send error info to parent window
                        if (window.parent && window.parent !== window) {
                            window.parent.postMessage({
                                type: "cypress-proxy-error",
                                url: "' . htmlspecialchars($targetUrl) . '",
                                error: "' . htmlspecialchars($e->getMessage()) . '"
                            }, "*");
                        }
                    </script>
                </body>
                </html>
            ';

            return response($errorHtml)
                ->header('Content-Type', 'text/html')
                ->header('X-Frame-Options', 'ALLOWALL')
                ->header('Content-Security-Policy', 'frame-ancestors *');
        }
    }

    /**
     * Process HTML content to fix relative URLs and inject event capture
     *
     * @param string $content
     * @param string $baseUrl
     * @param string $originalUrl
     * @return string
     */
    private function processHtmlContent($content, $baseUrl, $originalUrl)
    {
        // Parse the original URL to get the current path
        $parsedOriginalUrl = parse_url($originalUrl);
        $currentPath = isset($parsedOriginalUrl['path']) ? dirname($parsedOriginalUrl['path']) : '/';
        if ($currentPath === '.') $currentPath = '/';

        // Convert relative URLs to absolute URLs of the external site
        // Handle root-relative URLs (starting with /)
        $content = preg_replace_callback('/href="\/([^"]*)"/', function($matches) use ($baseUrl) {
            return 'href="' . $baseUrl . '/' . $matches[1] . '"';
        }, $content);

        $content = preg_replace_callback('/src="\/([^"]*)"/', function($matches) use ($baseUrl) {
            return 'src="' . $baseUrl . '/' . $matches[1] . '"';
        }, $content);

        $content = preg_replace_callback('/action="\/([^"]*)"/', function($matches) use ($baseUrl) {
            return 'action="' . $baseUrl . '/' . $matches[1] . '"';
        }, $content);

        // Handle relative URLs that don't start with / or http (e.g., "about", "../page")
        // For href attributes
        $content = preg_replace_callback('/href="(?!http|\/\/|javascript:|mailto:|tel:|#)([^"]+)"/', function($matches) use ($baseUrl, $currentPath) {
            $relativeUrl = $matches[1];
            if (strpos($relativeUrl, '../') === 0 || strpos($relativeUrl, './') === 0) {
                // Handle paths like ../page or ./page
                $fullPath = $currentPath . '/' . $relativeUrl;
                $fullPath = preg_replace('#/+#', '/', $fullPath); // Remove double slashes
                $fullPath = $this->normalizePath($fullPath);
                return 'href="' . $baseUrl . $fullPath . '"';
            } else {
                // Simple relative path like "about" or "page.html"
                return 'href="' . $baseUrl . $currentPath . '/' . $relativeUrl . '"';
            }
        }, $content);

        // Fix protocol-relative URLs
        $content = preg_replace('/src="\/\/([^"]*)"/', 'src="https://$1"', $content);
        $content = preg_replace('/href="\/\/([^"]*)"/', 'href="https://$1"', $content);

        // Remove or neutralize base tags that might interfere with our proxy
        $content = preg_replace('/<base\s+[^>]*href=[^>]*>/i', '<!-- base tag removed by proxy -->', $content);

        // Get the Laravel application URL
        $appUrl = url('/');

        // Inject event capture JavaScript before closing </body> tag
        $eventCaptureJs = '
        <script>
        (function() {
            // ===== AJAX/Fetch Proxy Interceptor =====
            // Intercept XMLHttpRequest to proxy AJAX calls
            (function() {
                var originalXHR = window.XMLHttpRequest;
                var proxyUrl = "' . $appUrl . '/cypress/proxy?url=";

                window.XMLHttpRequest = function() {
                    var xhr = new originalXHR();
                    var originalOpen = xhr.open;
                    var originalSend = xhr.send;

                    xhr.open = function(method, url, async, user, password) {
                        // Check if URL needs proxying (external or absolute)
                        if (url.indexOf("http://") === 0 || url.indexOf("https://") === 0) {
                            console.log("Proxying AJAX request:", url);
                            // Don\'t proxy if it\'s already going through our proxy
                            if (url.indexOf("/cypress/proxy") === -1) {
                                url = proxyUrl + encodeURIComponent(url);
                            }
                        }
                        return originalOpen.apply(xhr, arguments);
                    };

                    return xhr;
                };

                // Intercept Fetch API
                var originalFetch = window.fetch;
                window.fetch = function(url, options) {
                    if (typeof url === "string" && (url.indexOf("http://") === 0 || url.indexOf("https://") === 0)) {
                        console.log("Proxying fetch request:", url);
                        if (url.indexOf("/cypress/proxy") === -1) {
                            url = proxyUrl + encodeURIComponent(url);
                        }
                    }
                    return originalFetch.apply(window, [url, options]);
                };
            })();

            // Communicate with parent window (our Cypress module)
            function sendEventToParent(eventData) {
                try {
                    if (window.parent && window.parent !== window) {
                        window.parent.postMessage({
                            type: "cypress-event",
                            data: eventData
                        }, "*");
                    }
                } catch(e) {
                    console.log("Could not send event to parent:", e);
                }
            }

            // Generate XPath for an element
            function getElementXPath(element) {
                if (element.id) {
                    return "//*[@id=\"" + element.id + "\"]";
                }

                if (element === document.body) {
                    return "/html/body";
                }

                var ix = 0;
                var siblings = element.parentNode.childNodes;
                for (var i = 0; i < siblings.length; i++) {
                    var sibling = siblings[i];
                    if (sibling === element) {
                        var tagName = element.tagName.toLowerCase();
                        return getElementXPath(element.parentNode) + "/" + tagName + "[" + (ix + 1) + "]";
                    }
                    if (sibling.nodeType === 1 && sibling.tagName === element.tagName) {
                        ix++;
                    }
                }
            }

            // Get comprehensive element data
            function getElementData(element, eventType) {
                var data = {
                    type: eventType,
                    element: element.tagName || "UNKNOWN",
                    class: null,
                    id: null,
                    name: null,
                    xpath: getElementXPath(element),
                    elementType: null,
                    text: null,
                    value: null,
                    href: null,
                    src: null,
                    placeholder: null,
                    title: null,
                    alt: null,
                    timestamp: new Date().toISOString()
                };

                // Extract class attribute
                if (element.className && element.className.length > 0) {
                    data.class = element.className;
                }

                // Extract id attribute
                if (element.id && element.id.length > 0) {
                    data.id = element.id;
                }

                // Extract name attribute
                if (element.name && element.name.length > 0) {
                    data.name = element.name;
                }

                // Extract type attribute
                if (element.type && element.type.length > 0) {
                    data.elementType = element.type;
                }

                // Extract text content
                if (element.textContent && element.textContent.trim().length > 0) {
                    data.text = element.textContent.trim().substring(0, 100);
                } else if (element.innerText && element.innerText.trim().length > 0) {
                    data.text = element.innerText.trim().substring(0, 100);
                }

                // Extract value for form elements
                if (element.value !== undefined && element.value !== null && element.value !== "") {
                    data.value = element.value.substring(0, 100);
                }

                // Extract href for links
                if (element.href && element.href.length > 0) {
                    data.href = element.href;
                }

                // Extract src for images/media
                if (element.src && element.src.length > 0) {
                    data.src = element.src;
                }

                // Extract placeholder
                if (element.placeholder && element.placeholder.length > 0) {
                    data.placeholder = element.placeholder;
                }

                // Extract title
                if (element.title && element.title.length > 0) {
                    data.title = element.title;
                }

                // Extract alt text
                if (element.alt && element.alt.length > 0) {
                    data.alt = element.alt;
                }

                // For elements without direct text, try to get meaningful content
                if (!data.text) {
                    if (element.tagName === "IMG" && element.alt) {
                        data.text = element.alt;
                    } else if (element.tagName === "INPUT" && element.placeholder) {
                        data.text = element.placeholder;
                    } else if (element.title) {
                        data.text = element.title;
                    }
                }

                return data;
            }

            // Handle link clicks to redirect through proxy
            function handleLinkClick(e) {
                const target = e.target.closest("a");
                if (target && target.href) {
                    // Skip special links and anchor links on same page
                    if (target.href.startsWith("javascript:") ||
                        target.href.startsWith("mailto:") ||
                        target.href.startsWith("tel:")) {
                        return; // Let these work normally
                    }

                    // Skip pure anchor links (same page navigation)
                    // Extract the URL from current proxy URL
                    var currentProxyUrl = window.location.href;
                    var urlParam = new URLSearchParams(window.location.search).get("url");
                    var currentBaseUrl = urlParam || window.location.href;

                    // Check if it\'s just an anchor on the current page
                    if (target.href.indexOf("#") !== -1) {
                        var targetBase = target.href.split("#")[0];
                        var currentBase = currentBaseUrl.split("#")[0];
                        if (targetBase === currentBase || targetBase === "") {
                            console.log("Same page anchor - allowing default behavior");
                            return; // Same page anchor, allow it
                        }
                    }

                    e.preventDefault();
                    e.stopPropagation();

                    // Check if clicking a link to a different domain
                    var targetDomain = new URL(target.href).hostname;
                    var currentDomain = new URL(currentBaseUrl).hostname;

                    if (targetDomain !== currentDomain) {
                        console.warn("⚠️ WARNING: Link goes to different domain:", targetDomain);
                        console.warn("Original domain:", currentDomain);
                        console.warn("This may not work properly due to authentication or CORS restrictions");
                    }

                    // Send click event with complete data
                    var eventData = getElementData(target, "click");
                    eventData.coordinates = { x: e.clientX, y: e.clientY };
                    eventData.href = target.href;
                    eventData.crossDomain = (targetDomain !== currentDomain);
                    sendEventToParent(eventData);

                    // Redirect through proxy using absolute URL
                    let targetUrl = target.href;
                    let proxyUrl = "' . $appUrl . '/cypress/proxy?url=" + encodeURIComponent(targetUrl);
                    console.log("=== CYPRESS PROXY DEBUG ===");
                    console.log("Link clicked:", target.href);
                    console.log("Target URL:", targetUrl);
                    console.log("Proxy URL:", proxyUrl);
                    console.log("Current location:", window.location.href);
                    console.log("========================");
                    window.location.href = proxyUrl;
                }
            }

            // Handle form submissions to redirect through proxy
            function handleFormSubmit(e) {
                e.preventDefault();

                const form = e.target;
                let actionUrl = form.action || window.location.href;
                let method = (form.method || "GET").toUpperCase();

                // Extract the real URL from proxy URL if form action is already proxied
                if (actionUrl.indexOf("/cypress/proxy?url=") !== -1) {
                    // Already a proxy URL, extract the real URL
                    const urlMatch = actionUrl.match(/[?&]url=([^&]+)/);
                    if (urlMatch) {
                        actionUrl = decodeURIComponent(urlMatch[1]);
                    }
                }

                // Send form submit event with complete data
                var eventData = getElementData(form, "form_submit");
                eventData.form_action = actionUrl;
                eventData.form_method = method;
                eventData.form_enctype = form.enctype;
                sendEventToParent(eventData);

                // For GET forms, just redirect to the action URL with query params
                if (method === "GET") {
                    var formData = new FormData(form);
                    var params = new URLSearchParams(formData);
                    var separator = actionUrl.indexOf("?") !== -1 ? "&" : "?";
                    var targetUrl = actionUrl + separator + params.toString();
                    window.location.href = "' . $appUrl . '/cypress/proxy?url=" + encodeURIComponent(targetUrl);
                    return;
                }

                // For POST forms, create a new form that submits through proxy
                var proxyForm = document.createElement("form");
                proxyForm.method = method;
                proxyForm.action = "' . $appUrl . '/cypress/proxy?url=" + encodeURIComponent(actionUrl);

                // Copy all form fields to the new form
                var formData = new FormData(form);
                formData.forEach(function(value, key) {
                    var input = document.createElement("input");
                    input.type = "hidden";
                    input.name = key;
                    input.value = value;
                    proxyForm.appendChild(input);
                });

                // Add to body and submit
                document.body.appendChild(proxyForm);
                proxyForm.submit();
            }

            // Capture regular click events (for buttons, divs, etc.)
            document.addEventListener("click", function(e) {
                // Handle link clicks for redirection FIRST
                handleLinkClick(e);

                // Only capture if it\'s not a link (links are handled separately)
                if (!e.target.closest("a")) {
                    var eventData = getElementData(e.target, "click");
                    eventData.coordinates = { x: e.clientX, y: e.clientY };
                    sendEventToParent(eventData);
                }
            }, true);

            // Capture form submissions
            document.addEventListener("submit", handleFormSubmit);

            // Capture input changes
            document.addEventListener("input", function(e) {
                var eventData = getElementData(e.target, "input");
                eventData.input_type = e.target.type;
                eventData.value = e.target.value ? e.target.value.substring(0, 50) : "";
                eventData.checked = e.target.checked || null;
                eventData.selected = e.target.selected || null;
                sendEventToParent(eventData);
            });

            // Capture change events (for selects, checkboxes, radios, file inputs)
            document.addEventListener("change", function(e) {
                var eventData = getElementData(e.target, "change");
                eventData.input_type = e.target.type;
                eventData.value = e.target.value || "";
                eventData.checked = e.target.checked || null;
                eventData.selected = e.target.selected || null;
                
                // Handle file input
                if (e.target.type === "file" && e.target.files && e.target.files.length > 0) {
                    eventData.type = "file_upload";
                    eventData.fileCount = e.target.files.length;
                    eventData.files = [];
                    eventData.accept = e.target.accept || null;
                    eventData.multiple = e.target.multiple || false;
                    
                    // Extract file metadata
                    for (var i = 0; i < e.target.files.length; i++) {
                        var file = e.target.files[i];
                        eventData.files.push({
                            name: file.name,
                            size: file.size,
                            type: file.type,
                            lastModified: file.lastModified
                        });
                    }
                    
                    console.log("File upload detected:", eventData.files);
                }
                
                // Handle select dropdown
                if (e.target.tagName === "SELECT") {
                    eventData.selectedIndex = e.target.selectedIndex;
                    eventData.selectedText = e.target.options[e.target.selectedIndex]?.text || "";
                }
                
                sendEventToParent(eventData);
            });

            // Capture scroll events (commented out to reduce noise)
            // window.addEventListener("scroll", function() {
            //     sendEventToParent({
            //         type: "scroll",
            //         scrollX: window.scrollX,
            //         scrollY: window.scrollY,
            //         timestamp: new Date().toISOString()
            //     });
            // });

            // Notify parent that page is ready
            window.addEventListener("load", function() {
                sendEventToParent({
                    type: "page_loaded",
                    url: "' . $originalUrl . '",
                    title: document.title || "",
                    domain: window.location.hostname,
                    timestamp: new Date().toISOString()
                });
            });
        })();
        </script>
        ';

        // Insert the script before closing </body> tag, or at the end if no </body>
        if (strpos($content, '</body>') !== false) {
            $content = str_replace('</body>', $eventCaptureJs . '</body>', $content);
        } else {
            $content .= $eventCaptureJs;
        }

        return $content;
    }


    /**
     * Normalize a path by resolving . and .. segments
     *
     * @param string $path
     * @return string
     */
    private function normalizePath($path)
    {
        $parts = explode('/', $path);
        $normalized = [];

        foreach ($parts as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                array_pop($normalized);
            } else {
                $normalized[] = $part;
            }
        }

        return '/' . implode('/', $normalized);
    }

    /**
     * Serve the capture script
     *
     * @return \Illuminate\Http\Response
     */
    public function captureScript()
    {
        $scriptPath = public_path('cypress/capture-script.js');

        if (!file_exists($scriptPath)) {
            return response('console.error("Capture script not found");', 404)
                ->header('Content-Type', 'application/javascript');
        }

        $content = file_get_contents($scriptPath);

        return response($content)
            ->header('Content-Type', 'application/javascript')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }

    /**
     * Capture event from bookmarklet (no auth required)
     *
     * @param Request $request
     * @return JsonResponse
     */
    /**
     * Handle CORS preflight OPTIONS request
     */
    public function handleCorsOptions()
    {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With')
            ->header('Access-Control-Max-Age', '86400');
    }

    /**
     * Add CORS headers to response
     */
    private function addCorsHeaders($response)
    {
        return $response
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With');
    }

    public function captureEventBookmarklet(Request $request): JsonResponse
    {
        try {
            $event = $request->input('event', []);
            $sessionId = $event['session_id'] ?? 'unknown';

            // Extract enhanced event data
            $selectors = $event['selectors'] ?? [];
            $eventType = $event['type'] ?? 'unknown';

            // Store event in database with complete information
            $testCaseEvent = TestCaseEvent::create([
                'session_id' => $sessionId,
                'event_type' => $eventType,
                'selector' => $event['cypressSelector'] ?? null,
                'tag_name' => $event['tagName'] ?? null,
                'url' => $event['pageUrl'] ?? $event['url'] ?? null,
                'value' => $event['value'] ?? null,
                'inner_text' => $event['innerText'] ?? $event['text'] ?? null,
                'attributes' => !empty($selectors) ? json_encode($selectors) : null,
                'event_data' => json_encode($event),
                'is_saved' => false
            ]);

            // Get total event count for this session
            $eventCount = TestCaseEvent::where('session_id', $sessionId)->count();

            $response = response()->json([
                'success' => true,
                'message' => 'Event captured successfully',
                'event_count' => $eventCount,
                'session_id' => $sessionId
            ]);

            return $this->addCorsHeaders($response);

        } catch (\Exception $e) {
            Log::error('Bookmarklet event capture error: ' . $e->getMessage());

            $response = response()->json([
                'success' => false,
                'message' => 'Failed to capture event: ' . $e->getMessage()
            ], 500);

            return $this->addCorsHeaders($response);
        }
    }

    /**
     * Get events for a session
     *
     * @param Request $request
     * @return JsonResponse
     */
    /**
     * Get current session ID from dashboard
     */
    public function getCurrentSession(): JsonResponse
    {
        // Generate or get current session ID using timestamp
        $sessionId = session('cypress_current_session', now()->timestamp);

        // Store in session for consistency
        session(['cypress_current_session' => $sessionId]);

        return response()->json([
            'success' => true,
            'session_id' => (string)$sessionId
        ]);
    }

    public function getEvents(Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id');

        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID required'
            ], 400);
        }

        try {
            // Check both regular and bookmarklet directories
            $testFile = storage_path("app/cypress-tests/{$sessionId}/test_results.json");
            $bookmarkletFile = storage_path("app/cypress-bookmarklet/{$sessionId}/events.json");

            if (file_exists($testFile)) {
                $results = json_decode(file_get_contents($testFile), true);
            } elseif (file_exists($bookmarkletFile)) {
                $results = json_decode(file_get_contents($bookmarkletFile), true);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found',
                    'events' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'events' => $results['events'] ?? [],
                'event_count' => count($results['events'] ?? []),
                'session_id' => $sessionId
            ]);

        } catch (\Exception $e) {
            Log::error('Get events error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get events: ' . $e->getMessage()
            ], 500);
        }
    }
}
