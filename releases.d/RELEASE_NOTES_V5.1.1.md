[↑ Retour aux release notes](../RELEASE_NOTES.md)

> Breadcrumb: [Release Notes](../RELEASE_NOTES.md) / RELEASE_NOTES_V5.1.1.md

# V5.1.1 – Maintenance écosystème et sécurisation complète

## Objectif
Durcir l’écosystème plugins et stabiliser le runtime d’extensions.

## Inclus
- `PluginValidator`
- `ExtensionRegistry` résilient
- tests de non-régression plugin/runtime/extensions
- hardening CI avec `composer audit || true`
- documentation des bonnes pratiques et du lifecycle plugin

## Correctif compatibilité
- restauration des méthodes legacy de `ExtensionRegistry` encore utilisées par le runtime existant

[↑ Retour aux release notes](../RELEASE_NOTES.md)
