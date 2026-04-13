
# PhpFormGenerator

Refonte complète du projet legacy avec architecture moderne en `src/`.

## Installation

```bash
composer require iriven/php-form-generator
```

## Exemple

```php
use Iriven\PhpFormGenerator\FormGenerator;
use Iriven\PhpFormGenerator\Infrastructure\ArrayRequestDataProvider;

$form = new FormGenerator(new ArrayRequestDataProvider([], ['email' => 'john@example.com'], 'POST'));

echo $form
    ->open(['method' => 'post', 'action' => '/submit'])
    ->addFieldset(['legend' => 'Profil'])
    ->addText('Nom')
    ->addEmail('Email')
    ->endFieldset()
    ->addCountries('Country')
    ->addFile('Document')
    ->addSubmit('Envoyer')
    ->close();
```

## Points clés

- plus de couche legacy
- API fluide maintenue
- pré-remplissage via provider injectable
- rendu HTML échappé
- support de tous les éléments historiquement exposés
