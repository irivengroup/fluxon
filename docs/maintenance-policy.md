[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / Politique de maintenance

# Maintenance Policy

## Objectif
La ligne V4.2.x est une ligne de maintenance stable.

## Ce qui entre dans V4.2.x
- corrections de bugs
- durcissement runtime
- corrections documentation
- amélioration des tests
- stabilisation CI / Scrutinizer / coverage

## Ce qui n’entre pas dans V4.2.x
- nouvelles API publiques majeures
- refactors structurels cassants
- nouveaux concepts d’extension non validés
- changements de contrat public

## Règle
Toute modification doit préserver le contrat public documenté dans `docs/public-api.md`.

[↑ Retour au sommaire docs](index.md)
