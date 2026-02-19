<?php
/**
 * BuyNiger AI Chatbot Controller - Fixed Version
 */

namespace App\Http\Controllers;

use App\Models\AIChatSession;
use App\Models\AIChatMessage;
use App\Services\AI\AIService;
use App\Services\AI\AIDataHelper;
use App\Services\AI\AIActionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function open()
    {
        try {
            $user = Auth::user();
            
            $session = AIChatSession::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (!$session) {
                $session = AIChatSession::create([
                    'user_id' => $user->id,
                    'session_type' => 'support',
                    'status' => 'active',
                    'context' => [],
                    'message_count' => 0
                ]);
            }

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'messages' => AIChatMessage::where('session_id', $session->id)->orderBy('created_at')->take(30)->get()
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot open: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function send(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required',
                'message' => 'required|string|max:1000'
            ]);

            $user = Auth::user();
            $session = AIChatSession::findOrFail($request->session_id);

            if ($session->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Save user message
            AIChatMessage::create([
                'session_id' => $session->id,
                'role' => 'user',
                'content' => $request->message
            ]);

            // 1. Check for actionable commands (pass session for context)
            $actionHelper = new AIActionHelper($user, $session);
            $actionResult = $actionHelper->parseAndExecute($request->message);

            if ($actionResult) {
                $response = $actionResult['message'];
            } else {
                // 2. Use AI with data context
                $aiService = new AIService();
                
                if (!$aiService->isEnabled()) {
                    $response = "⚠️ AI is not configured. Please ask admin to add Groq API key in AI Settings.";
                } else {
                    $dataHelper = new AIDataHelper($user);
                    $systemPrompt = $this->buildSystemPrompt($user, $session, $dataHelper, $actionHelper);
                    
                    // Get recent messages for context
                    $recentMessages = AIChatMessage::where('session_id', $session->id)
                        ->orderBy('created_at', 'desc')
                        ->take(6)
                        ->get()
                        ->reverse()
                        ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
                        ->values()
                        ->toArray();
                    
                    $messages = array_merge(
                        [['role' => 'system', 'content' => $systemPrompt]],
                        $recentMessages
                    );
                    
                    try {
                        $response = $aiService->chat($messages);
                        // Clean markdown formatting from response
                        $response = $this->cleanResponse($response);
                    } catch (\Exception $e) {
                        Log::error('AI Error: ' . $e->getMessage());
                        $response = "❌ AI Error: " . $e->getMessage();
                    }
                }
            }

            // Save response
            $aiMessage = AIChatMessage::create([
                'session_id' => $session->id,
                'role' => 'assistant',
                'content' => $response
            ]);

            $session->increment('message_count');

            return response()->json(['success' => true, 'message' => $aiMessage]);

        } catch (\Exception $e) {
            Log::error('Chatbot send: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function buildSystemPrompt($user, $session, AIDataHelper $dataHelper, AIActionHelper $actionHelper): string
    {
        $fullContext = $dataHelper->getFullContext();
        $actions = $actionHelper->getAvailableActions();
        
        $sessionContext = "";
        if ($session->context) {
            if (isset($session->context['last_product_id'])) {
                $sessionContext = "\nLast created product: #{$session->context['last_product_id']} - {$session->context['last_product_name']}";
            }
        }

        $prompt = "You are BuyNiger AI assistant. You have FULL ACCESS to the user's data below. Use this data to answer their questions accurately.

===== USER DATA =====
{$fullContext}{$sessionContext}
===== END DATA =====

CAPABILITIES:
";
        foreach ($actions as $action) {
            $prompt .= "- {$action}\n";
        }

        $prompt .= "
CRITICAL RULES:
1. USE THE ACTUAL DATA ABOVE to answer questions. Never make up data.
2. When asked about products, orders, sales - refer to the exact names and numbers shown above.
3. DO NOT use markdown formatting. No asterisks (**), no hashtags (#), no backticks.
4. Write in plain, natural conversational text.
5. Use N for Naira currency (e.g., N50,000).
6. When listing items, use simple dashes (-) only.
7. Be specific - use actual product names, order numbers, and amounts from the data.

EXAMPLES:
User asks: 'how many products do I have?'
Good: 'You have 17 products. Here are some of them: Health & Beauty Item 1, Sports & Outdoors Item 1, Automotive Item 1...'

User asks: 'list my products'
Good: 'Here are your products:
- #15: Health & Beauty Item 1 - N10,000 (90 in stock)
- #16: Health & Beauty Item 2 - N10,000 (38 in stock)
...'

User asks: 'what is my balance?'
Good: 'Your available balance is N225,000.'";

        return $prompt;
    }

    /**
     * Remove markdown formatting from AI response
     */
    private function cleanResponse(string $response): string
    {
        // Remove ** bold markers
        $response = preg_replace('/\*\*([^*]+)\*\*/', '$1', $response);
        // Remove * italic markers
        $response = preg_replace('/\*([^*]+)\*/', '$1', $response);
        // Remove __ bold markers
        $response = preg_replace('/__([^_]+)__/', '$1', $response);
        // Remove _ italic markers
        $response = preg_replace('/_([^_]+)_/', '$1', $response);
        // Remove # headers
        $response = preg_replace('/^#+\s*/m', '', $response);
        // Remove ``` code blocks markers
        $response = preg_replace('/```[a-z]*\n?/', '', $response);
        // Replace ₦ with N for consistency
        $response = str_replace('₦', 'N', $response);
        // Clean up excess newlines
        $response = preg_replace('/\n{3,}/', "\n\n", $response);
        
        return trim($response);
    }

    private function getUserRole($user): string
    {
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) return 'superadmin';
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) return 'admin';
        if (method_exists($user, 'isVendor') && $user->isVendor()) return 'vendor';
        return 'customer';
    }

    public function history($sessionId)
    {
        $session = AIChatSession::findOrFail($sessionId);
        if ($session->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json([
            'messages' => AIChatMessage::where('session_id', $sessionId)->orderBy('created_at')->get()
        ]);
    }
}

