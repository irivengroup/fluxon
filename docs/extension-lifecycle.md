[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / extension-lifecycle.md

# Extension Lifecycle

## Objectif
Documenter le cycle de vie d’une extension dans le runtime plugin.

## Flux
1. enregistrement dans `ExtensionRegistry`
2. résolution via `for($type)`
3. application séquentielle via `apply($type, $options)`

## Résilience
Les extensions fautives sont isolées pour éviter de casser le runtime global.

[↑ Retour au sommaire docs](index.md)
