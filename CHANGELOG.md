# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Fixed
- Bearer guard registration now remains bound to the package service provider
  when Laravel 13 rebinds custom authentication driver callbacks.
- Token rotation now preserves the original finite expiration timestamp,
  preventing rotated credentials from silently becoming non-expiring or
  renewing expired access. Grace-period rotation also no longer extends an
  earlier expiration, postpones an earlier revocation, or reactivates an
  expired or revoked predecessor. Immediate rotation now preserves an earlier
  revocation timestamp instead of moving it forward.

### Changed
- Synchronized source formatting, Laravel 13 model and command declarations,
  and static-analysis types with the current ECS, Rector, and PHPStan rules.
- Updated package dependency constraints and refreshed docblocks to match
  the current codebase.
- Renamed package interfaces to `*Interface`, traits to `*Trait`, and
  abstract classes to `Abstract*` for consistency.
- Polymorphic `owner`, `context`, and `boundary` relations now resolve
  their lookup keys through the configured morph key registry, so ULID
  and UUID owners are hydrated correctly without subclassing the token
  models. `HasAccessTokensTrait` now uses the same registry path for
  `accessTokens`, `contextTokens`, `boundaryTokens`, and
  `accessTokenGroups`.
- Added a `Cline\Bearer\Database\Models` facade so registry access
  follows the same pattern used by the other Cline packages.
- Added facade-level and service-provider integration tests for morph
  key registry wiring.

### Breaking
- Laravel 13 is now the only supported framework version. Applications on
  Laravel 12 or earlier must upgrade before installing this release.
- Renamed public contracts and abstract exception base classes, including
  `TokenType` to `TokenTypeInterface`, `HasAccessTokens` to
  `HasAccessTokensInterface`, `HasAccessTokens` trait to
  `HasAccessTokensTrait`, and `BearerException` to
  `BearerExceptionInterface`. Update imports, implementations, and
  extends clauses accordingly.

### Added
- Added repository-level maintainer guidance in `AGENTS.md`.
- Added optional recoverable legacy token support via per-type `revealable`
  configuration, encrypted `plain_text_token` storage, explicit
  `revealPlainTextToken()` access, and `revealed` audit events.
- Added pluggable Bearer ability providers with built-in `array` and
  `warden` implementations, including owner-aware Warden checks and
  explicit query support errors for non-queryable providers.
- Initial release
