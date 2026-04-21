[↑ Parent : racine docs](index.md)

> Breadcrumb: [Docs](index.md)

# PhpFormGenerator Wiki

## Sommaire

- [Présentation](presentation.md)
- [Fonctionnalités](features.md)
- [Installation](installation.md)
- [API Builder](builder.md)
- [API Factory](factory.md)
- [Types de champs](fields.md)
- [Validation & Soumission](validation.md)
- [Mapping des données](mapping.md)
- [Rendu HTML](rendering.md)
- [Sécurité (CSRF & Captcha)](security.md)
- [Extensibilité](extensibility.md)
- [Architecture V4](architecture.md)
- [Plugins officiels](plugins.md)
- [Migration 3.x vers 4.x](migration-v4.md)

> Ce wiki est évolutif : toute nouvelle fonctionnalité doit ajouter ou enrichir une rubrique sans casser la structure existante.
> V4.1.3 ajoute la consolidation plugins, les non-régressions runtime et la préparation release candidate.

- [Checklist release](release-checklist.md)
- [Publication d’un plugin](plugin-publishing.md)

> V4.1.4 ajoute la checklist de release finale et la documentation de publication de plugin.

- [Matrice de support](support-matrix.md)
- [API publique](public-api.md)

> V4.2.0 marque la publication stable plugins-ready avec matrice de support et clarification de l’API publique.

- [Politique de maintenance](maintenance-policy.md)
- [Workflow de correction](bugfix-workflow.md)

> V4.2.1 formalise la maintenance stable, le workflow de correction et la discipline de non-régression.

- [Feature line V4.3.0](feature-line-v4_3.md)
- [Hooks](hooks.md)
- [Themes](themes.md)
- [Schema](schema.md)

> V4.3.2 intègre les hooks dans le cycle de vie complet du formulaire et confirme le branchement runtime des thèmes.


> V4.3.3 industrialise les hooks, confirme les thèmes custom et normalise toute la documentation avec breadcrumbs et liens parent.

- [Schema Export](schema-export.md)

> V4.3.4 ajoute l’export de schéma runtime, des hooks avancés de schéma et corrige le sommaire central des release notes.


- [Lifecycle Hooks](lifecycle-hooks.md)
- [Custom Themes](custom-themes.md)

> V4.3.5 renforce l’export de schéma et ajoute une documentation dédiée au lifecycle hooks et aux thèmes custom.


- [Capacités avancées stables](advanced-capabilities.md)

> V4.4.0 promeut la feature line en capacités avancées stables.


- [Maintenance avancée](maintenance-advanced.md)
- [Politique de non-régression](non-regression-policy.md)

> V4.4.1 consolide la maintenance stable et la non-régression complète de la ligne avancée.


- [Clôture de stabilisation](stabilization-closure.md)

> V4.4.2 clôture la stabilisation de la ligne avancée avant une future V4.5.0.


- [Advanced Rendering](advanced-rendering.md)


- [Maintenance du rendu avancé](advanced-rendering-maintenance.md)

> V4.5.1 consolide le rendu avancé et sa couverture de non-régression.


- [Runtime](runtime.md)
- [Lifecycle](lifecycle.md)
- [Runtime Hooks](runtime-hooks.md)

> V4.6.0 unifie hooks, thèmes et schéma dans un runtime cohérent.


- [Conformité statique du runtime](runtime-static-conformance.md)

> V4.6.1 consolide la conformité statique complète du runtime unifié.


- [Consolidation du runtime](runtime-consolidation.md)

> V4.6.2 clôture la consolidation du runtime unifié avec des tests ciblés et une documentation de synthèse.


- [Runtime Advanced](runtime-advanced.md)
- [Hooks V2](hooks-v2.md)
- [Runtime Payload](runtime-payload.md)
- [Theme Inheritance](theme-inheritance.md)

> V4.7.0 ouvre une ligne de capacités runtime avancées avec payload typé, hooks priorisés et schéma enrichi.


- [Maintenance du runtime avancé](runtime-advanced-maintenance.md)

> V4.7.1 stabilise le runtime avancé avec de nouveaux tests ciblés et une documentation de maintenance.


- [Headless Mode](headless-mode.md)
- [Schema Frontend](schema-frontend.md)
- [Validation Export](validation-export.md)
- [UI Component Mapping](ui-component-mapping.md)

> V4.8.0 rend le moteur frontend-ready avec schéma headless, mapping UI et validation exportable.


- [Maintenance headless](headless-maintenance.md)

> V4.8.1 stabilise la ligne headless/frontend-ready avec des tests ciblés et une documentation de maintenance.


- [Frontend SDK](frontend-sdk.md)
- [Frontend SDK React](frontend-sdk-react.md)
- [Frontend SDK Vue](frontend-sdk-vue.md)
- [Frontend SDK Mobile](frontend-sdk-mobile.md)

> V4.9.0 introduit le SDK frontend officiel au-dessus du mode headless stabilisé.


- [Frontend SDK Maintenance](frontend-sdk-maintenance.md)

> V4.9.1 stabilise le SDK frontend avec des garanties de structure et de robustesse.

- [Schema Versioning](schema-versioning.md)
- [Plugin Contract](plugin-contract.md)
- [Hooks Lifecycle](hooks-lifecycle.md)

> V5.0.0 fige l’API publique, le schéma et les contrats d’extension comme base industrielle.


- [Post-release Hardening](post-release-hardening.md)

> V5.0.1 consolide la release majeure par une passe de hardening plugin/runtime/SDK.


- [Plugin Best Practices](plugin-best-practices.md)
- [Extension Lifecycle](extension-lifecycle.md)
- [Plugin Lifecycle Architecture](plugin-lifecycle-architecture.md)

> V5.1.1 sécurise l’écosystème plugins et documente une architecture lifecycle de niveau framework.


- [CLI](cli.md)
- [make:form](make-form.md)
- [make:plugin](make-plugin.md)
- [Debug Tools](debug-tools.md)

> V5.2.0 introduit la CLI officielle et l’outillage développeur du projet.


- [CLI Maintenance](cli-maintenance.md)

> V5.2.1 stabilise l’outillage CLI avec une passe de non-régression complète.


- [Schema Migrations](schema-migrations.md)
- [Schema Compatibility](schema-compatibility.md)

> V5.3.0 introduit le versionnement avancé du schéma et les migrations officielles.

- [Schema Maintenance](schema-maintenance.md)

> V5.3.1 consolide le périmètre du schéma versionné et la non-régression des migrations.


- [Frontend SDK Advanced](frontend-sdk-advanced.md)
- [UI Component Overrides](ui-component-overrides.md)
- [Frontend Schema Rendering](frontend-schema-rendering.md)

> V5.4.0 enrichit le SDK frontend avec composants UI configurables et schéma de rendu avancé.



> V5.4.1 stabilise le SDK frontend avancé et verrouille le contrat de rendu.


- [Rendering Channels](rendering-channels.md)
- [Custom Renderers](custom-renderers.md)

> V5.5.0 industrialise les thèmes et prépare un rendu multi-canal cohérent.


- [Rendering Maintenance](rendering-maintenance.md)

> V5.5.1 consolide les thèmes, les canaux et la stabilité de `runtime.rendering`.


- [Headless API](headless-api.md)
- [JSON Contract](json-contract.md)
- [Headless Submission](headless-submission.md)
- [Error Payloads](error-payloads.md)

> V5.6.0 introduit un mode headless complet et un contrat API JSON-first.


- [Headless Maintenance](headless-maintenance.md)

> V5.6.1 consolide le mode headless et la non-régression JSON-first.


- [Object Mapping](object-mapping.md)
- [Form Hydration](form-hydration.md)
- [Mapping Conventions](mapping-conventions.md)

> V5.7.0 introduit la génération assistée et le mapping objet/formulaire.

[↑ Parent : racine docs](index.md)

- [DTO Form Generation](dto-form-generation.md)
- [Schema Example Generation](schema-example-generation.md)

> V5.8.0 introduit la génération automatique de formulaires depuis DTO et schémas exemples.

- [DTO Generation Maintenance](dto-generation-maintenance.md)

> V5.8.1 consolide l’inférence DTO et la non-régression des schémas exemples.

- [DTO Attributes](dto-attributes.md)
- [Form Metadata](form-metadata.md)
- [Generation Strategy](generation-strategy.md)

> V5.9.0 introduit les attributs PHP et la génération enrichie par metadata.
