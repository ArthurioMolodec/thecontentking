<?php

$apiUrl = "http://178.18.248.235:1325/api.php";

$params = $_GET;

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $apiUrl . '?' . http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 21000);

$response = curl_exec($ch);

if ($response === false) {
  echo "cURL error: " . curl_error($ch);
}

curl_close($ch);

echo $response;
?>