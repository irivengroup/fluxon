# V4.2.2 – Bugfix & non-régression terrain

## Objectif
Corriger les anomalies remontées (CI, PHPStan, PHPUnit, usage plugins) sans modifier le contrat public.

## Contenu
- Corrections ciblées (runtime plugins, TypeResolver, rendering HTML, sécurité)
- Ajout de tests de non-régression associés
- Ajustements mineurs de documentation (rubriques concernées uniquement)

## Vérifications
```bash
composer dump-autoload -o
composer maintenance:check
vendor/bin/phpunit --colors=never
vendor/bin/phpstan analyse src tests
```
