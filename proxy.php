<?php

//namespacing  
namespace Expensify;

require '\vendor/autoload.php';

//initialize variables
$error_message = '';
$response = '';
$transaction_response = '';


//constants
define('API_URL', 'https://www.expensify.com/api');
define('PARTNER_NAME', 'applicant');
define('PARTNER_PASSWORD', 'd7c3119c6cdab02d68d9');

//input validation
$allowedCommands = ['Authenticate', 'GetTransactionList', 'CreateTransaction'];

if (!in_array($_GET['command'], $allowedCommands)) {
    http_response_code(400);
    exit();
}

//sanitization
$partnerName = filter_var($_GET['partnerName'], FILTER_SANITIZE_STRING);

//getResponseHeaders function  
function getResponseHeaders($response, $header_size)
{

    $header_str = substr($response, 0, $header_size);
    $headers = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header_str));
    return $headers;

}

//routing table
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

//handler functions
function authenticate()
{

    $query_string = $_SERVER['QUERY_STRING'];

    $query_string .= "&partnerName=" . PARTNER_NAME;
    $query_string .= "&partnerPassword=" . PARTNER_PASSWORD;

    $base_api_url = API_URL . $query_string;

    return proxyRequest($base_api_url);

    if (strpos($response, 'errorCode') !== false) {
        $error_data = json_decode($response, true);
        $error_message = $error_data['errorMessage']; // Set the error message
    }

    return $response;

}

function getTransactionList()
{

    //auth token handling
    if ($_COOKIE['authToken']) {
        $query_string .= "&authToken=" . $_COOKIE['authToken'];
    }

    $base_api_url = API_URL . $query_string;

    return proxyRequest($base_api_url);
}
//proxyRequest function
function proxyRequest($base_api_url)
{

    $curl = curl_init();
    if ($curl === false) {
        throw new \Exception('Error initializing API URL');
    }
    //sets up cURL to make proxy request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1); //set http headers
    curl_setopt($ch, CURLOPT_URL, $base_api_url);

    //sets request method - GET, POST, PUT etc 
    if (!empty($params)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }

    //make request
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

    //function that returns the error number associated with the last cURL transfer.
    if (curl_errno($ch)) {
        echo json_encode(['error' => 'Error accessing Expensify API: ' . curl_error($ch)]);
        exit;
    }

    //close curl handle
    curl_close($ch);

    //output the response
    $headers = getResponseHeaders($response, $header_size);
    $body = substr($response, $header_size);
    foreach ($headers as $hdr) {
        if (strpos($hdr, "Set-Cookie") === 0) {
            continue;
        } else {
            header($hdr);
        }
    }
    return $body;

}

//script body
if ($_GET['command']) {

    $query_string = $_SERVER['QUERY_STRING'];
    $base_api_url = "https://www.expensify.com/api";

    if ($_GET['command'] == "Authenticate") {
        $partnerName = "applicant";
        $partnerPassword = "d7c3119c6cdab02d68d9";
        $useExpensifyLogin = "false";
        $query_string .= "&partnerName=" . $partnerName . "&partnerPassword=" . $partnerPassword;
        $query_string .= "&useExpensifyLogin=" . $useExpensifyLogin;
        $base_api_url .= '?' . $query_string; //append query string correctly
        $response = proxyRequest($base_api_url); //assign response from proxyRequest
        $auth_data = json_decode($response, true);

        if (isset($auth_data['errorCode']) && $auth_data['errorCode'] === 'AUTH_FAILED') {
            echo "Authentication Error: " . $auth_data['errorMessage'];
            exit;
        }

    } else if ($_COOKIE['authToken']) {
        $authToken = $_COOKIE['authToken'];
        $query_string .= "&authToken=" . $authToken;
        $base_api_url .= '?' . $query_string; //append query string correctly

        $transaction_response = proxyRequest($base_api_url);
        //output the transaction response
        echo $transaction_response;
    }


}
?>