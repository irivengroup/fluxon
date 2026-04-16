# Checklist release

## Validation technique
- `composer dump-autoload -o`
- `vendor/bin/phpstan analyse src tests`
- `vendor/bin/phpunit --colors=never`
- `composer test:coverage`
- build Scrutinizer vert

## Validation plugins
- alias `FieldType` plugin
- alias `FormType` plugin
- extension plugin appliquée
- plugin vide sans effet de bord
- collision autorisée
- collision refusée si override désactivé

## Validation documentation
- README cohérent avec l’API publique
- wiki `docs/` à jour
- changelog à jour
- release notes à jour

## Packaging
- archive ZIP générée
- manifest/checksums présents
- notes de release prêtes
