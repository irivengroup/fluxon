# Architecture

## Couches

- `Application`: factory, registry, orchestration
- `Domain`: contrats, formulaires, champs, contraintes, événements
- `Infrastructure`: HTTP, mapping, CSRF
- `Presentation`: rendu HTML

## Décisions

- les formulaires sont des agrégats qui gèrent état, données, validation et vue
- les champs sont décrits par des `FieldTypeInterface`
- le rendu HTML passe par une `FormView`
- la validation est indépendante du renderer
- le mapping est injectable
