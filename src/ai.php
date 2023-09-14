<?php
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;


function checkPinCode($inputPin) {
  if (password_verify($inputPin, $_ENV['PIN_CODE'])) {
    return true;
  }
  return false;
}

$pin_code = $_POST['pincode'];

if (checkPinCode($pin_code)) {
  //echo "PIN kode sesuai.";
} else {
  echo "salah";
  exit;
}

$client = new Client();

$filePath = 'result.json';

if (file_exists($filePath)) {
    $all = file_get_contents($filePath);
    if ($all === false) {
        die("Gagal membaca dari file.");
    }
    $all = json_encode($all);
} else {
    die("File tidak ditemukan.");
}

$api_key = $_ENV['OPENAI_API_KEY'];
$prompt = $_ENV['INIT_PROMPT'] . ". Berikut adalah datanya: {$all}";

$response = $client->post('https://api.openai.com/v1/chat/completions', [
    'headers' => [
        'Authorization' => "Bearer {$api_key}",
        'Content-Type' => 'application/json',
    ],
    'json' => [
        'model' => 'gpt-4',
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => 0.7
    ]
]);

$result = json_decode($response->getBody(), true);
$analysis_output = "
        <div class='hn-wrap'>
            <div class='hn-title'>SUMMARY</div>
            <div class='hn-content'>".$result['choices'][0]['message']['content']."</div>
        </div>";
        

$response = [
  'analysis_output' => $analysis_output
];

$result = json_encode($response);

header('Content-Type: application/json; charset=utf-8');
echo $result;