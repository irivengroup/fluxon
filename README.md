# PhpFormGenerator V3.2 Enterprise avancée

Framework de formulaires PHP orienté entreprise avec :

- architecture `Application / Domain / Infrastructure / Presentation`
- `FormFactory`, `FormBuilder`, `Form`
- `FormTypeInterface` réutilisable
- validation par contraintes
- CSRF
- événements de formulaire
- mappers array et object
- thèmes HTML `Default`, `Bootstrap5`, `Tailwind`
- gestion native des `fieldset`
- dataset `CountryType` réintégré depuis le legacy

## Exemple rapide

```php
use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\Type\ContactType;
use Iriven\PhpFormGenerator\Infrastructure\Http\ArrayRequest;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer;

$factory = new FormFactory();
$form = $factory->create(ContactType::class, null, 'contact', [
    'method' => 'POST',
    'csrf_protection' => true,
]);

$form->handleRequest(new ArrayRequest('POST', $_POST, $_FILES));

if ($form->isSubmitted() && $form->isValid()) {
    $data = $form->data();
}

echo (new HtmlRenderer())->render($form);
```

## Types fournis

- `TextType`
- `EmailType`
- `TextareaType`
- `CheckboxType`
- `HiddenType`
- `SubmitType`
- `ChoiceType`
- `CountryType`
- `YesNoType`
- `UrlType`
- `FileType`
- `FormType`
- `CollectionType`

## Contraintes fournies

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
- `File`
- `MimeType`
- `MaxFileSize`
- `Callback`

## Points inclus

- `fieldset` imbriqués
- events `PRE_SET_DATA`, `PRE_SUBMIT`, `SUBMIT`, `POST_SUBMIT`, `VALIDATION_ERROR`
- build Scrutinizer
- GitHub Actions
- base compatible industrialisation
