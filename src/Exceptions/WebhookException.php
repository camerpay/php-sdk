<?php

declare(strict_types=1);

namespace CamerPay\Exceptions;

/**
 * Webhook recu invalide : signature HMAC non verifiable,
 * champ manquant, etc.
 */
final class WebhookException extends CamerPayException {}
