[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / Migration 3.x vers 4.x

# Migration 3.x vers 4.x

## Principes

- la couche legacy n’est plus la cible
- l’API publique doit être utilisée telle quelle
- les helpers internes optimisés ne doivent pas être consommés directement hors besoin avancé

## À utiliser
- `FormGenerator`
- `FormFactory`
- `FormTypeInterface`
- registries/plugins documentés à partir de V4.1.0

## À éviter
- dépendre d’implémentations internes non documentées
- reconstruire une compatibilité legacy

[↑ Retour au sommaire docs](index.md)
