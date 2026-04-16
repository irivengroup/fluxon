[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / Publication d’un plugin

# Publication d’un plugin

## Recommandations
- choisir des aliases explicites
- documenter les collisions possibles
- fournir au minimum :
  - un `PluginInterface`
  - les FieldTypes/FormTypes enregistrés
  - les extensions éventuelles
  - des tests d’intégration
  - une documentation dédiée

## Structure conseillée
- `src/`
- `tests/`
- `docs/`
- `README.md`

## Validation minimale
```bash
composer dump-autoload -o
vendor/bin/phpstan analyse src tests
vendor/bin/phpunit --colors=never
```

[↑ Retour au sommaire docs](index.md)
