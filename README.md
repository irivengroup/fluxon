# PhpFormGenerator

Refonte complète moderne du projet legacy `PhpFormGenerator`.

## Points clés

- architecture en `src/`
- séparation Application / Domain / Infrastructure / Presentation
- plus de couche legacy
- API fluide moderne
- rendu HTML échappé
- tous les éléments historiquement supportés conservés :
  - audio
  - button
  - captcha
  - checkbox
  - color
  - countries
  - datalist
  - date
  - datetime
  - datetime-local
  - editor
  - email
  - file
  - hidden
  - image
  - month
  - number
  - password
  - phone
  - radio
  - range
  - reset
  - search
  - select
  - submit
  - text
  - textarea
  - time
  - url
  - video
  - week
  - yes/no

## Installation

```bash
composer install
```

## Exemple

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Iriven\PhpFormGenerator\FormGenerator;
use Iriven\PhpFormGenerator\Infrastructure\Request\ArrayRequestDataProvider;

$form = new FormGenerator(new ArrayRequestDataProvider(
    method: 'POST',
    post: ['name' => 'Ada']
));

echo $form
    ->open(['method' => 'post', 'class' => 'profile-form'])
    ->addFieldset(['legend' => 'Profil'])
    ->addText('Name')
    ->addEmail('Email')
    ->addCountries('Country')
    ->endFieldset()
    ->addSubmit('Save')
    ->close();
```
