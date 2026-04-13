# Architecture

## Application
Orchestration de la construction du formulaire et façade publique.

## Domain
Modèle métier du formulaire, éléments et contrats.

## Infrastructure
Accès aux données de requête. Aucune dépendance framework imposée.

## Presentation
Rendu HTML, échappement et normalisation des attributs.

## Principes de migration
- plus de classes legacy
- noms de classes PSR
- `strict_types=1`
- propriétés typées
- séparation nette entre données de requête, éléments, formulaire et rendu
