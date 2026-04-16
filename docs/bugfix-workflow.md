[↑ Retour au sommaire docs](index.md)

> Breadcrumb: [Docs](index.md) / Workflow de correction

# Bugfix Workflow

## Étapes recommandées
1. reproduire le bug avec un test
2. corriger le comportement
3. vérifier `phpunit`
4. vérifier `phpstan`
5. vérifier la couverture si nécessaire
6. mettre à jour le wiki si le comportement documenté change

## Pour toute correction
- ajouter ou adapter un test de non-régression
- ne pas casser l’API publique
- documenter uniquement la rubrique concernée dans `docs/`

[↑ Retour au sommaire docs](index.md)
