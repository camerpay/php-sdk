<?php

declare(strict_types=1);

namespace CamerPay\Exceptions;

/**
 * Exception de base CamerPay. Toutes les autres exceptions du SDK
 * en heritent. Permet un catch general :
 *
 *   try { ... } catch (\CamerPay\Exceptions\CamerPayException $e) { ... }
 */
class CamerPayException extends \RuntimeException
{
    /** @var array<string, mixed> */
    protected array $response;

    /**
     * @param array<string, mixed> $response
     */
    public function __construct(
        string $message = '',
        int    $code = 0,
        array  $response = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /** @return array<string, mixed> */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * Le code d'erreur metier renvoye par CamerPay (ex: kyc_tier_volume_exceeded).
     */
    public function getErrorCode(): ?string
    {
        return isset($this->response['error']) ? (string) $this->response['error'] : null;
    }
}
