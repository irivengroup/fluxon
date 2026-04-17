[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / advanced-rendering.md

# Advanced Rendering

## Objectif
Documenter le rendu avancé avec hooks et thèmes.

## Nouvelles capacités V4.5.0
- `before_render`
- `after_render`
- `FormRenderManager`

## Exemple
```php
$hooks = (new FormHookKernel())
    ->register(new BeforeRenderHook())
    ->register(new AfterRenderHook());

$renderManager = new FormRenderManager(
    new HtmlRendererFactory(new FormThemeKernel()),
    $hooks,
);

$html = $renderManager->render($form, 'tailwind');
```


## Unification V4.6.0
Le rendu avancé s’inscrit désormais dans un runtime commun via `FormRuntimeContext`.

[↑ Retour au sommaire docs](index.md)
