# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.1] - 2024-08-18

### Fixed

- Fix return type for `DownloadCommand.php` due to underlying update of the Symfony command package.

## [1.1.0] - 2024-08-18

### Changed

- Update `composer.json` to require PHP 8.2 or higher.
- Update `symfony/console` to version 7.3 or higher.
- Added `$config` attributes to some classes to remove deprecated warnings.

### Removed

- Removed PHP 8.1 from the test matrix.

## [1.0.0]

### Added

- Test matrix for PHP (8.1, 8.2, 8.3, 8.4).

### Changed

- Update actions to v4.
- Added codacy action instead of using the script.

### Removed

- Remove the `composer.lock` file (see [Libraries > Lock file](https://getcomposer.org/doc/02-libraries.md#lock-file))

## [0.4.3] - 2022-07-27

See changes for details.

## [0.4.2] - 2021-12-07

See changes for details.

## [0.4.1] - 2021-11-23

See changes for details.

## [0.4.0] - 2021-08-16

See changes for details.

## [0.3.0] - 2021-03-12

See changes for details.

## [0.2.0] - 2021-03-10

See changes for details.

## [0.1.1] - 2021-01-14

See changes for details.

## [0.1.0] - 2020-12-20

First Release!

[1.1.0]: https://github.com/26b/i18n-midoru/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/26b/i18n-midoru/compare/0.4.3...1.0.0
[0.4.3]: https://github.com/26b/i18n-midoru/compare/0.4.2...0.4.3
[0.4.2]: https://github.com/26b/i18n-midoru/compare/0.4.1...0.4.2
[0.4.1]: https://github.com/26b/i18n-midoru/compare/0.4.0...0.4.1
[0.4.0]: https://github.com/26b/i18n-midoru/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/26b/i18n-midoru/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/26b/i18n-midoru/compare/0.1.1...0.2.0
[0.1.1]: https://github.com/26b/i18n-midoru/compare/0.1.0...0.1.1
[0.1.0]: https://github.com/26b/unbabble/releases/tag/0.1.0
