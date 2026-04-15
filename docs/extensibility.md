# Extensibilité

## Objectif

PhpFormGenerator V4.1.0 expose une base officielle orientée extension et plugins.
À partir de V4.1.1, cette base est réellement branchée dans le runtime.

## Points d’extension

- `FieldTypeRegistryInterface`
- `FormTypeRegistryInterface`
- `PluginInterface`
- `ExtensionRegistry`
- `FormPluginKernel`

## Créer un FieldType personnalisé

```php
final class SlugType extends TextType
{
}
```

## Créer un FormType personnalisé

```php
final class NewsletterType implements FormTypeInterface
{
    public function buildForm($builder, array $options = []): void
    {
        $builder->add('email', EmailType::class, ['required' => true]);
    }

    public function configureOptions($resolver): void
    {
        $resolver->setDefaults(['method' => 'POST']);
    }
}
```

## Créer un plugin

```php
final class DemoPlugin implements PluginInterface
{
    public function registerFieldTypes(FieldTypeRegistryInterface $registry): void
    {
        $registry->register('slug', SlugType::class);
    }

    public function registerFormTypes(FormTypeRegistryInterface $registry): void
    {
        $registry->register('newsletter', NewsletterType::class);
    }

    public function registerExtensions(ExtensionRegistry $registry): void
    {
    }
}
```

## Utilisation runtime

```php
$kernel = (new FormPluginKernel())
    ->register(new DemoPlugin());

$factory = new FormFactory(pluginKernel: $kernel);

$form = $factory->create('newsletter');
```
