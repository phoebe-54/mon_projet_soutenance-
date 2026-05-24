<?php

require_once "mtn_config.php";

function getAccessToken(): string
{
    global $baseUrl, $subscriptionKey, $apiUser, $apiKey;

    // Token MTN Collections (sandbox provisioning) — d’après la doc:
    // https://momodeveloper.mtn.com/API-collections#api=sandbox-provisioning-api&operation=post-v1_0-apiuser-apikey
    // On obtient un token via: /v1_0/apiuser/{apiUser}/apikey
    $endpoints = [
        '/v1_0/apiuser/' . $apiUser . '/apikey',
    ];

    $credentials = base64_encode($apiUser . ":" . $apiKey);

    foreach ($endpoints as $ep) {
        $url = rtrim($baseUrl, '/') . $ep;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic $credentials",
                "Ocp-Apim-Subscription-Key: $subscriptionKey",
                "Content-Type: application/json",
            ],
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 8,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            continue;
        }

        $json = json_decode($response, true);
        if (!is_array($json)) {
            continue;
        }

        // Selon la doc sandbox provisioning, response contient "apiKey" (pas access_token)
        if ($http === 201 && isset($json['apiKey']) && is_string($json['apiKey']) && $json['apiKey'] !== '') {
            return $json['apiKey'];
        }
    }

    die("<h3>TOKEN ERROR ❌</h3><p>Impossible d’obtenir access_token.</p>");
}
