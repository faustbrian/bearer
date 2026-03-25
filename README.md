[![GitHub Workflow Status][ico-tests]][link-tests]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

------

Stripe-style typed API tokens with groups, environments, and audit logging for Laravel. Features conductor-based API, token rotation, revocation strategies, seamless Sanctum integration, and optional recoverable legacy tokens for systems that must display keys after creation.

## Requirements

> **Requires [PHP 8.4+](https://php.net/releases/)** and Laravel 11+

## Installation

```bash
composer require cline/bearer
```

If you want Bearer to delegate runtime ability checks to Warden, install
Warden in your application and switch the authorization driver:

```bash
composer require cline/warden
```

```php
// config/bearer.php
'authorization' => [
    'default' => env('BEARER_AUTHORIZATION_DRIVER', 'array'),
],
```

`array` remains the default provider. `warden` requires both the token's
stored ability scope and the token owner's Warden permission to pass.

## Documentation

- **[Getting Started](DOCS.md#doc-docs-readme)** - Installation, configuration, and first steps
- **[Basic Usage](DOCS.md#doc-docs-basic-usage)** - Creating, validating, and managing tokens
- **[Authentication](DOCS.md#doc-docs-authentication)** - Integrating with Laravel authentication
- **[Custom Token Types](DOCS.md#doc-docs-custom-token-types)** - Defining typed tokens with abilities
- **[Token Metadata](DOCS.md#doc-docs-token-metadata)** - Attaching and querying token metadata
- **[Derived Keys](DOCS.md#doc-docs-derived-keys)** - Hierarchical token derivation for resellers
- **[Revocation & Rotation](DOCS.md#doc-docs-revocation-rotation)** - Token lifecycle management
- **[IP & Domain Restrictions](DOCS.md#doc-docs-ip-domain-restrictions)** - Network-based access control
- **[Rate Limiting](DOCS.md#doc-docs-rate-limiting)** - Throttling token usage
- **[Usage Tracking](DOCS.md#doc-docs-usage-tracking)** - Monitoring token activity
- **[Audit Logging](DOCS.md#doc-docs-audit-logging)** - Recording token events
- **[Token Generators](DOCS.md#doc-docs-token-generators)** - Custom token generation strategies

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please use the [GitHub security reporting form][link-security] rather than the issue queue.

## Credits

- [Brian Faust][link-maintainer]
- [All Contributors][link-contributors]

## License

The MIT License. Please see [License File](LICENSE.md) for more information.

[ico-tests]: https://github.com/faustbrian/bearer/actions/workflows/quality-assurance.yaml/badge.svg
[ico-version]: https://img.shields.io/packagist/v/cline/bearer.svg
[ico-license]: https://img.shields.io/badge/License-MIT-green.svg
[ico-downloads]: https://img.shields.io/packagist/dt/cline/bearer.svg

[link-tests]: https://github.com/faustbrian/bearer/actions
[link-packagist]: https://packagist.org/packages/cline/bearer
[link-downloads]: https://packagist.org/packages/cline/bearer
[link-security]: https://github.com/faustbrian/bearer/security
[link-maintainer]: https://github.com/faustbrian
[link-contributors]: ../../contributors
