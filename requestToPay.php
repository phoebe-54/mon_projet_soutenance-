

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "X-Reference-Id: $referenceId",
    "X-Target-Environment: sandbox",
    "Ocp-Apim-Subscription-Key: $subscriptionKey",
    "Content-Type: application/json"
]);
$url = "https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay/$referenceId";