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

// Pre-filter: Check for off-topic keywords
$lowerPrompt = strtolower($userPrompt);

$productKeywords = ['product', 'price', 'cost', 'stock', 'buy', 'purchase', 'available', 'sale', 'discount', 'cheap', 'expensive', 'food', 'vegetable', 'fruit', 'meat', 'dairy', 'snack', 'drink', 'beverage', 'milk', 'egg', 'chicken', 'beef', 'fish', 'bread', 'rice', 'pasta', 'shampoo', 'soap', 'toothpaste', 'detergent', 'grocery', 'groceries', 'item', 'catalog'];
$offTopicKeywords = ['weather', 'temperature', 'rain', 'president', 'government', 'politics', 'election', 'calculate', 'math', 'solve', 'equation', 'recipe', 'how to cook', 'who is', 'who was', 'what is a', 'what is the', 'define', 'meaning of', 'capital of', 'country', 'history', 'religion', 'bible', 'quran', 'tell me about', 'explain', 'describe', 'write'];

$hasProductKeyword = false;
foreach ($productKeywords as $keyword) {
    if (strpos($lowerPrompt, $keyword) !== false) {
        $hasProductKeyword = true;
        break;
    }
}

$hasOffTopicKeyword = false;
foreach ($offTopicKeywords as $keyword) {
    if (strpos($lowerPrompt, $keyword) !== false) {
        $hasOffTopicKeyword = true;
        break;
    }
}

// Block off-topic questions
if ($hasOffTopicKeyword || (!$hasProductKeyword && strlen($userPrompt) > 25)) {
    echo "I'd love to help, but I'm specialized in EasyBuy products! 😊 Ask me about our groceries, prices, or what's on sale today!";
    exit;
}

// Build AI prompt
$prompt = "You are a friendly EasyBuy store assistant. Answer warmly and conversationally. Use emojis occasionally. Only discuss products from the list below.\n\nProducts:\n" . $productData . "\n\nCustomer: " . $userPrompt . "\nYou:";

// Get AI response
try {
    $client = Ollama::client($_ENV['OLLAMA_BASE_URL']);
    $response = $client->completions()->create([
        'model' => 'gemma2:2b',
        'prompt' => $prompt,
        'stream' => false,
        'options' => [
            'num_predict' => 60,
            'temperature' => 0.3,
            'top_k' => 40,
            'repeat_penalty' => 1.1,
        ]
    ]);
    
    $aiResponse = $response->response ?? "I'm sorry, I couldn't generate a response. Please try again.";
    
    // Post-validate: Check if AI responded with off-topic content
    $responseLower = strtolower($aiResponse);
    $offTopicPhrases = ['weather', 'president', 'calculate', 'recipe', 'capital', 'country', 'history', 'as an ai', 'i can help you with', 'however', 'unfortunately'];
    
    foreach ($offTopicPhrases as $phrase) {
        if (strpos($responseLower, $phrase) !== false) {
            echo "I'd love to help, but I'm specialized in EasyBuy products! 😊 Ask me about our groceries, prices, or what's on sale today!";
            exit;
        }
    }
    
    echo $aiResponse;
    
} catch (Exception $e) {
    error_log("Ollama API Error: " . $e->getMessage());
    echo "Sorry, I'm having trouble connecting to the service. Please try again later.";
}