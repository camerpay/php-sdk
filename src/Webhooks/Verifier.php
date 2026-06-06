<?php

declare(strict_types=1);

namespace CamerPay\Webhooks;

use CamerPay\Exceptions\WebhookException;

/**
 * Verification HMAC-SHA256 des webhooks CamerPay.
 *
 * Algorithme :
 *   data      = uuid + "|" + invoice_id + "|" + status + "|" + amount
 *   signature = hash_hmac('sha256', data, callback_secret)
 *
 * Usage minimal :
 *
 *   $verifier = new \CamerPay\Webhooks\Verifier($_ENV['CAMERPAY_WEBHOOK_SECRET']);
 *
 *   try {
 *       $event = $verifier->verifyFromRequest();
 *       if ($event['status'] === 'completed') {
 *           // Marquer la commande comme payee
 *       }
 *       http_response_code(200);
 *       echo 'OK';
 *   } catch (\CamerPay\Exceptions\WebhookException $e) {
 *       http_response_code(401);
 *       echo $e->getMessage();
 *   }
 *
 * Usage manuel (si vous avez deja le payload) :
 *
 *   $verifier->verify($payload);  // throw si invalide
 *   // OU
 *   if ($verifier->isValid($payload)) { ... }
 */
final class Verifier
{
    public function __construct(private readonly string $secret)
    {
        if ($secret === '') {
            throw new \InvalidArgumentException('Webhook secret cannot be empty.');
        }
    }

    /**
     * Lit la requete POST courante ($_POST + header signature),
     * verifie la signature, retourne le payload comme array.
     *
     * @return array{uuid: string, invoice_id: string, status: string, amount: string, signature: string}
     * @throws WebhookException
     */
    public function verifyFromRequest(): array
    {
        $payload = [
            'uuid'       => (string) ($_POST['uuid'] ?? ''),
            'invoice_id' => (string) ($_POST['invoice_id'] ?? ''),
            'status'     => (string) ($_POST['status'] ?? ''),
            'amount'     => (string) ($_POST['amount'] ?? ''),
            'signature'  => (string) ($_POST['signature']
                          ?? ($_SERVER['HTTP_X_CAMERPAY_SIGNATURE'] ?? '')),
        ];

        $this->verify($payload);
        return $payload;
    }

    /**
     * Verifie un payload deja parse.
     *
     * @param array<string, string|int|float|null> $payload
     * @throws WebhookException
     */
    public function verify(array $payload): void
    {
        foreach (['uuid', 'invoice_id', 'status', 'amount', 'signature'] as $f) {
            if (!isset($payload[$f]) || $payload[$f] === '') {
                throw new WebhookException("Missing field in payload: {$f}");
            }
        }

        $expected = $this->computeSignature(
            (string) $payload['uuid'],
            (string) $payload['invoice_id'],
            (string) $payload['status'],
            (string) $payload['amount'],
        );

        if (!hash_equals($expected, (string) $payload['signature'])) {
            throw new WebhookException('Invalid HMAC signature.');
        }
    }

    /**
     * Version qui retourne bool sans lever d'exception.
     *
     * @param array<string, string|int|float|null> $payload
     */
    public function isValid(array $payload): bool
    {
        try {
            $this->verify($payload);
            return true;
        } catch (WebhookException) {
            return false;
        }
    }

    /**
     * Calcule la signature HMAC-SHA256 attendue.
     * Utilitaire public pour debugger/tester.
     */
    public function computeSignature(
        string $uuid,
        string $invoiceId,
        string $status,
        string $amount,
    ): string {
        $data = $uuid . '|' . $invoiceId . '|' . $status . '|' . $amount;
        return hash_hmac('sha256', $data, $this->secret);
    }
}
