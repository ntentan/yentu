CHANGELOG
=========

## v0.6.0 - 2026-03-02
### Added
- Exposing the configuration path and adding string for it.

### Changed
- Improved the approach for managing primary keys.
- Updated queries in the database manipulator to align with Atiaa.
- Improved debugging for definition scripts.
- Updated dependencies.

### Fixed
- Status reporting after command failures.
- Handling of missing methods in the database manipulator.
- Typos and various minor cleanups.


## v0.5.0 - 2025-04-06
### Changed
- The exception structure, so exceptions thrown for actions that are reversible are clearly marked.
- The language in the prompts used during interactive database initialization.

### Fixed
- Automatic reversals of commands that fail.
- The `DatabaseManipulatorFactory` so it generates a friendlier exception when the manipulator class cannot be found.

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
