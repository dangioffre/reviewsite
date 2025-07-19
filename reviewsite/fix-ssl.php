<?php
// Download SSL certificate for Windows PHP
$certUrl = 'https://curl.se/ca/cacert.pem';
$certPath = __DIR__ . '/cacert.pem';

echo "Downloading SSL certificate...\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 30,
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$certData = file_get_contents($certUrl, false, $context);

if ($certData === false) {
    echo "Failed to download certificate\n";
    exit(1);
}

file_put_contents($certPath, $certData);
echo "Certificate saved to: $certPath\n";
echo "Add this to your .env file:\n";
echo "CURL_CA_BUNDLE=" . $certPath . "\n";
?>