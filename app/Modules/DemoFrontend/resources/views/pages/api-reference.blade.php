@extends('DemoFrontend::layouts.page')

@section('title', 'API Reference')
@section('description', 'Complete API reference for TestPilot automation platform')

@section('styles')
<style>
    .api-section {
        background: white;
        border-radius: var(--radius-lg);
        padding: var(--space-xl);
        margin-bottom: var(--space-lg);
        border: 1px solid var(--gray-lighter);
    }

    .api-endpoint {
        background: var(--gray-lightest);
        padding: var(--space-md);
        border-radius: var(--radius-md);
        margin-bottom: var(--space-md);
        border-left: 4px solid var(--primary);
    }

    .method {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 0.875rem;
        margin-right: var(--space-sm);
    }

    .method.get { background: #10B981; color: white; }
    .method.post { background: #2563EB; color: white; }
    .method.put { background: #F59E0B; color: white; }
    .method.delete { background: #EF4444; color: white; }

    .code-block {
        background: var(--dark);
        color: var(--gray-lightest);
        padding: var(--space-md);
        border-radius: var(--radius-md);
        font-family: 'Fira Code', monospace;
        font-size: 0.875rem;
        overflow-x: auto;
        margin-top: var(--space-sm);
    }

    .param-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: var(--space-sm);
    }

    .param-table th {
        background: var(--gray-lightest);
        padding: var(--space-sm);
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid var(--gray-lighter);
    }

    .param-table td {
        padding: var(--space-sm);
        border-bottom: 1px solid var(--gray-lighter);
        color: var(--gray);
    }
</style>
@endsection

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">API Reference</h1>
        <p class="page-subtitle">Complete REST API documentation for TestPilot platform integration</p>
    </div>

    <div class="api-section">
        <h2 style="margin-bottom: var(--space-md);">🔐 Authentication</h2>
        <p style="color: var(--gray); margin-bottom: var(--space-md);">All API requests require authentication using Bearer tokens. Include your API token in the Authorization header.</p>
        
        <div class="code-block">Authorization: Bearer YOUR_API_TOKEN</div>
    </div>

    <div class="api-section">
        <h2 style="margin-bottom: var(--space-md);">📋 Projects</h2>
        
        <div class="api-endpoint">
            <div>
                <span class="method get">GET</span>
                <code>/api/projects</code>
            </div>
            <p style="margin-top: var(--space-sm); color: var(--gray);">List all projects</p>
            
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>page</td>
                        <td>integer</td>
                        <td>Page number for pagination</td>
                    </tr>
                    <tr>
                        <td>per_page</td>
                        <td>integer</td>
                        <td>Items per page (default: 15)</td>
                    </tr>
                </tbody>
            </table>

            <div class="code-block">{
  "data": [
    {
      "id": 1,
      "name": "E-commerce Tests",
      "description": "Test suite for online store",
      "created_at": "2025-12-30T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 10
  }
}</div>
        </div>

        <div class="api-endpoint">
            <div>
                <span class="method post">POST</span>
                <code>/api/projects</code>
            </div>
            <p style="margin-top: var(--space-sm); color: var(--gray);">Create a new project</p>
            
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Required</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>name</td>
                        <td>string</td>
                        <td>Yes</td>
                        <td>Project name</td>
                    </tr>
                    <tr>
                        <td>description</td>
                        <td>string</td>
                        <td>No</td>
                        <td>Project description</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="api-section">
        <h2 style="margin-bottom: var(--space-md);">📦 Test Cases</h2>
        
        <div class="api-endpoint">
            <div>
                <span class="method get">GET</span>
                <code>/api/projects/{project}/modules/{module}/test-cases</code>
            </div>
            <p style="margin-top: var(--space-sm); color: var(--gray);">Get all test cases in a module</p>
        </div>

        <div class="api-endpoint">
            <div>
                <span class="method post">POST</span>
                <code>/api/test-cases/{id}/generate-code</code>
            </div>
            <p style="margin-top: var(--space-sm); color: var(--gray);">Generate code from recorded events</p>
            
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>format</td>
                        <td>string</td>
                        <td>Code format: 'cypress' or 'playwright'</td>
                    </tr>
                </tbody>
            </table>

            <div class="code-block">{
  "format": "cypress",
  "code": "describe('Login Test', () => {\n  it('should login', () => {\n    cy.visit('...');\n  });\n});",
  "generated_at": "2025-12-30T10:30:00Z"
}</div>
        </div>
    </div>

    <div class="api-section">
        <h2 style="margin-bottom: var(--space-md);">🎬 Recording Sessions</h2>
        
        <div class="api-endpoint">
            <div>
                <span class="method post">POST</span>
                <code>/api/recording/start</code>
            </div>
            <p style="margin-top: var(--space-sm); color: var(--gray);">Start a new recording session</p>
            
            <table class="param-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Required</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>test_case_id</td>
                        <td>integer</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>url</td>
                        <td>string</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td>browser</td>
                        <td>string</td>
                        <td>No</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="api-endpoint">
            <div>
                <span class="method post">POST</span>
                <code>/api/recording/stop</code>
            </div>
            <p style="margin-top: var(--space-sm); color: var(--gray);">Stop the active recording session</p>
        </div>
    </div>

    <div class="api-section">
        <h2 style="margin-bottom: var(--space-md);">📊 Response Codes</h2>
        <table class="param-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Status</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>200</td>
                    <td>OK</td>
                    <td>Request successful</td>
                </tr>
                <tr>
                    <td>201</td>
                    <td>Created</td>
                    <td>Resource created successfully</td>
                </tr>
                <tr>
                    <td>400</td>
                    <td>Bad Request</td>
                    <td>Invalid request parameters</td>
                </tr>
                <tr>
                    <td>401</td>
                    <td>Unauthorized</td>
                    <td>Authentication required or failed</td>
                </tr>
                <tr>
                    <td>404</td>
                    <td>Not Found</td>
                    <td>Resource not found</td>
                </tr>
                <tr>
                    <td>500</td>
                    <td>Server Error</td>
                    <td>Internal server error</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="api-section">
        <h2 style="margin-bottom: var(--space-md);">💡 Rate Limiting</h2>
        <p style="color: var(--gray); line-height: 1.8;">
            API requests are limited to 100 requests per minute per API token. Rate limit information is included in response headers:
        </p>
        <div class="code-block">X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1735560000</div>
    </div>

    <div class="api-section">
        <h2 style="margin-bottom: var(--space-md);">🔗 Need Help?</h2>
        <p style="color: var(--gray); line-height: 1.8;">
            For more information or support with the API, visit our <a href="{{ route('landing.support') }}" style="color: var(--primary);">Support page</a> or join our <a href="{{ route('landing.community') }}" style="color: var(--primary);">Community</a>.
        </p>
    </div>
</div>
@endsection
