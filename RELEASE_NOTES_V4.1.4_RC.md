# V4.1.4 RC – Validation complète, hardening final et tag release candidate

## Objectif
Valider l’ensemble de la chaîne qualité et finaliser la documentation d’exploitation avant un tag stable.

## Inclus
- checklist de release
- guide de publication de plugin
- manifest de validation
- préparation RC finale

## Validation recommandée
```bash
composer dump-autoload -o
vendor/bin/phpstan analyse src tests
vendor/bin/phpunit --colors=never
composer test:coverage
composer release:check
```
