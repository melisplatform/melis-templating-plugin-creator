<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'router' => [
        'routes' => [
        	'melis-backoffice' => [
                'child_routes' => [
                    'application-MelisTemplatingPluginCreator' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'MelisTemplatingPluginCreator',
                            'defaults' => [
                                '__NAMESPACE__' => 'MelisTemplatingPluginCreator\Controller',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'default' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/[:controller[/:action]]',
                                    'constraints' => [
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
        	],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            // Service
            'MelisTemplatingPluginCreatorService' => \MelisTemplatingPluginCreator\Service\MelisTemplatingPluginCreatorService::class,           
        ]
    ],
    'controllers' => [
        'invokables' => [
            'MelisTemplatingPluginCreator\Controller\TemplatingPluginCreator' => \MelisTemplatingPluginCreator\Controller\TemplatingPluginCreatorController::class,
        ],
    ],
    'form_elements' => [
        'factories' => [      
            'MelisTemplatingPluginCreatorModuleSelect' => \MelisTemplatingPluginCreator\Form\Factory\MelisTemplatingPluginCreatorModuleSelectFactory::class,
            'MelisTemplatingPluginCreatorSiteSelect' => \MelisTemplatingPluginCreator\Form\Factory\MelisTemplatingPluginCreatorSiteSelectFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'melis-templating-plugin-creator/render-form'  => __DIR__ . '/../view/melis-templating-plugin-creator/templating-plugin-creator/render-form.phtml',           
            'melis-templating-plugin-creator/partial/render-step1'  => __DIR__ . '/../view/melis-templating-plugin-creator/templating-plugin-creator/partial/render-step1.phtml',
            'melis-templating-plugin-creator/partial/render-step2'  => __DIR__ . '/../view/melis-templating-plugin-creator/templating-plugin-creator/partial/render-step2.phtml',
            'melis-templating-plugin-creator/partial/render-step3'  => __DIR__ . '/../view/melis-templating-plugin-creator/templating-plugin-creator/partial/render-step3.phtml',
            'melis-templating-plugin-creator/partial/render-step4'  => __DIR__ . '/../view/melis-templating-plugin-creator/templating-plugin-creator/partial/render-step4.phtml',
            'melis-templating-plugin-creator/partial/render-step5'  => __DIR__ . '/../view/melis-templating-plugin-creator/templating-plugin-creator/partial/render-step5.phtml',
            'melis-templating-plugin-creator/partial/render-step6'  => __DIR__ . '/../view/melis-templating-plugin-creator/templating-plugin-creator/partial/render-step6.phtml',
            'melis-templating-plugin-creator/render-step6-finalization'  => __DIR__ . '/../view/melis-templating-plugin-creator/templating-plugin-creator/render-step6-finalization.phtml',
            'melis-templating-plugin-creator/partial/field-form'  => __DIR__ . '/../view/melis-templating-plugin-creator/templating-plugin-creator/partial/field-form.phtml',

        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
