## Unreleased

### Fixed
- Fixed `YesNoType::choices()` to return a true `array<string, string>` shape for PHPStan compliance.
- Fixed PHPStan issues in `YesNoType::choices()` and captcha length normalization in `FormBuilder`.


### Added
- Added reusable application form types: `ContactType`, `InvoiceType`, `RegistrationType`, `CustomerType`, and `InvoiceLineType`.

### Changed
- Simplified the select field hierarchy: `CountryType` now extends `SelectType` directly, `ChoiceType` was removed, and internal references were normalized.
- `CountryType` now supports optional sorting, placeholder rendering, and region-based filtering.
- `CountryType` now uses the full built-in country choice list with normalized uppercase codes and trimmed labels.

- `FormView` now exposes an `options` alias for `vars` to preserve backward-compatible access patterns.
- CSRF defaults are enforced consistently across factory-created forms, builder-created forms, and the fluent `FormGenerator` API.
- CSRF protection now defaults to `true` in the factory, fluent builder entry point, and generated forms.
- Documentation updated to describe built-in form types and default CSRF behavior.
