<?php

namespace App\Services\Doku;

class DokuSignatureService
{
    /**
     * Generate HMAC-SHA256 signature for DOKU API calls.
     * Component string:
     * "Client-Id:" + clientId + "\n" +
     * "Request-Id:" + requestId + "\n" +
     * "Request-Timestamp:" + timestamp + "\n" +
     * "Request-Target:" + targetPath + "\n" +
     * "Digest:" + base64(sha256(body)) (if body not empty)
     */
    public function generateSignature(
        string $clientId,
        string $requestId,
        string $timestamp,
        string $targetPath,
        string $jsonBody,
        string $secretKey
    ): string {
        $components = "Client-Id:" . $clientId . "\n"
            . "Request-Id:" . $requestId . "\n"
            . "Request-Timestamp:" . $timestamp . "\n"
            . "Request-Target:" . $targetPath;

        if ($jsonBody !== '') {
            $digest = base64_encode(hash('sha256', $jsonBody, true));
            $components .= "\nDigest:" . $digest;
        }

        $rawSignature = hash_hmac('sha256', $components, $secretKey, true);

        return 'HMACSHA256=' . base64_encode($rawSignature);
    }

    /**
     * Verify incoming webhook notification signature.
     */
    public function verifyWebhookSignature(
        string $clientId,
        string $requestId,
        string $timestamp,
        string $targetPath,
        string $rawBody,
        string $secretKey,
        string $receivedSignature
    ): bool {
        $expected = $this->generateSignature(
            $clientId,
            $requestId,
            $timestamp,
            $targetPath,
            $rawBody,
            $secretKey
        );

        return hash_equals($expected, $receivedSignature);
    }
}
