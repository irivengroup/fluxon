## Unreleased

### Added
- Added reusable application form types: `ContactType`, `InvoiceType`, `RegistrationType`, `CustomerType`, and `InvoiceLineType`.

### Changed

- `FormView` now exposes an `options` alias for `vars` to preserve backward-compatible access patterns.
- CSRF defaults are enforced consistently across factory-created forms, builder-created forms, and the fluent `FormGenerator` API.
- CSRF protection now defaults to `true` in the factory, fluent builder entry point, and generated forms.
- Documentation updated to describe built-in form types and default CSRF behavior.
