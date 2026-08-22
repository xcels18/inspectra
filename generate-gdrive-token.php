#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$oauthFile = __DIR__ . '/oauth-credentials.json';
$creds     = json_decode(file_get_contents($oauthFile), true);
$creds     = $creds['installed'] ?? $creds['web'] ?? $creds;

$client = new Google\Client();
$client->setClientId($creds['client_id']);
$client->setClientSecret($creds['client_secret']);
$client->setRedirectUri('http://localhost');
$client->addScope(Google\Service\Drive::DRIVE);
$client->setAccessType('offline');
$client->setPrompt('consent');

$authUrl = $client->createAuthUrl();

echo "Buka URL ini di browser:\n";
echo $authUrl . "\n\n";
echo "Setelah authorize, copy code dari URL browser dan paste di sini: ";

$code = trim(fgets(STDIN));
$code = preg_replace('/.*[?&]code=([^&]+).*/', '$1', $code);

$token = $client->fetchAccessTokenWithAuthCode($code);

if (isset($token['refresh_token'])) {
    echo "\nRefresh token berhasil!\n\n";
    echo "Tambahkan baris ini ke .env:\n";
    echo "GOOGLE_DRIVE_CLIENT_ID=" . $creds['client_id'] . "\n";
    echo "GOOGLE_DRIVE_CLIENT_SECRET=" . $creds['client_secret'] . "\n";
    echo "GOOGLE_DRIVE_REFRESH_TOKEN=" . $token['refresh_token'] . "\n";
} elseif (isset($token['error'])) {
    echo "\nGagal: " . $token['error'] . " - " . ($token['error_description'] ?? '') . "\n";
} else {
    echo "\nGagal mendapatkan token:\n";
    print_r($token);
}
