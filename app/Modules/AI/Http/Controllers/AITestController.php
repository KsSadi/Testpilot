<?php

namespace App\Modules\AI\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\AI\Facades\AI;
use App\Modules\AI\Models\AIProvider;

class AITestController extends Controller
{
    /**
     * Show the AI test playground page
     */
    public function index()
    {
        $activeProvider = AIProvider::where('is_active', true)->first();
        
        return view('AI::test-playground', compact('activeProvider'));
    }

    /**
     * Generate AI response for Cypress testing
     */
    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:5000',
        ]);

        try {
            // System prompt focused on Cypress testing
            $systemPrompt = "You are an expert Cypress test automation assistant. Help users with:
- Writing Cypress test code
- Test selectors and assertions
- Best practices for E2E testing
- Debugging Cypress tests
- Test structure and organization
- Handling async operations
- Common Cypress patterns and solutions

Provide clear, practical code examples when applicable. Focus on Cypress-specific solutions.";

            // Simple one-liner! 🎉
            $response = AI::ask($request->prompt, [
                'system_prompt' => $systemPrompt
            ]);

            if ($response['success']) {
                return response()->json([
                    'success' => true,
                    'response' => $response['content'],
                    'tokens' => $response['tokens'],
                    'cost' => $response['cost'],
                    'response_time' => $response['response_time'],
                    'provider' => $response['provider'],
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => $response['error'] ?? 'AI generation failed. Please try again.'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
