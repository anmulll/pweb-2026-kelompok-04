<?php

namespace Tests\Unit\Services;

use App\Services\Doku\DokuCheckoutGateway;
use App\Services\Doku\DokuSignatureService;
use Tests\TestCase;

class DokuCheckoutTimestampTest extends TestCase
{
    protected DokuSignatureService $signatureService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->signatureService = new DokuSignatureService();
    }

    public function test_signature_service_generates_valid_hmac_sha256_format(): void
    {
        $clientId = 'CLIENT-TEST-123';
        $requestId = 'REQ-TEST-456';
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $targetPath = '/checkout/v1/payment';
        $jsonBody = json_encode(['amount' => 100000]);
        $secretKey = 'secret-key-test';

        $signature = $this->signatureService->generateSignature(
            $clientId,
            $requestId,
            $timestamp,
            $targetPath,
            $jsonBody,
            $secretKey
        );

        $this->assertStringStartsWith('HMACSHA256=', $signature);
    }

    public function test_timestamp_matches_iso8601_utc_format(): void
    {
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $timestamp);
    }

    public function test_verify_webhook_signature_succeeds_for_valid_signature(): void
    {
        $clientId = 'CLIENT-TEST-123';
        $requestId = 'REQ-TEST-456';
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $targetPath = '/api/payments/doku/notification';
        $rawBody = '{"order":{"invoice_number":"ORD-123","amount":50000}}';
        $secretKey = 'test-secret';

        $generatedSignature = $this->signatureService->generateSignature(
            $clientId,
            $requestId,
            $timestamp,
            $targetPath,
            $rawBody,
            $secretKey
        );

        $isValid = $this->signatureService->verifyWebhookSignature(
            $clientId,
            $requestId,
            $timestamp,
            $targetPath,
            $rawBody,
            $secretKey,
            $generatedSignature
        );

        $this->assertTrue($isValid);
    }

    public function test_verify_webhook_signature_fails_for_tampered_payload(): void
    {
        $clientId = 'CLIENT-TEST-123';
        $requestId = 'REQ-TEST-456';
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $targetPath = '/api/payments/doku/notification';
        $rawBody = '{"order":{"invoice_number":"ORD-123","amount":50000}}';
        $tamperedBody = '{"order":{"invoice_number":"ORD-123","amount":99999}}';
        $secretKey = 'test-secret';

        $generatedSignature = $this->signatureService->generateSignature(
            $clientId,
            $requestId,
            $timestamp,
            $targetPath,
            $rawBody,
            $secretKey
        );

        $isValid = $this->signatureService->verifyWebhookSignature(
            $clientId,
            $requestId,
            $timestamp,
            $targetPath,
            $tamperedBody,
            $secretKey,
            $generatedSignature
        );

        $this->assertFalse($isValid);
    }

    public function test_doku_gateway_rejects_production_mode(): void
    {
        config(['doku.is_production' => true]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DOKU Production mode is strictly disabled');

        new DokuCheckoutGateway($this->signatureService);
    }
}
