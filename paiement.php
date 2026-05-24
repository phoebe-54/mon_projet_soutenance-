<?php

require_once "mtn_config.php";
require_once "mtn_token.php";

$token = getAccessToken();

if (empty($token) || !is_string($token)) {
    echo 'MTN TOKEN ERROR: token vide';
    exit;
}

echo '<pre>MTN token (apiKey) obtenu: '.htmlspecialchars(substr($token,0,10))."...\n</pre>";

// Dans la sandbox provisioning, l’API retourne un "apiKey".
// Pour la requête requesttopay, MTN attend un Bearer token.
// Si ton flow attend le Bearer, il faut utiliser l’apiKey retournée comme token.


$referenceId = bin2hex(random_bytes(16));

$phone = "229XXXXXXXX";

$data = [
    "amount" => "100",
    "currency" => "XOF",
    "externalId" => $referenceId,

    "payer" => [
        "partyIdType" => "MSISDN",
        "partyId" => $phone
    ],

    "payerMessage" => "Paiement NOCIBE",
    "payeeNote" => "Commande ciment"
];

$url = $baseUrl . "/collection/v1_0/requesttopay";

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),

    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token",
        "X-Reference-Id: $referenceId",
        "X-Target-Environment: sandbox",
        "Ocp-Apim-Subscription-Key: $subscriptionKey",
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "<pre>";
echo "HTTP: $http\n";
echo $response;
echo "</pre>";

if ($error) {
    echo "CURL ERROR ❌";
    exit;
}

// SUCCESS MTN = 202 Accepted
if ($http == 202) {
    echo "Paiement envoyé ✅";
} else {
    echo "Erreur paiement ❌";
}