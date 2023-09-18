<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
require '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;

$client = new Client();
$cookieJar = new FileCookieJar($_ENV['COOKIE_FILE'], true);

if ($_ENV['MODE'] == 'dev'){
    echo "Success";
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['user_email'];
    $password = $_POST['password'];

    $payload = [
        'user_email' => $username,
        'password' => $password
    ];

    $headers = [
        'User-Agent' => 'Mozilla/5.0',
        'Content-Type' => 'application/x-www-form-urlencoded'
    ];

    try {
        $response = $client->post('https://portal-honeynet.bssn.go.id/honeynetadmin/doLogin', [
            'form_params' => $payload,
            'headers' => $headers,
            'cookies' => $cookieJar
        ]);
        
        $body = (string) $response->getBody();
        $logged_in = (strpos($body, 'Logout') !== false);
        
        if ($logged_in) {
            //echo "Login success";
            $cookieJar->save('/var/www/html/honeynet/src/cookie.txt');
            echo "Success";
            //header("location: dashboard.php");
            exit();
        } else {
            echo "Login failed";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
