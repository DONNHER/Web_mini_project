<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use App\Services\AI\AIServiceManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ChatbotController extends Controller
{
    protected $aiManager;

    public function __construct(AIServiceManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    public function index()
    {
        $messages = Session::get('chat_history', []);
        return view('chatbot.index', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = $request->message;
        $history = Session::get('chat_history', []);

        // Add user message to history
        $history[] = ['role' => 'user', 'content' => $userMessage];

        // Build context-aware prompt with real-time product data
        $prompt = $this->buildPrompt($userMessage, $history);

        try {
            // Use fallback logic to ensure service continuity if local Ollama node is offline
            $result = $this->aiManager->generateWithFallback($prompt, 'chat');
            $response = $result['text'];

            // Add AI response to history
            $history[] = ['role' => 'assistant', 'content' => $response];
            Session::put('chat_history', $history);

            return response()->json([
                'success' => true,
                'message' => $response,
                'provider' => $result['provider'] ?? 'unknown'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'The neural network is currently offline. Please check local node status or fallback credentials.',
            ], 500);
        }
    }

    public function clear()
    {
        Session::forget('chat_history');
        return redirect()->route('chatbot.index');
    }

    protected function buildPrompt(string $message, array $history): string
    {
        // Fetch active products to provide context to the AI
        $products = LoanProduct::where('is_active', true)->get();
        $productContext = "Current Available Loan Products:\n";

        foreach ($products as $product) {
            $productContext .= "- {$product->name}: Interest Rate: {$product->interest_rate}%, Term: {$product->duration_months} months, Amount Range: PHP " . number_format($product->min_amount) . " to " . number_format($product->max_amount) . ". Description: {$product->description}\n";
        }

        $context = "You are a highly knowledgeable financial assistant for 'LendingSystem'.
        Use the following product information to answer user questions accurately.
        If a user asks about a specific loan, refer to these details.
        Keep your answers professional, helpful, and concise.

        {$productContext}

        Recent Conversation History:
        ";

        foreach (array_slice($history, -5) as $msg) {
            $context .= ($msg['role'] === 'user' ? "User: " : "AI: ") . $msg['content'] . "\n";
        }

        $context .= "User: " . $message . "\nAI:";

        return $context;
    }
}
