# Changelog

## v6.0.4 - 2026-10-05
* Maintenance release.

## v6.0.3 - 2026-09-25
### Security
* **security:** declare the tool key on this module's controllers (audit item 7.0)

## v6.0.2 - 2026-08-20
### Fixed
* **templating-plugin-creator-react:** use the code-xml </> icon for the "New" toggle
### Changed
* Replace thumbnail extension with the actual file extension in the templating config
* Pushed the jquery updates from main branch
### Docs
* **melisai:** React back-office AI documentation for MelisTemplatingPluginCreator

## v6.0.1 - 2026-08-10
### Added
* **composer:** add docs link and authors block, swap zf2 keyword for laminas, bump php constraint to ^8.3|^8.5

## v6.0.0 - 2026-08-10
### Security
* **security:** add SECURITY.md (private vulnerability reporting policy)
* Fix audit findings
### Added
* **react:** full React brick for Templating Plugin Creator + fix module name casing
### Fixed
* **security:** harden legacy file/dir creation & output escaping
### Dependencies & build
* **composer:** bump melis-core/melis-tool-creator/melis-cms constraint to ^6.0
* **sync:** align melis-react branch with parent deliverable

## v5.3.1 - 2024-11-20
### Changed
* Activated new module if activate plugin is set to 1
* Saved the temp thumbnail in the root public

## v5.3.0 - 2024-09-25
### Changed
* Issue on datetimepicker
* Related to datetimepicker issue
* Related to jquery migration
* Interface roles translations
* Update jQuery 3.7.1 migration

## v5.2.0 - 2024-06-06
* Maintenance release.

## v5.1.0 - 2024-02-13
### Changed
* Display error message in case gd lib is not installed

## v5.0.0 - 2022-06-22
### Changed
* Updated checked/unchecked values
* Update site select options
* Changed deprecated ArraySerializable to ArraySerializableHydrator and updated other functions affected by php 8
### Dependencies & build
* Remove laminas paginator and updated php version
