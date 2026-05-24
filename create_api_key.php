<?php

$subscriptionKey = "TON_PRIMARY_KEY";
$apiUser = "TON_REFERENCE_ID_UTILISE_POUR_API_USER";

$url = "https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/$apiUser/apikey";

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Ocp-Apim-Subscription-Key: $subscriptionKey"
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;