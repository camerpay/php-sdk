<?php

declare(strict_types=1);

namespace CamerPay\Tests\Webhooks;

use CamerPay\Exceptions\WebhookException;
use CamerPay\Webhooks\Verifier;
use PHPUnit\Framework\TestCase;

final class VerifierTest extends TestCase
{
    private const SECRET = 'test_secret_abc123';

    public function testComputeSignatureMatchesServerImplementation(): void
    {
        $verifier = new Verifier(self::SECRET);

        // Donnees identiques au format CamerPay :
        // data = uuid|invoice_id|status|amount
        $expected = hash_hmac(
            'sha256',
            '5add2319-...|FACT-001|completed|10000.00',
            self::SECRET
        );

        $signature = $verifier->computeSignature(
            '5add2319-...', 'FACT-001', 'completed', '10000.00'
        );

        $this->assertSame($expected, $signature);
    }

    public function testVerifyAcceptsValidPayload(): void
    {
        $verifier = new Verifier(self::SECRET);

        $payload = [
            'uuid'       => 'abc',
            'invoice_id' => 'INV-1',
            'status'     => 'completed',
            'amount'     => '1000.00',
        ];
        $payload['signature'] = $verifier->computeSignature(
            $payload['uuid'], $payload['invoice_id'], $payload['status'], $payload['amount']
        );

        // Pas d'exception attendue
        $verifier->verify($payload);
        $this->assertTrue($verifier->isValid($payload));
    }

    public function testVerifyRejectsInvalidSignature(): void
    {
        $verifier = new Verifier(self::SECRET);

        $payload = [
            'uuid'       => 'abc',
            'invoice_id' => 'INV-1',
            'status'     => 'completed',
            'amount'     => '1000.00',
            'signature'  => 'invalid_signature_xyz',
        ];

        $this->expectException(WebhookException::class);
        $verifier->verify($payload);
    }

    public function testVerifyRejectsMissingField(): void
    {
        $verifier = new Verifier(self::SECRET);

        $this->expectException(WebhookException::class);
        $verifier->verify([
            'uuid'       => 'abc',
            // invoice_id manquant
            'status'     => 'completed',
            'amount'     => '1000.00',
            'signature'  => 'whatever',
        ]);
    }

    public function testEmptySecretThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Verifier('');
    }
}
