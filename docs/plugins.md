[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / Plugins officiels

# Plugins officiels

## Base plugins-ready

Le projet fournit une base officielle pour des plugins :

- `Application\FormPluginKernel`
- `Infrastructure\Registry\PluginRegistry`
- `Infrastructure\Registry\InMemoryFieldTypeRegistry`
- `Infrastructure\Registry\InMemoryFormTypeRegistry`
- `Infrastructure\Registry\BuiltinRegistries`

## Branchement runtime réel

À partir de V4.1.1, les plugins sont branchés au runtime :
- résolution des `FieldType` par alias
- résolution des `FormType` par alias
- propagation des extensions via `ExtensionRegistry`
- utilisation directe dans `FormFactory`
- prise en compte par `TypeResolver`

## Hardening runtime V4.1.2

- alias vide rejeté
- classe vide rejetée
- collisions configurables
- tests d’intégration plugin ajoutés

## Consolidation V4.1.3

- tests de non-régression plugins
- validation de l’ordre d’enregistrement
- vérification des collisions autorisées/interdites
- scénario plugin vide
- préparation release candidate

## Stratégie de collision

Par défaut :
- override autorisé

Option possible :
- instancier les registries avec `allowOverride = false` pour rejeter les collisions

## Exemple d’initialisation

```php
$kernel = (new FormPluginKernel())
    ->register(new DemoPlugin());

$factory = new FormFactory(pluginKernel: $kernel);

$form = $factory->create('newsletter');
```

## Exemple builder avec alias plugin

```php
$builder = $factory->createBuilder('demo');
$builder->add('slug', 'slug');
```

## Bonnes pratiques

- utiliser des aliases courts mais explicites
- éviter les aliases trop génériques
- documenter les collisions possibles
- fournir des fixtures/tests pour chaque plugin
- ajouter une rubrique dédiée dans `docs/` pour chaque plugin publié

[↑ Retour au sommaire docs](index.md)
