<?php

declare(strict_types=1);

namespace CamerPay\Exceptions;

/**
 * HTTP 5xx : erreur cote CamerPay. Generalement transitoire.
 * Implementez un retry avec backoff exponentiel.
 */
final class ServerException extends CamerPayException {}
