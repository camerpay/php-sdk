<?php

declare(strict_types=1);

namespace CamerPay\Exceptions;

/**
 * HTTP 404 : ressource inexistante (UUID inconnu, endpoint invalide, etc.)
 */
final class NotFoundException extends CamerPayException {}
