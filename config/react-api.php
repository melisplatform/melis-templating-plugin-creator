<?php

/**
 * Routes + contrôleur React API fournis par MelisTemplatingPluginCreator.
 *
 * Ces routes s'ajoutent aux child_routes de `melis-react-api` (le bridge GÉNÉRIQUE). Modularité :
 * le contrôleur / les routes / l'invokable de l'outil vivent dans SON module, pas dans
 * MelisReactApi. Laminas\Stdlib\ArrayUtils::merge() fusionne les configs, via
 * MelisTemplatingPluginCreator\Module::getConfig(). L'UI est livrée par la brique React du module
 * (public/ui-react), donc l'outil n'apparaît dans le BO React que si le module est ACTIF.
 *
 * Toutes les routes sont des littéraux (aucun `:id`), donc pas de piège d'ordre de matching : le
 * seul segment variable est `/tpc/step/:step`, contraint à [1-4] et déclaré après les littéraux.
 *
 * Contrat de réponse : { success: bool, data: T, error?: string }.
 */

$controller = static fn (string $action): array => [
    '__NAMESPACE__' => 'MelisTemplatingPluginCreator\Controller',
    'controller'    => 'MelisReactApiTemplatingPluginCreator',
    'action'        => $action,
];

return [
    'router' => [
        'routes' => [
            'melis-backoffice' => [
                'child_routes' => [
                    'melis-react-api' => [
                        'child_routes' => [
                            // Contexte statique : préflight (droits FS / GD), méta des 6 étapes,
                            // langues, modules de site, sites, types d'affichage des champs.
                            'tpc-context' => [
                                'type'    => 'Segment',
                                'options' => ['route' => '/tpc/context[/]', 'defaults' => $controller('context')],
                            ],
                            // État courant de l'assistant (conteneur de session) → restaure l'UI au reload.
                            'tpc-state' => [
                                'type'    => 'Segment',
                                'options' => ['route' => '/tpc/state[/]', 'defaults' => $controller('state')],
                            ],
                            // Réinitialise l'assistant (vide la session + la vignette temporaire).
                            'tpc-reset' => [
                                'type'    => 'Segment',
                                'options' => ['route' => '/tpc/reset[/]', 'defaults' => $controller('reset')],
                            ],
                            // Récapitulatif (étape 5), mis en forme pour l'affichage.
                            'tpc-summary' => [
                                'type'    => 'Segment',
                                'options' => ['route' => '/tpc/summary[/]', 'defaults' => $controller('summary')],
                            ],
                            // Vignette du plugin (étape 2). `/remove` est déclaré AVANT `/thumbnail`
                            // pour que le plus spécifique gagne le matching.
                            'tpc-thumbnail-remove' => [
                                'type'    => 'Segment',
                                'options' => ['route' => '/tpc/thumbnail/remove[/]', 'defaults' => $controller('thumbnailRemove')],
                            ],
                            'tpc-thumbnail' => [
                                'type'    => 'Segment',
                                'options' => ['route' => '/tpc/thumbnail[/]', 'defaults' => $controller('thumbnail')],
                            ],
                            // Formulaires dynamiques de l'étape 3 : la liste des champs à traduire (étape 4)
                            // dérive des champs saisis → un endpoint dédié évite de la recalculer côté React.
                            'tpc-translation-fields' => [
                                'type'    => 'Segment',
                                'options' => ['route' => '/tpc/translation-fields[/]', 'defaults' => $controller('translationFields')],
                            ],
                            // GÉNÉRATION (étape 6) : écrit le plugin sur disque + active le module.
                            'tpc-generate' => [
                                'type'    => 'Segment',
                                'options' => ['route' => '/tpc/generate[/]', 'defaults' => $controller('generate')],
                            ],
                            // Validation + persistance d'une étape (1..4). Déclaré APRÈS les littéraux.
                            'tpc-step' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/tpc/step/:step',
                                    'constraints' => ['step' => '[1-4]'],
                                    'defaults'    => $controller('step'),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [
            'MelisTemplatingPluginCreator\Controller\MelisReactApiTemplatingPluginCreator'
                => \MelisTemplatingPluginCreator\Controller\MelisReactApiTemplatingPluginCreatorController::class,
        ],
    ],
];
