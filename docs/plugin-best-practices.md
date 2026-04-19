[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / plugin-best-practices.md

# Plugin Best Practices

## Objectif
Définir les bonnes pratiques pour les auteurs de plugins officiels et tiers.

## Recommandations
- ne pas lancer d’exception dans `supports()`
- garder `apply()` idempotent lorsque possible
- enregistrer les extensions dans un ordre explicite
- conserver la compatibilité avec les contrats `@api`

[↑ Retour au sommaire docs](index.md)
