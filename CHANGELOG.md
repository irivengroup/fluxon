## Unreleased

### Added
- Hardened captcha handling with mixed-case generation, TTL expiration, max-attempt invalidation, and extracted SVG rendering into `CaptchaSvgRenderer`.
- Added basic i18n system with TranslatorInterface and ArrayTranslator.

## Unreleased

### Added
- V3.7 validation features: `GroupedConstraint` and `When`.
- V3.7 extension system with `FieldTypeExtensionInterface`, `FormExtensionInterface`, and `ExtensionRegistry`.
- Native upload pipeline with `NativeRequest`, `UploadedFile`, and `LocalUploadedFileStorage`.
- `EnumTransformer` and `StringTrimTransformer`.
- Additional README usage scenarios and end-to-end examples.

### Changed
- Decomposed `FormGenerator` into `FormGeneratorFieldFacade` and `FormGeneratorOpenNormalizer`, and removed the dedicated legacy API compatibility test suite so tests now target the new public API only.
- Updated PHPStan-facing signatures for choice-based builder helpers and migrated remaining tests/examples from legacy `open([...])` usage to the new `open($attributes, $options)` standard.
- Added `LoginType`, introduced a clearer public API separating form `attributes` from configuration `options`, separated `choices` from HTML attributes on choice-based builder helpers, ensured hidden field labels are never rendered, and kept controlled compatibility for legacy `open([...])` calls.
- Reduced `FormBuilder` and `HtmlWidgetRenderer` complexity through real class extraction: field definition, fieldset management, form creation, widget attributes, select widgets, and simple widgets are now delegated to dedicated collaborators.
- Stabilized the framework by adding translator-aware native constraints, improved accessible HTML output (`aria-invalid`, `aria-describedby`, error/help ids, `role="alert"`), and added targeted regression tests.
- Extracted submission, validation, and data mapping out of `Form` into `FormSubmissionProcessor`, `FormValidationProcessor`, and `FormDataMappingProcessor`, turning `Form` into a real orchestrator.
- Performed a real structural decomposition: `HtmlRenderer` now delegates to dedicated fieldset/row/widget renderers, and `Form` now delegates view construction to `FormViewBuilder` and `FormViewFactory`.
- Refactored `FormBuilder::add()` and `HtmlRenderer::renderWidget()` into smaller dedicated methods to reduce complexity and improve analyzer stability.
- Applied a project-wide static-analysis compatibility pass: normalized unsupported `list<...>` and `class-string` annotations, and tightened builder/type resolver string guarantees.
- Hardened `SessionCsrfManager` and `SessionCaptchaManager`: removed suppressed `session_start()`, added explicit failure handling, and initialize session storage only after a verified active session.
- Added explicit `array<string, scalar|null>` PHPDoc typing to translator parameters for PHPStan compliance.
- Added precise PHPDoc array value types for field type extension contracts and implementations to keep PHPStan green.
- Relaxed type annotations for factory and builder APIs so built-in short names like `ContactType` and `EmailType` are valid and PHPStan-compliant.
- `FormFactory` and `FormBuilder` now support extension registries.
- Validation now supports group-aware constraint filtering.

## Unreleased

### Fixed
- Fixed `FormGenerator` syntax and finalized the V3.9.3 public API normalization so tests can exercise the new `attributes`/`options` behavior correctly.
- Added `ForgotPasswordType` and `ResetPasswordType`, fixed short-name form type resolution for `LoginType` and auth form types, normalized `open()` so `method` and `action` become form configuration values, normalized public field HTML attributes into the internal `attr` bag, and aligned `FormFactory` default CSRF behavior with real token validation.
- Aligned non-CSRF-focused tests with restored default CSRF validation by either submitting the generated token or explicitly using `NullCsrfManager` where CSRF is outside test scope.
- Fixed default CSRF behavior: when CSRF protection is enabled and no manager is provided, forms now use `SessionCsrfManager` instead of `NullCsrfManager`, restoring real default token validation.
- Fixed translator integration for CSRF validation in `FormSubmissionProcessor` so translated `_form` errors are emitted during request handling.
- Restored backward compatibility for captcha session storage by keeping the raw challenge string in `$_SESSION['_pfg_captcha']` and moving TTL/attempt metadata to `$_SESSION['_pfg_captcha_meta']`.
- Removed remaining analyzer issues in `DateTimeTransformer`, `EnumTransformer`, and `FormBuilder` docs, and continued decomposing `Form`/`HtmlRenderer` responsibilities with helper extraction.
- Fixed parser-incompatible annotations in `BuiltinTypeRegistry` and `FormBuilder`, updated enum reflection handling, corrected `DateTimeTransformer` fallback logic, and began decomposing complex classes with helper extraction.
- Hardened builder/type resolution, dynamic nested form instantiation, enum transformation, and captcha test guards to eliminate remaining analyzer ambiguities.
- Fixed enum transformer property access warnings, corrected dynamic entry form type instantiation syntax, and made `FormView->options` a writable alias initialized from `vars` without readonly violations.
- Fixed enum transformation for both pure enums and backed enums, and simplified session activation checks in session-based security managers for static-analysis compatibility.
- Fixed `YesNoType::choices()` to return a true `array<string, string>` shape for PHPStan compliance.
- Fixed PHPStan issues in `YesNoType::choices()` and captcha length normalization in `FormBuilder`.


### Added
- README expanded with a detailed `Utilisation` section including complete contact, registration, and invoice workflows with data retrieval and validation.
- `FormBuilder::add()` now resolves built-in field types from short names such as `TextType`, `EmailType`, `CountryType`, and `CaptchaType`.
- `FormFactory::create()` now resolves built-in form types from short names such as `ContactType`.
- Added a native type-resolution layer for built-in field types and application form types.
- Added reusable application form types: `ContactType`, `InvoiceType`, `RegistrationType`, `CustomerType`, and `InvoiceLineType`.

### Changed
- Simplified the select field hierarchy: `CountryType` now extends `SelectType` directly, `ChoiceType` was removed, and internal references were normalized.
- `CountryType` now supports optional sorting, placeholder rendering, and region-based filtering.
- `CountryType` now uses the full built-in country choice list with normalized uppercase codes and trimmed labels.

- `FormView` now exposes an `options` alias for `vars` to preserve backward-compatible access patterns.
- CSRF defaults are enforced consistently across factory-created forms, builder-created forms, and the fluent `FormGenerator` API.
- CSRF protection now defaults to `true` in the factory, fluent builder entry point, and generated forms.
- Documentation updated to describe built-in form types and default CSRF behavior.
