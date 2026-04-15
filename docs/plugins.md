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

## Convention recommandée

Un plugin doit idéalement exposer :

- ses `FieldType`
- ses `FormType`
- ses extensions
- sa documentation dans `docs/`
