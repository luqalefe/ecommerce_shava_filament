<?php
// Script para debugar query parameters

$url = "http://nginx/api/mercadopago/webhook?data.id=12345";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"test": true}');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-signature: ts=9999999999,v1=fake',
    'x-request-id: debug-test',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
echo "Response: {$response}\n";
