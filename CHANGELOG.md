# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-01-XX

### Added
- Initial release of Event API Client
- `EventsClient` class for making API requests with retry logic
- `Events` static class for simplified usage
- Global helper functions (`events_config`, `events_track`)
- Comprehensive error handling and response parsing
- Configurable retry options with exponential backoff
- Support for environment variable configuration
- JSON payload validation
- Input validation for constructor parameters
- Security improvements (SSL verification, User-Agent)
- Basic test suite
- Development tools (PHPUnit, PHPStan, PHP_CodeSniffer)

### Features
- Lightweight and dependency-free (only requires ext-curl and ext-json)
- Laravel-style static usage
- Flexible configuration options
- Comprehensive error handling
- Automatic retry with exponential backoff and jitter
- Support for custom headers and timeouts
