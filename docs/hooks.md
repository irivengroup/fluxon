[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / Hooks

# Hooks

## Contrats introduits
- `HookInterface`
- `FormHookInterface`

## Runtime V4.3.3
La ligne V4.3.3 fournit :
- `InMemoryHookRegistry`
- `FormHookKernel`
- dispatch des hooks dans le cycle de vie complet du formulaire

## Hooks lifecycle disponibles
- `post_build`
- `pre_handle_request`
- `pre_submit`
- `validation_error`
- `post_submit`
- `post_handle_request`

## Industrialisation V4.3.3
- ordre d’exécution conservé
- stratégie claire en cas d’exception
- contexte normalisé transmis aux hooks
- support de hooks multiples sur un même nom

## Exemple
```php
$hooks = (new FormHookKernel())
    ->register(new MyHook());

$factory = new FormFactory(hookKernel: $hooks);
$form = $factory->createBuilder('demo')->getForm();
```

[↑ Retour au sommaire docs](index.md)
