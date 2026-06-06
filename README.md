# CamerPay PHP SDK

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.1-blue.svg)](https://php.net)

SDK PHP officiel pour [CamerPay](https://camerpay.biz) — passerelle de paiement Mobile Money, cartes et PayPal au Cameroun.

## Installation

```bash
composer require camerpay/php-sdk
```

Requis : **PHP 8.1+** avec extensions `curl` et `json`.

## Démarrage rapide

### Initier un paiement

```php
use CamerPay\CamerPay;

$camerpay = new CamerPay($_ENV['CAMERPAY_TOKEN']);

$tx = $camerpay->payments->initiate([
    'amount'                => 5000,
    'currency'              => 'XAF',
    'payment_method'        => 'orange_money',
    'customer_phone'        => '+237690000000',
    'customer_email'        => 'client@exemple.com',
    'merchant_invoice_id'   => 'FACT-001',
    'merchant_callback_url' => 'https://votre-site.com/webhooks/camerpay',
    'merchant_return_url'   => 'https://votre-site.com/merci',
]);

// Rediriger le client vers la page de paiement
header('Location: ' . $tx['pay_url']);
```

### Vérifier le statut

```php
$tx = $camerpay->payments->status('5add2319-f71b-4f2d-a4f4-97fe0d11c1d4');

if ($tx['transaction']['status'] === 'completed') {
    // Marquer la commande comme payée
}
```

### Rembourser

```php
// Remboursement total
$camerpay->payments->refund($uuid);

// Remboursement partiel
$camerpay->payments->refund($uuid, amount: 2500, reason: 'Geste commercial');
```

### Versements groupés (Mass Payout)

```php
$batch = $camerpay->payouts->createBatch([
    'reference'    => 'SALAIRES-2026-06',
    'callback_url' => 'https://votre-site.com/webhooks/payouts',
    'beneficiaries' => [
        ['phone' => '+237690000001', 'amount' => 75000, 'operator' => 'orange_money', 'name' => 'Jean'],
        ['phone' => '+237670000002', 'amount' => 50000, 'operator' => 'mtn_momo',     'name' => 'Aisha'],
    ],
]);

echo "Batch UUID : " . $batch['batch_uuid'];
```

## Vérifier un webhook

```php
use CamerPay\CamerPay;
use CamerPay\Exceptions\WebhookException;

$verifier = CamerPay::webhooks($_ENV['CAMERPAY_WEBHOOK_SECRET']);

try {
    $event = $verifier->verifyFromRequest();

    if ($event['status'] === 'completed') {
        // Marquer la commande comme payée
    }

    http_response_code(200);
    echo 'OK';
} catch (WebhookException $e) {
    http_response_code(401);
    echo 'Invalid signature';
}
```

## Gestion d'erreurs

Toutes les exceptions héritent de `CamerPay\Exceptions\CamerPayException` :

```php
use CamerPay\Exceptions\AuthenticationException;
use CamerPay\Exceptions\QuotaExceededException;
use CamerPay\Exceptions\ValidationException;
use CamerPay\Exceptions\NotFoundException;
use CamerPay\Exceptions\ServerException;
use CamerPay\Exceptions\CamerPayException;

try {
    $tx = $camerpay->payments->initiate([...]);
} catch (AuthenticationException $e) {
    // 401 — token invalide ou révoqué
} catch (QuotaExceededException $e) {
    // 402 — plafond KYC atteint OU quota plan dépassé
    echo $e->getNextAction();   // "Fournissez votre attestation NIU..."
    echo $e->getUpgradeUrl();   // https://camerpay.biz/client/kyc
    echo $e->getRemainingAmount(); // 5000 XAF restant ce mois
} catch (ValidationException $e) {
    // 422 — données invalides
    print_r($e->getFieldErrors());
} catch (NotFoundException $e) {
    // 404 — UUID inconnu
} catch (ServerException $e) {
    // 5xx — erreur côté CamerPay, retry recommandé
} catch (CamerPayException $e) {
    // Tout le reste
}
```

## Configuration avancée

```php
$camerpay = new CamerPay(
    apiKey:  $_ENV['CAMERPAY_TOKEN'],
    baseUrl: 'https://camerpay.biz/api',  // Override si proxy/staging
    timeout: 30,                          // Secondes
);
```

## Sécurité

- ✅ Vérification HMAC-SHA256 avec `hash_equals()` (timing-attack safe)
- ✅ TLS 1.2+ enforced via `CURLOPT_SSL_VERIFYPEER`
- ✅ Pas de dépendances externes (cURL natif, zéro Guzzle)
- ✅ Tous les secrets via `$_ENV` recommandé

**Ne JAMAIS** :
- ❌ Committer votre token API dans Git
- ❌ Logger le payload brut (peut contenir des données sensibles)
- ❌ Exposer votre `CAMERPAY_WEBHOOK_SECRET` côté frontend

## Documentation API

- Documentation complète : [camerpay.biz/docs](https://camerpay.biz/docs)
- Démarrage rapide : [camerpay.biz/docs/getting-started](https://camerpay.biz/docs/getting-started)
- Référence webhooks : [camerpay.biz/docs/webhooks](https://camerpay.biz/docs/webhooks)
- Codes d'erreur : [camerpay.biz/docs/errors](https://camerpay.biz/docs/errors)

## Support

- Email : contact@camerpay.biz
- Issues GitHub : https://github.com/madengue/camerpay/issues
- Dashboard marchand : https://camerpay.biz/client

## Licence

[MIT](LICENSE)
