[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / non-regression-policy.md

# Politique de non-régression

## Règle
Toute correction de bug sur la ligne stable V4.4.x doit être accompagnée d’un test.

## Contrôles minimaux
```bash
vendor/bin/phpunit --colors=never
vendor/bin/phpstan analyse src tests
composer validate:full
```

[↑ Retour au sommaire docs](index.md)
