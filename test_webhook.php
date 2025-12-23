<?php
// Script para testar webhook do Mercado Pago

$timestamp = time();
$dataId = '12345';
$requestId = 'test-' . uniqid();
$secret = 'test_webhook_secret_shava_2024';

// Construir manifest (EXATAMENTE como o controller faz)
$manifest = "id:{$dataId};request-id:{$requestId};ts:{$timestamp};";

// Calcular hash
$hash = hash_hmac('sha256', $manifest, $secret);
$signature = "ts={$timestamp},v1={$hash}";

echo "=== Teste de Webhook Mercado Pago ===\n\n";
echo "Timestamp: {$timestamp}\n";
echo "Data ID: {$dataId}\n";
echo "Request ID: {$requestId}\n";
echo "Secret: {$secret}\n";
echo "Manifest: {$manifest}\n";
echo "Hash calculado: {$hash}\n";
echo "Signature: {$signature}\n\n";

// URL tem que ter o data.id como query param
$url = "http://nginx/api/mercadopago/webhook?data.id={$dataId}";

echo "URL: {$url}\n\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['action' => 'payment.updated', 'data' => ['id' => $dataId]]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "x-signature: {$signature}",
    "x-request-id: {$requestId}",
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "=== Resposta ===\n";
echo "HTTP Code: {$httpCode}\n";
echo "Body: {$response}\n";
if ($error) echo "Curl Error: {$error}\n";
echo "\n";

if ($httpCode === 200) {
    echo "✅ WEBHOOK ACEITO - Assinatura válida!\n";
} elseif ($httpCode === 403) {
    echo "🚫 WEBHOOK REJEITADO - Assinatura inválida\n";
} else {
    echo "⚠️ Resposta inesperada\n";
}
