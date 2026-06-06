# Changelog

All notable changes to `camerpay/php-sdk` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-06-06

### Added

- Initial release of the official CamerPay PHP SDK.
- `CamerPay\CamerPay` main client class with readonly resources.
- `CamerPay\Resources\Payments` — initiate, status, refund.
- `CamerPay\Resources\Payouts` — createBatch, getBatch (Mass Payout).
- `CamerPay\Webhooks\Verifier` — HMAC-SHA256 webhook signature verification with `hash_equals()` (timing-attack safe).
- Typed exception hierarchy mapped to HTTP status codes:
  - `CamerPayException` (base)
  - `AuthenticationException` (401)
  - `QuotaExceededException` (402) with helpers `getRemainingAmount()`, `getNextAction()`, `getUpgradeUrl()`
  - `ValidationException` (422) with `getFieldErrors()`
  - `NotFoundException` (404)
  - `ServerException` (5xx)
  - `WebhookException` for invalid webhook payloads
- Native cURL HTTP client (zero runtime dependencies — no Guzzle).
- TLS 1.2+ enforced (`CURLOPT_SSL_VERIFYPEER`).
- PSR-4 autoload under `CamerPay\` namespace.
- PHPUnit 10 tests for webhook verification.
- Full README with usage examples and security best practices.

### Requirements

- PHP `^8.1`
- `ext-curl`
- `ext-json`
