<?php

declare(strict_types=1);

namespace CamerPay\Exceptions;

/**
 * HTTP 401 : token Bearer manquant, invalide ou revoque.
 */
final class AuthenticationException extends CamerPayException {}
