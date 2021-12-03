<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'plugins' => [
        'meliscore' => [
            'interface' => [
                'meliscore_leftmenu' => [
                    'interface' => [
                        'meliscore_toolstree_section' => [
                            'interface' => [
                                   'meliscore_tool_creatrion_designs' => [
                                    'conf' => [
                                        'id' => 'id_meliscore_tool_creatrion_designs',
                                        'melisKey' => 'meliscore_tool_creatrion_designs',
                                        'name' => 'tr_meliscore_tool_creatrion_designs',
                                        'icon' => 'fa fa-paint-brush',
                                    ],
                                    'interface' => [
                                        'meliscore_tool_tools' => [
                                            'conf' => [
                                                'id' => 'id_meliscore_tool_tools',
                                                'melisKey' => 'meliscore_tool_tools',
                                                'name' => 'tr_meliscore_tool_tools',
                                                'icon' => 'fa fa-magic',
                                            ],
                                            'interface' => [
                                                'melistemplatingplugincreator_conf' => [
                                                    'conf' => [
                                                        'type' => '/melistemplatingplugincreator/interface/melistemplatingplugincreator_tool',
                                                    ],
                                                ]
                                            ]
                                        ]
                                    ]
                                ]                               
                            ]
                        ]
                    ]
                ]
            ]
        ],

        'melistemplatingplugincreator' => [
            'interface' => [
                'melistemplatingplugincreator_tool' => [
                    'conf' => [
                        'id' => 'id_melistemplatingplugincreator_tool',
                        'melisKey' => 'melistemplatingplugincreator_tool',
                        'name' => 'tr_melistemplatingplugincreator_title',
                        'icon' => 'fa fa-puzzle-piece',
                        'follow_regular_rendering' => false,
                    ],
                    'forward' => [
                        'module' => 'MelisTemplatingPluginCreator',
                        'controller' => 'TemplatingPluginCreator',
                        'action' => 'render-tool',
                        'jscallback' => '',
                        'jsdatas' => []
                    ],
                    'interface' => [
                        'melistemplatingplugincreator_header' => [
                            'conf' => [
                                'id' => 'id_melistemplatingplugincreator_header',
                                'melisKey' => 'melistemplatingplugincreator_header',
                                'name' => 'tr_melistemplatingplugincreator_header',
                            ],
                            'forward' => [
                                'module' => 'MelisTemplatingPluginCreator',
                                'controller' => 'TemplatingPluginCreator',
                                'action' => 'render-tool-header',
                                'jscallback' => '',
                                'jsdatas' => []
                            ],
                        ],
                        'melistemplatingplugincreator_content' => [
                            'conf' => [
                                'id' => 'id_melistemplatingplugincreator_content',
                                'melisKey' => 'melistemplatingplugincreator_content',
                                'name' => 'tr_melistemplatingplugincreator_content',
                            ],
                            'forward' => [
                                'module' => 'MelisTemplatingPluginCreator',
                                'controller' => 'TemplatingPluginCreator',
                                'action' => 'render-tool-content',
                                'jscallback' => '',
                                'jsdatas' => []
                            ],
                            'interface' => [
                                'melistemplatingplugincreator_steps' => [ 
                                    'conf' => [
                                        'id' => 'id_melistemplatingplugincreator_steps',
                                        'melisKey' => 'melistemplatingplugincreator_steps',
                                        'name' => 'tr_melistemplatingplugincreator_steps',
                                    ],
                                    'forward' => [
                                        'module' => 'MelisTemplatingPluginCreator',
                                        'controller' => 'TemplatingPluginCreator',
                                        'action' => 'render-templating-plugin-creator-steps',
                                        'jscallback' => '',
                                        'jsdatas' => []
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];