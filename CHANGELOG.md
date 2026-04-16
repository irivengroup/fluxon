## V4.2.2

### Fixed
- Corrections issues runtime plugins (alias/extension)
- Robustesse TypeResolver (gestion erreurs et alias)
- Cohérence rendering HTML (attributes/labels/accessibilité)
- Ajustements sécurité CSRF/Captcha

### Tests
- Ajout tests de non-régression ciblés

## V4.2.1

### Added
- `docs/maintenance-policy.md`
- `docs/bugfix-workflow.md`
- `RELEASE_NOTES_V4.2.1.md`
- `VALIDATION_MANIFEST_V4.2.1.md`

### Changed
- Formalisation de la ligne V4.2.x comme ligne de maintenance stable.
- Clarification du workflow de correction et de la discipline de non-régression.

## V4.2.0

### Added
- `docs/support-matrix.md`
- `docs/public-api.md`
- `RELEASE_NOTES_V4.2.0.md`

### Changed
- Stabilisation finale de la ligne V4 comme publication stable plugins-ready.
- Clarification de la surface publique officiellement supportée.
- Packaging final et documentation de publication consolidés.

## V4.1.4

### Added
- `docs/release-checklist.md`
- `docs/plugin-publishing.md`
- `RELEASE_NOTES_V4.1.4_RC.md`
- scripts Composer `validate:full` et `rc:check`

### Changed
- Documentation de validation finale et de packaging renforcée pour la release candidate.

## V4.1.3

### Added
- Tests de non-régression plugins pour l’ordre d’enregistrement, les collisions et les plugins vides.
- Fixtures supplémentaires pour la consolidation runtime plugins.
- `RELEASE_NOTES_V4.1.3_RC1.md`.

### Changed
- Documentation plugins et extensibilité consolidée pour la préparation release candidate.

## V4.1.2

### Added
- Tests d’intégration plugins pour alias de `FieldType`, alias de `FormType` et propagation d’extensions.
- Fixtures plugin dédiées pour les tests runtime.
- Durcissement des registries avec validation des aliases/classes et gestion configurable des collisions.

### Changed
- Replaced the Scrutinizer configuration with the project-specific configuration provided for the release hardening pass.
- Documentation plugins enrichie avec la stratégie de collision et les bonnes pratiques runtime.

## V4.1.1 Fixes

### Fixed
- Fixed runtime plugin resolver parse error in `TypeResolver::shortName()`.
- Simplified Scrutinizer configuration for more robust PHPUnit + Clover coverage execution.

## V4.1.1

### Added
- Branchement runtime complet des plugins : résolution des aliases de `FieldType` et `FormType` depuis les registries plugin.
- Intégration de `FormPluginKernel` dans `FormFactory`.
- Documentation runtime des plugins dans le wiki `docs/`.

### Changed
- `TypeResolver` supporte désormais les registries runtime en plus des builtins.
- Le runtime propage aussi les extensions plugin via `ExtensionRegistry`.

## V4.1.0

### Added
- Base officielle plugins-ready avec `PluginInterface`, registries de types, `PluginRegistry` et `FormPluginKernel`.
- Wiki `docs/` enrichi avec architecture V4, plugins officiels, migration 3.x -> 4.x et extensibilité documentée.

### Changed
- Contrat public V4 clarifié dans la documentation.
- Documentation d’exploitation recentrée dans le wiki Markdown.

## V3.9.5

### Changed
- Finalisation de la série 3.9.x avec une passe de stabilisation release-ready.
- Harmonisation de la documentation de validation release.
- Ajout d’un fichier `RELEASE_NOTES_V3.9.5.md`.
- Ajout de `.gitattributes` pour améliorer les exports d’archive.
- Consolidation finale des helpers extraits par responsabilité.

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
- Split `PropertyAccessor` into `PropertyReader` and `PropertyWriter` helpers while keeping the public accessor API unchanged.
- Applied an additional long-method optimization pass across country catalog access, property access, attribute normalization, captcha validation, enum reverse transformation, collection item submission, and form rendering.
- Enabled Scrutinizer coverage ingestion via PHPUnit Clover coverage and decomposed `FormGeneratorFieldFacade` into basic fields, choice fields, and attribute normalization collaborators.
- Added `CountryCatalog`, centralized HTML attribute rendering, refactored `NativeRequest` file normalization, simplified `Count` and `Choice` constraints, and cleaned fixture form type structure for the V3.9.4 quality pass.
- Decomposed `FormSubmissionProcessor` into an orchestrator plus `Domain\Form\Submission\FieldSubmissionProcessor` for field and collection submission logic.
- Applied a second consolidation pass to reduce complexity in `PropertyAccessor::getValue()`, `FormSubmissionProcessor`, `HtmlRowRenderer`, and `CaptchaSvgRenderer`.
- Applied additional scrutiny-driven refactors to reduce method complexity in attribute building, file field normalization, captcha validation, object assignment, and captcha test structure.
- Reduced internal complexity by decomposing `OptionsResolver::matchesAllowedTypes()` and `PropertyAccessor::setValue()` into smaller private helpers.
- Decomposed `FormGenerator` into `FormGeneratorFieldFacade` and `Application\FormGenerator\OpenNormalizer`, and removed the dedicated legacy API compatibility test suite so tests now target the new public API only.
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
