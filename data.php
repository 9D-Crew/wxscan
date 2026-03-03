<?php
// Proxy script to forward requests to an API endpoint

// Target API base URL
$targetBaseUrl = 'https://wx.lewolfyt.cc/';

// Get the current request's query string
$queryString = $_SERVER['QUERY_STRING'] ?? '';

// Build the target URL
$targetUrl = $targetBaseUrl . ($queryString ? '?' . $queryString : '');

// Initialize cURL session
$ch = curl_init($targetUrl);

// Set cURL options
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,     // Return response as string
    CURLOPT_FOLLOWLOCATION => true,     // Follow redirects
    CURLOPT_MAXREDIRS      => 5,        // Maximum redirects to follow
    CURLOPT_TIMEOUT        => 30,       // Timeout in seconds
    CURLOPT_SSL_VERIFYPEER => false,    // Disable SSL verification (enable for production)
    CURLOPT_SSL_VERIFYHOST => false,    // Disable host verification (enable for production)
    CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'] ?? 'PHP-Proxy/1.0',
    
    // Forward HTTP headers (optional)
    CURLOPT_HTTPHEADER     => [
        'Accept: ' . ($_SERVER['HTTP_ACCEPT'] ?? '*/*'),
        'Accept-Language: ' . ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''),
        // Add other headers as needed
    ]
]);

// Forward the request method
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
} elseif ($method !== 'GET') {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
}

// Forward optional authentication (if provided in original request)
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
}

// Execute the request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Proxy request failed',
        'message' => curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

// Get the HTTP response code from the target
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
http_response_code($httpCode);

// Forward content type header
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
if ($contentType) {
    header('Content-Type: ' . $contentType);
}

// Close cURL session
curl_close($ch);

// Output the response
echo $response;
?>