# PhpFormGenerator V3.2

Enterprise-oriented PHP form framework starter with fieldsets, validation, CSRF, events, themes and mappers.

## Legacy field type parity

This build preserves the full set of historically supported field types from the legacy package:

- Audio
- Button
- Captcha
- Checkbox
- Color
- Countries
- Datalist
- Date
- Datetime
- DatetimeLocal
- Editor
- Email
- File
- Hidden
- Image
- Month
- Number
- Password
- Phone
- Radio
- Range
- Reset
- Search
- Select
- Submit
- Text
- Textarea
- Time
- Url
- Video
- Week
- YesNo

## Quick start

```php
use Iriven\PhpFormGenerator\Application\FormGenerator;

$html = (new FormGenerator())
    ->open('profile', ['method' => 'POST'])
    ->addFieldset(['legend' => 'Identity'])
    ->addText('name', ['label' => 'Name'])
    ->addEmail('email', ['label' => 'Email', 'required' => true])
    ->addCountries('country', ['label' => 'Country'])
    ->endFieldset()
    ->addSubmit('save', ['label' => 'Save'])
    ->render();
```

## CI note

Composer 2.2+ blocks plugins by default. This project explicitly allows the Infection Composer plugin through `config.allow-plugins` in `composer.json`.
