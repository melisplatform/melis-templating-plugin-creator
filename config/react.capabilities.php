<?php

/**
 * Capacités d'outils — droits avancés du back-office React (module MelisTemplatingPluginCreator).
 *
 * Même convention que melis-cron / melis-newsletter : déclaration par `melisKey`, lue par
 * MelisReactApi\Service\Capabilities via la clé de config mergée `melisReactToolCapabilities`.
 * Pilote l'affichage des cases à cocher dans l'onglet Rights (RightsTreeView) et, côté serveur,
 * la garde `denyUnlessCan()` de MelisReactApiTemplatingPluginCreatorController.
 * Mergé dans MelisTemplatingPluginCreator\Module::getConfig().
 *
 * Sémantique DEFAULT-ALLOW : absence de donnée = tout permis (un rôle legacy garde le tout-venant).
 *
 * L'outil est un ASSISTANT en 6 étapes qui GÉNÈRE du code PHP dans le dépôt puis active un module.
 * Découper les droits par étape n'aurait pas de sens (le parcours est linéaire — interdire l'étape 3
 * rendrait l'outil inutilisable) : on les découpe par CAPACITÉ RÉELLE, chacune adossée à un endpoint :
 *
 *   wizard            accès au parcours de configuration (étapes 1 → 4)
 *   wizard.edit         └─ enregistrer/valider une étape (sinon : assistant en lecture seule)
 *   thumbnail         accès à la vignette du plugin (étape 2)
 *   thumbnail.create    └─ téléverser une vignette
 *   thumbnail.delete    └─ retirer la vignette
 *   summary           accès au récapitulatif (étape 5)
 *   summary.list        └─ lire le récapitulatif
 *   finalization      accès à la finalisation (étape 6)
 *   finalization.create └─ GÉNÉRER le plugin : écrit des fichiers PHP dans module/, réécrit
 *                          module.config.php + Module.php + les fichiers de langue, active le
 *                          module sur un site et invalide le cache de chemins de modules.
 *                          C'est la capacité sensible — la seule qui mute la plateforme.
 *
 * Les `label` sont les CLÉS de traduction Melis existantes (`tr_...`, celles des titres d'étapes),
 * résolues dans la locale courante côté serveur par MelisReactApi (rightsCapabilitiesAction).
 * Les actions (`list`/`create`/`edit`/`delete`) sont libellées par le dictionnaire i18n de l'hôte.
 */

return [
    'melisReactToolCapabilities' => [
        'melistemplatingplugincreator_tool' => [
            'tabs' => [
                ['key' => 'wizard', 'label' => 'tr_melistemplatingplugincreator_caps_wizard',
                    'actions' => ['edit']],
                ['key' => 'thumbnail', 'label' => 'tr_melistemplatingplugincreator_caps_thumbnail',
                    'actions' => ['create', 'delete']],
                ['key' => 'summary', 'label' => 'tr_melistemplatingplugincreator_caps_summary',
                    'actions' => ['list']],
                ['key' => 'finalization', 'label' => 'tr_melistemplatingplugincreator_caps_finalization',
                    'actions' => ['create']],
            ],
        ],
    ],
];
