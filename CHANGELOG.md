# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Updated package dependency constraints and refreshed docblocks to match
  the current codebase.
- Renamed package interfaces to `*Interface`, traits to `*Trait`, and
  abstract classes to `Abstract*` for consistency.
- Polymorphic `owner`, `context`, and `boundary` relations now resolve
  their lookup keys through the configured morph key registry, so ULID
  and UUID owners are hydrated correctly without subclassing the token
  models.
- Added a `Cline\Bearer\Database\Models` facade so registry access
  follows the same pattern used by the other Cline packages.
- Added facade-level and service-provider integration tests for morph
  key registry wiring.

### Breaking
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
