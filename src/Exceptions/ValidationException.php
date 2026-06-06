<?php

declare(strict_types=1);

namespace CamerPay\Exceptions;

/**
 * HTTP 422 : validation des donnees echouee.
 * Le champ `errors` du response detaille chaque champ en erreur.
 */
final class ValidationException extends CamerPayException
{
    /** @return array<string, array<int, string>> */
    public function getFieldErrors(): array
    {
        return $this->response['errors'] ?? [];
    }
}
