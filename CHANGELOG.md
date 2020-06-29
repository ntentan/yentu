CHANGELOG
=========

## v0.4.0 - 2020-02-25

### Added
- A new `Cli` class was added to handle the execution and error reporting

### Changed
- Reorganized the classes so everything resolves properly with a single request of the `Command` class from the DI container.
- Minimized dependency on the Config class.

### Fixed
- `status` and `reverse` commands broken from previous release.

## v0.3.0 - 2018-08-05
First release with a changelog