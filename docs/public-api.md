[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / API publique

# Public API

## Officially supported public entry points

### Application
- `Iriven\PhpFormGenerator\Application\FormGenerator`
- `Iriven\PhpFormGenerator\Application\FormFactory`
- `Iriven\PhpFormGenerator\Application\FormPluginKernel`

### Contracts
- `Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface`
- `Iriven\PhpFormGenerator\Domain\Contract\PluginInterface`
- `Iriven\PhpFormGenerator\Domain\Contract\FieldTypeRegistryInterface`
- `Iriven\PhpFormGenerator\Domain\Contract\FormTypeRegistryInterface`

### Runtime extension points
- plugin registration through `FormPluginKernel`
- field/form alias registration through registries
- extensions through `ExtensionRegistry`

## Stable public behaviors
- fluent builder API
- factory API
- form type resolution by class and alias
- field type resolution by class and alias
- submission / validation lifecycle
- HTML rendering via public renderers

## Internal implementation details
The following namespaces are considered implementation-oriented and may evolve internally as long as the public contract stays stable:
- `Infrastructure\*`
- internal helper classes extracted during optimization passes
- internal rendering support helpers
- internal submission processors

## Recommendation
Consumer code should target the public entry points and documented contracts only.

[↑ Retour au sommaire docs](index.md)
