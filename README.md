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

## Utilisation détaillée

### 1. Formulaire simple
Pour les cas basiques, utilise `FormGenerator`.

### 2. Formulaire métier réutilisable
Pour factoriser, implémente un `FormTypeInterface` et crée le formulaire via `FormFactory`.

### 3. Formulaires imbriqués
Un champ peut être un sous-formulaire si son type implémente `FormTypeInterface`.

### 4. Collections
`CollectionType` permet de gérer une liste d'entrées homogènes, par exemple des lignes de facture.

### 5. Mapping d'objet
Le framework sait mapper vers un tableau ou vers un objet métier.

### 6. Événements
Tu peux ajouter des listeners et subscribers sur le dispatcher interne.

### 7. Fichiers
Dès qu'un champ `FileType`, `AudioType`, `ImageType` ou `VideoType` est ajouté, le formulaire est automatiquement rendu avec `enctype="multipart/form-data"`.

### 8. Captcha
Le `CaptchaType` :
- génère automatiquement un code alphanumérique
- longueur configurable de 5 à 8 caractères
- est sensible à la casse
- valide côté serveur via le `SessionCaptchaManager`
- affiche un challenge SVG sans dépendance GD

Exemple :

```php
$generator->addCaptcha('captcha', [
    'label' => 'Code de sécurité',
    'min_length' => 5,
    'max_length' => 8,
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
