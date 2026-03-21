# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Renamed package interfaces to `*Interface`, traits to `*Trait`, and
  abstract classes to `Abstract*` for consistency.

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
- Initial release
