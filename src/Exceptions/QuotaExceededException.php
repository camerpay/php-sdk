<?php

declare(strict_types=1);

namespace CamerPay\Exceptions;

/**
 * HTTP 402 : plafond KYC tier depasse OU quota plan atteint
 * OU abonnement expire.
 *
 * Consulter getErrorCode() pour distinguer le cas exact :
 *  - kyc_tier_volume_exceeded
 *  - transaction_limit_reached
 *  - subscription_expired
 */
final class QuotaExceededException extends CamerPayException
{
    public function getRemainingAmount(): ?int
    {
        return isset($this->response['remaining']) ? (int) $this->response['remaining'] : null;
    }

    public function getMonthlyLimit(): ?int
    {
        return isset($this->response['monthly_limit']) ? (int) $this->response['monthly_limit'] : null;
    }

    public function getNextAction(): ?string
    {
        return isset($this->response['next_action']) ? (string) $this->response['next_action'] : null;
    }

    public function getUpgradeUrl(): ?string
    {
        return $this->response['upgrade_url'] ?? $this->response['renew_url'] ?? null;
    }
}
