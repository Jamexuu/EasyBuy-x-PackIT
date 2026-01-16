<?php
require_once '../config.php';

header('Content-Type: text/plain');

// Load product data
$productData = trim(file_get_contents("data.txt"));

// Get user input
$input = json_decode(file_get_contents('php://input'), true);
$userPrompt = trim($input['prompt'] ?? '');

if ($userPrompt === '') {
    echo "Hi there! 👋 I'm your EasyBuy assistant. What groceries are you looking for today?";
    exit;
}

/*
 HARD RULE:
 Qwen must be forced to output ONE exact refusal string
*/
$refusalText = "Sorry, I can only help with EasyBuy products and information!";

$prompt = <<<PROMPT
You are Ebby, the EasyBuy Store Assistant.

MANDATORY RULES (NO EXCEPTIONS):
- You MUST ONLY answer questions related to EasyBuy products, store details, pricing, categories, payment, shipping, or availability
- You MUST NOT answer questions about weather, jokes, history, politics, recipes, movies, countries, capitals, or general knowledge
- If the question is NOT related to EasyBuy, respond EXACTLY with:
"{$refusalText}"
- Be friendly and concise
- Do NOT explain reasoning
- Maximum 30 words

EASYBUY STORE INFORMATION:
- Location: Brgy. Altura Bata, Tanauan City, Batangas, Philippines
- Currency: Philippine Peso (₱)
- Payment Methods: Cash on Delivery (COD), PayPal
- Shipping: Weight-based, ₱100 base fee
- Categories: Produce, Meat and Seafood, Dairy, Frozen Goods, Condiments and Sauces, Snacks, Beverages, Personal Care

PRODUCT CATALOG:
{$productData}

Customer: {$userPrompt}
Assistant:
PROMPT;


// Ollama API
$apiUrl = rtrim($_ENV['OLLAMA_BASE_URL'], '/') . '/api/generate';

$requestData = [
    'model' => 'qwen2.5:1.5b',
    'prompt' => $prompt,
    'stream' => false,
    'options' => [
        'temperature' => 1,
        'num_predict' => 60,
        'num_ctx' => 728,
        'top_k' => 20,
        'top_p' => 0.85,
        'repeat_penalty' => 1.1,
        'stop' => ['PRODUCTS', 'Customer:', 'Assistant:']
    ]
];

// Send request
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($requestData),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false || $curlError || $httpCode !== 200) {
    echo "Sorry, I'm having trouble right now. Please try again.";
    exit;
}

$data = json_decode($response, true);
$aiResponse = trim($data['response'] ?? '');


if ($aiResponse === $refusalText) {
    echo $aiResponse;
    exit;
}

// Block ANY response that looks off-topic
$blockedWords = [
    'weather','temperature','joke','story','math','calculate',
    'history','politics','president','recipe','cook','song','movie', 'sky'
];

$lower = strtolower($aiResponse);
foreach ($blockedWords as $word) {
    if (strpos($lower, $word) !== false) {
        echo $refusalText;
        exit;
    }
}

// Otherwise allow response
echo $aiResponse;
