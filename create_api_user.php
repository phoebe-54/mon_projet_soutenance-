<?php

$subscriptionKey = "TON_PRIMARY_KEY";

$url = "https://sandbox.momodeveloper.mtn.com/v1_0/apiuser";

$referenceId = uniqid("nocibe_");

$data = [
    "providerCallbackHost" => "https://example.com"
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Reference-Id: $referenceId",
    "Ocp-Apim-Subscription-Key: $subscriptionKey",
    "Content-Type: application/json"
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "HTTP: $http\n";
echo $response;