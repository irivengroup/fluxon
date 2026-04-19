[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / plugin-lifecycle-architecture.md

# Plugin Lifecycle Architecture

## Objectif
Décrire un design pro du lifecycle plugin inspiré d’un dispatcher d’événements.

## Vision cible
- `PluginInterface` : point d’entrée d’enregistrement
- `PluginValidator` : validation pré-enregistrement
- `PluginRegistry` : stockage + exposition des registres
- `ExtensionRegistry` : extensions typées, résolues par `supports()`
- `FormPluginKernel` : orchestration runtime

## Niveau Symfony/EventDispatcher
- `plugin.pre_register`
- `plugin.post_register`
- `extension.pre_apply`
- `extension.post_apply`
- `schema.pre_enrich`
- `schema.post_enrich`

[↑ Retour au sommaire docs](index.md)
