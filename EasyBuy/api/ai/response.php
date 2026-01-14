<?php
require_once __DIR__ . "/../../vendor/autoload.php";
require_once '../config.php';

use ArdaGnsrn\Ollama\Ollama;

header('Content-Type: text/plain');

// Load product data
$productData = file_get_contents(__DIR__ . "/data.txt");

// Get user input
$input = json_decode(file_get_contents('php://input'), true);
$userPrompt = trim($input['prompt'] ?? '');

if (empty($userPrompt)) {
    echo "Hi there! 👋 I'm your EasyBuy assistant. What groceries are you looking for today?";
    exit;
}

// Build STRICT AI prompt with boundaries
$prompt = "You are EasyBuy Store Assistant. Follow these rules STRICTLY:

1. ONLY answer questions about products from the list below
2. If asked about ANYTHING else (weather, math, recipes, politics, etc.), respond EXACTLY: \"I can only help with EasyBuy products!\"
3. Be friendly and use emojis for product questions
4. Keep responses under 50 words

PRODUCTS:
" . $productData . "

Customer: " . $userPrompt . "
Assistant:";

// Get AI response
try {
    $client = Ollama::client($_ENV['OLLAMA_BASE_URL']);
    $response = $client->completions()->create([
        'model' => 'gemma2:2b',
        'prompt' => $prompt,
        'stream' => true,
        'options' => [
            'num_predict' => 35,
            'temperature' => 0.2,  
            'num_ctx' => 512,
            'top_k' => 20,          
            'top_p' => 0.8,         
            'repeat_penalty' => 1.2,
            'stop' => ['\n\n', 'Customer:', 'Human:'], 
        ]
    ]);
    
    $aiResponse = trim($response->response ?? "");
    
    if (empty($aiResponse)) {
        echo "I'm sorry, I couldn't generate a response. Please try again.";
        exit;
    }
    
    // Post-validation: Detect off-topic responses
    $responseLower = strtolower($aiResponse);
    
    // Check for refusal patterns (good signs)
    $refusalPatterns = ['only help with', 'easybuy products', 'can\'t help', 'specialized in'];
    $isRefusal = false;
    foreach ($refusalPatterns as $pattern) {
        if (strpos($responseLower, $pattern) !== false) {
            $isRefusal = true;
            break;
        }
    }
    
    // Check for off-topic content (bad signs)
    $offTopicIndicators = [
        'weather', 'temperature', 'rain', 'sunny',
        'president', 'government', 'election', 'political',
        'calculate', 'equation', 'math problem',
        'recipe', 'how to cook', 'ingredients for',
        'capital of', 'country', 'history of',
        'as an ai', 'language model', 'i don\'t have access'
    ];
    
    $hasOffTopicContent = false;
    foreach ($offTopicIndicators as $indicator) {
        if (strpos($responseLower, $indicator) !== false && !$isRefusal) {
            $hasOffTopicContent = true;
            break;
        }
    }
    
    // Block if off-topic detected
    if ($hasOffTopicContent) {
        echo "I can only help with EasyBuy products! 😊 Ask me about groceries, prices, or stock availability.";
        exit;
    }
    
    // Check if response mentions products from the catalog
    $hasProductMention = false;
    $productNames = ['milk', 'egg', 'bread', 'rice', 'chicken', 'apple', 'banana']; // Add your actual products
    foreach ($productNames as $product) {
        if (strpos($responseLower, $product) !== false) {
            $hasProductMention = true;
            break;
        }
    }
    
    // If it's not a refusal and doesn't mention products, it's probably off-topic
    if (!$isRefusal && !$hasProductMention && strlen($userPrompt) > 15) {
        echo "I can only help with EasyBuy products! 😊 Ask me about groceries, prices, or stock availability.";
        exit;
    }
    
    echo $aiResponse;
    
} catch (Exception $e) {
    error_log("Ollama API Error: " . $e->getMessage());
    echo "Sorry, I'm having trouble connecting to the service. Please try again later.";
}