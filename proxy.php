<?php

// namespacing  
namespace Expensify;

require '\vendor/autoload.php';

// constants
define('API_URL', 'https://www.expensify.com/api');
define('PARTNER_NAME', 'applicant');
define('PARTNER_PASSWORD', 'd7c3119c6cdab02d68d9');

// input validation
$allowedCommands = ['Authenticate', 'GetTransactionList', 'CreateTransaction'];

if (!in_array($_GET['command'], $allowedCommands)) {
    http_response_code(400);
    exit();
}

// sanitization
$partnerName = filter_var($_GET['partnerName'], FILTER_SANITIZE_STRING);

// getResponseHeaders function  
function getResponseHeaders($response, $header_size)
{

    $header_str = substr($response, 0, $header_size);
    $headers = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header_str));
    return $headers;

}

// routing table
$command = $_GET['command'];

switch ($command) {

    case 'Authenticate':
        $handler = 'authenticate';
        break;

    case 'GetTransaction':
        $handler = 'getTransaction';
        break;

    case 'CreateTransaction':
        $handler = 'createTransaction';
        break;

}

// handler functions
function authenticate()
{

    $qs = $_SERVER['QUERY_STRING'];

    $qs .= "&partnerName=" . PARTNER_NAME;
    $qs .= "&partnerPassword=" . PARTNER_PASSWORD;

    $api_url = API_URL . $qs;

    return proxyRequest($api_url);

}

function getTransactionList()
{

    // Auth token handling
    if ($_COOKIE['authToken']) {
        $qs .= "&authToken=" . $_COOKIE['authToken'];
    }

    $api_url = API_URL . $qs;

    return proxyRequest($api_url);
}
// proxyRequest function
function proxyRequest($url)
{

    $curl = curl_init();
    if ($curl === false) {
        throw new \Exception('Error initializing curl');
    }
    //sets default options
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1); //set http headers
    curl_setopt($ch, CURLOPT_URL, $url);

    //sets request method - GET, POST, PUT etc 
    if (!empty($params)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }

    //make request
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

    //close curl handle
    curl_close($ch);
    $headers = getResponseHeaders($response, $header_size);
    $body = substr($response, $header_size);
    foreach ($headers as $hdr) {
        if (strpos($hdr, "Set-Cookie") === 0) {
            continue;
        } else {
            header($hdr);
        }
    }
    echo $body;

}
/*
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
class ExpensifyLogger {
private $log;
private $cache;
public function __construct() {
$this->log = new Logger('expensify');
$this->log->pushHandler(new StreamHandler('path/to/log.log', Logger::INFO));
$this->cache = new FilesystemAdapter();
}
// Add methods here to interact with the log and cache, if needed
}
// Usage
$expensifyLogger = new ExpensifyLogger();
// Now you can access $expensifyLogger->log and $expensifyLogger->cache
*/

// script body
if ($_GET['command']) {

    $qs = $_SERVER['QUERY_STRING'];
    $api_url = "https://www.expensify.com/api";
    if ($_GET['command'] == "Authenticate") {
        $partnerName = "applicant";
        $partnerPassword = "d7c3119c6cdab02d68d9";
        $useExpensifyLogin = "false";
        $qs = $qs . "&partnerName=" . $partnerName . "&partnerPassword=" . $partnerPassword;
        $qs = $qs . "&useExpensifyLogin=" . $useExpensifyLogin;
        $api_url = $api_url . $qs;
        proxyRequest($api_url);
    } else if ($_COOKIE['authToken']) {
        $authToken = $_COOKIE['authToken'];
        $qs = $qs . "&authToken=" . $authToken;
        $api_url = $api_url . $qs;
        proxyRequest($api_url);
    }

}
?>