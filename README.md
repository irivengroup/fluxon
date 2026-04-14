# PhpFormGenerator V3.5

Enterprise-grade PHP form framework with:

- nested `FormType` support
- recursive `CollectionType`
- fieldsets
- object and array data mapping
- form and field constraints
- event lifecycle (`PRE_SET_DATA`, `PRE_SUBMIT`, `SUBMIT`, `POST_SUBMIT`, `VALIDATION_ERROR`)
- CSRF abstraction
- HTML themes (`Default`, `Bootstrap5`, `Tailwind`)
- JSON schema export for frontend integration
- legacy field types preserved

## Quick example

```php
use Iriven\PhpFormGenerator\Application\FormFactory;
use App\Form\ProfileType;

$form = (new FormFactory())->create(ProfileType::class, $dto, [
    'name' => 'profile',
    'csrf_protection' => true,
]);

$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    $data = $form->getData();
}
```
