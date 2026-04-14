# PhpFormGenerator

PhpFormGenerator est un framework de formulaires PHP **autonome**. Il ne dépend d'aucun framework applicatif externe et fournit un noyau complet pour construire, afficher, soumettre, valider et mapper des formulaires simples ou complexes.

## Fonctionnalités intégrées

- architecture standalone, sans dépendance à Symfony, Laravel ou autre framework
- API builder fluide via `Application\FormGenerator`
- API factory / types de formulaires via `Application\FormFactory`
- `FormTypeInterface` réutilisable
- formulaires imbriqués
- `CollectionType` récursif
- `fieldset` natifs, y compris imbriqués
- mapping tableau et mapping objet
- validation par contraintes
- cycle d'événements :
  - `form.pre_set_data`
  - `form.pre_submit`
  - `form.submit`
  - `form.post_submit`
  - `form.validation_error`
- renderer HTML standalone
- thèmes HTML :
  - `DefaultTheme`
  - `Bootstrap5Theme`
  - `TailwindTheme`
- export JSON schema
- CSRF via abstraction dédiée
- gestion automatique du `multipart/form-data` dès qu'un champ fichier est ajouté
- support des types de champs historiques du legacy
- captcha alphanumérique sensible à la casse avec validation serveur

## Types de champs inclus

### Champs texte et saisie
- `TextType`
- `TextareaType`
- `EditorType`
- `EmailType`
- `PasswordType`
- `SearchType`
- `PhoneType`
- `UrlType`
- `HiddenType`

### Champs date et temps
- `DateType`
- `DatetimeType`
- `DatetimeLocalType`
- `TimeType`
- `MonthType`
- `WeekType`

### Champs numériques
- `IntegerType`
- `FloatType`
- `NumberType`
- `RangeType`
- `ColorType`

### Choix
- `CheckboxType`
- `RadioType`
- `SelectType`
- `YesNoType`
- `CountryType`
- `DatalistType`

### Fichiers et médias
- `FileType`
- `AudioType`
- `ImageType`
- `VideoType`

### Boutons
- `ButtonType`
- `SubmitType`
- `ResetType`

### Structure et spéciaux
- `CollectionType`
- `CaptchaType`

## API builder fluide

### Exemple rapide

```php
use Iriven\PhpFormGenerator\Application\FormGenerator;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer;

$generator = (new FormGenerator('contact'))
    ->open([
        'method' => 'POST',
        'action' => '/contact',
        'csrf_protection' => true,
    ])
    ->addFieldset([
        'legend' => 'Contact',
        'description' => 'Informations principales',
    ])
    ->addText('name', ['label' => 'Nom', 'required' => true])
    ->addEmail('email', ['label' => 'Email', 'required' => true])
    ->addTextarea('message', ['label' => 'Message'])
    ->addCaptcha('captcha', ['label' => 'Code'])
    ->endFieldset()
    ->addSubmit('send', ['label' => 'Envoyer']);

$form = $generator->getForm();
echo (new HtmlRenderer())->renderForm($form->createView());
```

### Builder legacy / historique conservé

Méthodes disponibles sur `FormGenerator` :

- `addText()`
- `addEmail()`
- `addTextarea()`
- `addEditor()`
- `addCheckbox()`
- `addHidden()`
- `addSubmit()`
- `addButton()`
- `addReset()`
- `addFile()`
- `addAudio()`
- `addImage()`
- `addVideo()`
- `addCountries()`
- `addCountry()`
- `addDatetime()`
- `addDatetimeLocal()`
- `addDate()`
- `addTime()`
- `addMonth()`
- `addWeek()`
- `addInteger()`
- `addFloat()`
- `addNumber()`
- `addRange()`
- `addColor()`
- `addPassword()`
- `addPhone()`
- `addSearch()`
- `addUrl()`
- `addRadio()`
- `addSelect()`
- `addYesNo()`
- `addDatalist()`
- `addCaptcha()`
- `addCollection()`
- `addFieldset()`
- `endFieldset()`

## API factory et FormType

### Exemple avec type de formulaire

```php
use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormBuilderInterface;
use Iriven\PhpFormGenerator\Infrastructure\Http\ArrayRequest;
use Iriven\PhpFormGenerator\Infrastructure\Options\OptionsResolver;

final class ContactType implements FormTypeInterface
{
    public function buildForm($builder, array $options = []): void
    {
        $builder
            ->add('name', \Iriven\PhpFormGenerator\Domain\Field\TextType::class, ['required' => true])
            ->add('email', \Iriven\PhpFormGenerator\Domain\Field\EmailType::class, ['required' => true])
            ->add('captcha', \Iriven\PhpFormGenerator\Domain\Field\CaptchaType::class, ['label' => 'Security code'])
            ->add('submit', \Iriven\PhpFormGenerator\Domain\Field\SubmitType::class, ['label' => 'Send']);
    }

    public function configureOptions($resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'csrf_protection' => true,
        ]);
    }
}

$factory = new FormFactory();
$form = $factory->create(ContactType::class);

$request = new ArrayRequest('POST', [
    'form' => [
        'name' => 'Jane',
        'email' => 'jane@example.com',
        'captcha' => 'Ab12X',
        '_token' => '...',
    ],
]);

$form->handleRequest($request);
```

## Utilisation

Cette rubrique regroupe les exemples d’usage du framework, depuis la création du formulaire jusqu’à la récupération et la validation des données.

### Création simple avec le builder fluide

```php
use Iriven\PhpFormGenerator\Application\FormGenerator;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer;
use Iriven\PhpFormGenerator\Infrastructure\Http\ArrayRequest;

$generator = (new FormGenerator('contact'))
    ->open([
        'method' => 'POST',
        'action' => '/contact',
    ])
    ->addText('name', ['label' => 'Name', 'required' => true])
    ->addEmail('email', ['label' => 'Email', 'required' => true])
    ->addTextarea('message', ['label' => 'Message'])
    ->addCaptcha('captcha', ['label' => 'Security code'])
    ->addSubmit('send', ['label' => 'Send']);

$form = $generator->getForm();
echo (new HtmlRenderer())->renderForm($form->createView());

$request = new ArrayRequest('POST', [
    'contact' => [
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'message' => 'Hello from the contact form.',
        'captcha' => 'ABCDE',
    ],
]);

$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    $data = $form->getData();
}
```

### Création avec la factory

```php
use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormType\ContactType;
use Iriven\PhpFormGenerator\Infrastructure\Http\ArrayRequest;

$factory = new FormFactory();
$form = $factory->create(ContactType::class);

$form->handleRequest(new ArrayRequest('POST', [
    'form' => [
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'subject' => 'Need help',
        'message' => 'Can you call me back?',
        'captcha' => 'ABCDE',
    ],
]));

if ($form->isSubmitted() && $form->isValid()) {
    $data = $form->getData();
}
```

### Mapping natif des field types et form types

Le framework intègre un mapping natif des types internes. Cela permet d’écrire des types de champs et des types de formulaires sans devoir utiliser le chemin complet des classes internes du framework.

Le moteur résout automatiquement les types connus à partir de leur nom court dans l’API factory et dans l’API builder.

```php
use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;

final class DummyType implements FormTypeInterface
{
    public function buildForm($builder, array $options = []): void
    {
        $builder
            ->add('name', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('captcha', CaptchaType::class, ['label' => 'Security code'])
            ->add('submit', SubmitType::class, ['label' => 'Send']);
    }

    public function configureOptions($resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'csrf_protection' => true,
        ]);
    }
}

$factory = new FormFactory();

$dummyForm = $factory->create(DummyType::class);
$contactForm = $factory->create(ContactType::class);
```

Le même mécanisme fonctionne aussi avec des chaînes courtes :

```php
$builder->add('country', 'CountryType', [
    'sort' => true,
    'placeholder' => 'Select a country',
]);

$form = $factory->create('ContactType');
```

### Validation et récupération des erreurs

```php
if (!$form->isValid()) {
    $errors = $form->getErrors();
}
```

### Cas complet : ContactType

```php
$form = $factory->create(ContactType::class);

$form->handleRequest(new ArrayRequest('POST', [
    'form' => [
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'phone' => '+33123456789',
        'country' => 'FR',
        'subject' => 'Project request',
        'message' => 'I would like to discuss a project.',
        'captcha' => 'ABCDE',
    ],
]));

if ($form->isSubmitted() && $form->isValid()) {
    $contactData = $form->getData();
}
```

### Cas complet : RegistrationType

```php
use Iriven\PhpFormGenerator\Application\FormType\RegistrationType;

$form = $factory->create(RegistrationType::class);

$form->handleRequest(new ArrayRequest('POST', [
    'form' => [
        'email' => 'new-user@example.com',
        'password' => 'secret123',
        'confirmPassword' => 'secret123',
        'acceptTerms' => true,
        'captcha' => 'ABCDE',
    ],
]));

if ($form->isSubmitted() && $form->isValid()) {
    $registrationData = $form->getData();
}
```

### Cas complet : InvoiceType

```php
use Iriven\PhpFormGenerator\Application\FormType\InvoiceType;

$form = $factory->create(InvoiceType::class);

$form->handleRequest(new ArrayRequest('POST', [
    'form' => [
        'customer' => [
            'name' => 'Acme Corp',
            'email' => 'billing@acme.test',
        ],
        'issuedAt' => '2026-04-13T10:30',
        'items' => [
            [
                'label' => 'Design',
                'quantity' => '2',
                'price' => '150.00',
            ],
            [
                'label' => 'Development',
                'quantity' => '5',
                'price' => '300.00',
            ],
        ],
    ],
]));

if ($form->isSubmitted() && $form->isValid()) {
    $invoiceData = $form->getData();
}
```

### Fichiers et multipart automatique

```php
$builder->add('attachment', 'FileType');
```

Dès qu’un champ fichier ou média est ajouté, le formulaire bascule automatiquement en `multipart/form-data`.

### CountryType avancé

```php
$builder->add('country', 'CountryType', [
    'region' => 'europe',
    'sort' => true,
    'placeholder' => 'Select a country',
]);
```

### Désactivation explicite du CSRF

```php
$form = $factory->create(ContactType::class, null, [
    'csrf_protection' => false,
]);
```

## Rendu HTML

```php
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\Bootstrap5Theme;

echo (new HtmlRenderer(new Bootstrap5Theme()))->renderForm($form->createView());
```

## Validation

Les contraintes intégrées incluent notamment :
- `Required`
- `Email`
- `Length`
- `Choice`
- `Regex`
- `Url`
- `Min`
- `Max`
- `Range`
- `Count`
- `Callback`
- `File`
- `MimeType`
- `MaxFileSize`

## Notes importantes

- les noms canoniques sont `addDatetime()` et `addTextarea()`
- `multipart/form-data` est géré automatiquement
- le projet est autonome et ne repose sur aucun framework externe

## Built-in application form types

The project includes reusable application-level form types:

- `ContactType`
- `InvoiceType`
- `RegistrationType`
- supporting nested types: `CustomerType`, `InvoiceLineType`

### CSRF protection defaults

CSRF protection is enabled by default.

You do not need to declare it explicitly. Pass `['csrf_protection' => false]` only when you intentionally want to disable it.

### ContactType

`ContactType` includes:
- `name`
- `email`
- `phone`
- `country`
- `subject`
- `message`
- `captcha`
- `submit`

### RegistrationType

`RegistrationType` includes:
- `email`
- `password`
- `confirmPassword`
- `acceptTerms`
- `captcha`
- `submit`

It also adds a form-level validation rule to verify password confirmation.

### InvoiceType

`InvoiceType` includes:
- nested `customer`
- `issuedAt`
- collection of `items`
- submit button

The line items use `InvoiceLineType`, and the customer section uses `CustomerType`.

The generated `FormView` now exposes the root options through both `vars` and `options` for backward-compatible inspection in tests, integrations, and custom renderers.

Additional hardening:
- `YesNoType` now exposes string choice keys consistently.
- captcha length normalization no longer triggers PHPStan false positives.

`YesNoType` now exposes canonical string keys (`yes` / `no`) for stricter static-analysis compatibility.

### CountryType built-in choices

`CountryType` now ships with the complete built-in country list provided by the project requirements.

Normalization rules applied internally:
- country codes are stored as uppercase keys
- labels are trimmed and exposed as display values
- the resulting choice map is returned as `array<string, string>`

Examples:
- `FR => France`
- `US => United States`
- `CI => Cote D'Ivoire`
- `TL => Timor-Leste`

### CountryType advanced options

`CountryType` now supports the full built-in country list with additional runtime options:

- `sort` => `true` to sort countries alphabetically by label
- `placeholder` => `'Select a country'` to render an empty first option
- `region` => one of:
  - `africa`
  - `americas`
  - `asia`
  - `europe`
  - `oceania`
  - `middle_east`

Example:

```php
$builder->add('country', \Iriven\PhpFormGenerator\Domain\Field\CountryType::class, [
    'placeholder' => 'Select a country',
    'sort' => true,
    'region' => 'europe',
]);
```

Legacy compatibility is preserved through the `SelectType` hierarchy.

### SelectType hierarchy optimization

`CountryType` now extends `SelectType` directly.

`ChoiceType` has been removed, and all internal references were normalized to the `SelectType` hierarchy.
This keeps the field model simpler while preserving the expected behavior for country and yes/no select-style fields.

## Cas complets

### Formulaire de contact complet

```php
use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormType\ContactType;
use Iriven\PhpFormGenerator\Infrastructure\Http\NativeRequest;

$factory = new FormFactory();
$form = $factory->create(ContactType::class);

$request = new NativeRequest($_SERVER['REQUEST_METHOD'], $_POST, $_FILES);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    $data = $form->getData();
    // $data['name'], $data['email'], $data['message'], etc.
}

echo (new \Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer())->renderForm($form->createView());
```

### Formulaire d'inscription avec validation métier

```php
use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormType\RegistrationType;
use Iriven\PhpFormGenerator\Infrastructure\Http\ArrayRequest;

$factory = new FormFactory();
$form = $factory->create(RegistrationType::class);

$form->handleRequest(new ArrayRequest('POST', [
    'form' => [
        'email' => 'john@example.com',
        'password' => 'Secret123',
        'confirmPassword' => 'Secret123',
        'acceptTerms' => true,
        'captcha' => 'AbC123',
    ],
]));

if ($form->isSubmitted() && $form->isValid()) {
    $data = $form->getData();
}
```

### Facture avec lignes dynamiques

```php
use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormType\InvoiceType;

$factory = new FormFactory();
$form = $factory->create(InvoiceType::class);

if ($form->isSubmitted() && $form->isValid()) {
    $invoice = $form->getData();
}
```

## V3.7 — nouveautés intégrées

### Validation groups
Vous pouvez maintenant piloter l’exécution des contraintes via `validation_groups` sur le formulaire ou sur un champ via `GroupedConstraint`.

```php
use Iriven\PhpFormGenerator\Domain\Constraint\GroupedConstraint;
use Iriven\PhpFormGenerator\Domain\Constraint\Required;

$builder->add('vatNumber', TextType::class, [
    'constraints' => [
        new GroupedConstraint(new Required(), ['Business']),
    ],
]);
```

### Contraintes conditionnelles
La contrainte `When` permet de n’exécuter une ou plusieurs contraintes que si une condition est vraie.

```php
use Iriven\PhpFormGenerator\Domain\Constraint\When;
use Iriven\PhpFormGenerator\Domain\Constraint\Required;

$builder->add('phone', TextType::class, [
    'constraints' => [
        new When(
            static fn (mixed $value, array $context): bool => ($context['data']['contactByPhone'] ?? false) === true,
            [new Required('Phone is required.')],
        ),
    ],
]);
```

### Extensions
Le noyau supporte maintenant un système d’extensions pour enrichir les field types et les formulaires sans modifier le cœur.

```php
use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Infrastructure\Extension\ExtensionRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Extension\TrimTextFieldExtension;

$registry = new ExtensionRegistry();
$registry->addFieldTypeExtension(new TrimTextFieldExtension());

$factory = new FormFactory(extensionRegistry: $registry);
```

### Upload pipeline natif
Le projet inclut désormais :
- `NativeRequest` pour fusionner `$_POST` et `$_FILES`
- `UploadedFile` comme valeur objet
- `LocalUploadedFileStorage` pour stocker physiquement un fichier validé

```php
use Iriven\PhpFormGenerator\Infrastructure\File\LocalUploadedFileStorage;

$storage = new LocalUploadedFileStorage(__DIR__ . '/uploads');
$path = $storage->store($uploadedFile, 'invoices');
```

### Enum transformer
Le transformeur `EnumTransformer` permet de mapper proprement une valeur scalar vers une enum PHP backed.

```php
use App\Enum\LeadStatus;
use Iriven\PhpFormGenerator\Domain\Transformer\EnumTransformer;

$transformer = new EnumTransformer(LeadStatus::class);
$status = $transformer->reverseTransform('new');
```

### Mapping natif des types

La factory et le builder acceptent maintenant :
- un FQCN complet
- ou un nom court de type intégré

Exemples valides :

```php
$factory->create(ContactType::class);
$factory->create('ContactType');

$builder
    ->add('name', TextType::class)
    ->add('email', 'EmailType')
    ->add('captcha', 'CaptchaType')
    ->add('submit', 'SubmitType');
```

Le moteur de résolution interne normalise ces valeurs avant instanciation.

Extension contracts now use explicit `array<string, mixed>` option typing for better static-analysis compatibility.


## Internationalisation (i18n)

Le framework intègre maintenant un système de traduction simple :

```php
use Iriven\PhpFormGenerator\Infrastructure\Translation\ArrayTranslator;

$translator = new ArrayTranslator([
    'required' => 'Ce champ est requis',
]);

echo $translator->trans('required');
```

Les messages de validation peuvent être traduits dynamiquement.

### Typage statique du translator

L’interface de traduction et son implémentation native documentent désormais explicitement les paramètres dynamiques passés à `trans()`.

Signature documentée :
- `array<string, scalar|null> $parameters`

Cela améliore la compatibilité avec PHPStan sans changer l’API publique.

### Session security hardening

The native session-based managers no longer suppress `session_start()` errors.

Current behavior:
- sessions are started explicitly and verified
- failure to start the session raises a clear `RuntimeException`
- CSRF and captcha session storage are initialized only after a valid active session exists

This applies to:
- `SessionCsrfManager`
- `SessionCaptchaManager`

### Static-analysis compatibility pass

A full compatibility pass was applied to normalize project annotations for stricter analyzers.

Highlights:
- unsupported `list<...>` annotations were normalized to `array<int, ...>`
- unsupported `class-string` style annotations were simplified where necessary
- builder and resolver contracts now use parser-friendly string annotations
- related constraint and extension annotations were normalized consistently across the project

### Enum and session reliability pass

The project now handles both enum families correctly:
- backed enums via `BackedEnum::from(...)`
- pure enums via `UnitEnum::cases()` and case-name matching

Session-based security managers were also hardened further:
- `session_start()` is now called explicitly
- session activation is verified separately afterward
- no boolean expression relies on assumptions that confuse static analyzers

### Reliability pass on enums, dynamic form entries, and form views

Recent hardening fixes also include:
- safer enum value/name extraction in `EnumTransformer`
- explicit dynamic class instantiation for nested collection entry form types
- backward-compatible `FormView->options` alias without violating readonly semantics

### Static-analysis hardening pass

A new hardening pass was applied to remove analyzer ambiguities around:
- resolved field type strings in `FormBuilder`
- dynamic nested form type instantiation in `Form`
- enum transformation logic
- nullable captcha expectations in tests

This keeps runtime behavior unchanged while improving compatibility with stricter CI analyzers.

### Refactoring and maintainability

Core hotspots were decomposed to improve maintainability:
- `FormBuilder::add()` now delegates resolution, normalization, extension application, captcha setup, file setup, and fieldset attachment to dedicated methods.
- `HtmlRenderer::renderWidget()` now delegates rendering to smaller widget-specific methods.

### Additional compatibility and maintainability pass

Recent fixes include:
- parser-friendly return annotations in `BuiltinTypeRegistry`
- reflection handling updated in `EnumTransformer`
- safer `DateTimeTransformer` fallback logic
- simplified PHPDoc on `FormBuilder` helper methods for broader analyzer compatibility
- extraction of small helper classes to start decomposing high-complexity areas

### Final analyzer compatibility pass

This pass removes remaining analyzer-specific issues around enum handling, date parsing branches, and complex array-shape docblocks, while continuing the gradual extraction of responsibilities from `Form` and `HtmlRenderer`.

### Décomposition réelle des classes complexes

La décomposition structurelle a été engagée sur les deux classes les plus chargées :

- `HtmlRenderer` est maintenant une façade qui délègue à :
  - `HtmlFieldsetRenderer`
  - `HtmlRowRenderer`
  - `HtmlWidgetRenderer`
- `Form` délègue maintenant toute la construction de vue à :
  - `FormViewBuilder`
  - `FormViewFactory`

Cela réduit la responsabilité directe des classes façade et prépare les prochaines extractions côté soumission et validation.

### Form comme orchestrateur

`Form` délègue désormais réellement :
- la soumission à `FormSubmissionProcessor`
- la validation à `FormValidationProcessor`
- le mapping final à `FormDataMappingProcessor`
- la construction de vue à `FormViewBuilder`

La classe `Form` conserve l’API publique mais n’embarque plus directement toute la logique métier interne.

### Captcha hardening

The native captcha subsystem now includes:
- mandatory mixed-case generation
- session-based expiration (TTL)
- max-attempt invalidation
- extracted `CaptchaSvgRenderer` for visual challenge rendering
- stronger noisy SVG output with additional lines, paths, and dots

Default runtime behavior:
- TTL: 300 seconds
- max attempts: 5

`SessionCaptchaManager` can also be instantiated explicitly with custom limits:

```php
$manager = new \Iriven\PhpFormGenerator\Infrastructure\Security\SessionCaptchaManager(
    ttlSeconds: 180,
    maxAttempts: 3,
);
```

Captcha session storage remains backward-compatible: the challenge code is still exposed under `$_SESSION['_pfg_captcha'][<key>]` as a string, while hardening metadata is stored separately under `$_SESSION['_pfg_captcha_meta']`.
