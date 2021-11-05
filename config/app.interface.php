<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'plugins' => [
        'melistemplatingplugincreator' => [
            'conf' => [
                'id' => '',
                'name' => 'tr_melistemplatingplugincreator_tool_name',
                'rightsDisplay' => 'none',
            ],
            'ressources' => [
                'js' => [
                    '/MelisTemplatingPluginCreator/js/templating-plugin-creator.js',
                    '/MelisTemplatingPluginCreator/js/bootstrap-tagsinput.js'
                ],
                'css' => [
                    '/MelisTemplatingPluginCreator/css/style.css',
                    '/MelisTemplatingPluginCreator/css/bootstrap-tagsinput.css'
                ],
                /**
                 * the "build" configuration compiles all assets into one file to make
                 * lesser requests
                 */
                'build' => [
                    // configuration to override "use_build_assets" configuration, if you want to use the normal assets for this module.
                    'disable_bundle' => true,
                    // lists of assets that will be loaded in the layout
                    'css' => [
                        '/MelisTemplatingPluginCreator/build/css/bundle.css',
                    ],
                    'js' => [
                        '/MelisTemplatingPluginCreator/build/js/bundle.js',
                    ]
                ]
            ],
            'datas' => [
                'steps' => [
                    'melistemplatingplugincreator_step1' => [
                        'name' => 'tr_melistemplatingplugincreator_plugin',
                        'icon' => 'fa-puzzle-piece'
                    ],
                    'melistemplatingplugincreator_step2' => [
                        'name' => 'tr_melistemplatingplugincreator_menu_texts_display',
                        'icon' => 'fa-language'
                    ],
                    'melistemplatingplugincreator_step3' => [
                        'name' => 'tr_melistemplatingplugincreator_main_properties',
                        'icon' => 'fa-cog'
                    ],
                    'melistemplatingplugincreator_step4' => [
                        'name' => 'tr_melistemplatingplugincreator_properties_translation',
                        'icon' => 'fa-file-text'
                    ],
                    'melistemplatingplugincreator_step5' => [
                        'name' => 'tr_melistemplatingplugincreator_summary',
                        'icon' => 'fa-list'
                    ],
                    'melistemplatingplugincreator_step6' => [
                        'name' => 'tr_melistemplatingplugincreator_finalization',
                        'icon' => 'fa-cogs'
                    ],                                       
                ],      
                'pluginIcons' => [
                    'charts'  => 'fa-bar-chart-o',
                    'calendar'  => 'fa-calendar',                                     
                    'warning_sign'  => 'fa-warning',
                    'table'  => 'fa-table',
                    'cogwheel' => 'fa-cog',
                    'chat'  => 'fa-comment',           
                    'link'  => 'fa-chain',                                       
                    'google_maps'  => 'fa-map-marker',  
                    'bin' => 'fa-trash-o',
                    'filter' => 'fa-filter',
                    'search' => 'fa-search',
                    'table' => 'fa-table',
                    'tag' => 'fa-tag',
                    'bookmark' => 'fa-bookmark',
                    'group' => 'fa-group',
                    'bell' => 'fa-bell',
                    'clock' => 'fa-clock-o',
                    'wrench' => 'fa-wrench',
                    'ban' => 'fa-ban',
                    'share' => 'fa-share',
                    'file' => 'fa-file',
                    'list' => 'fa-list',
                    'heart' => 'fa-heart',
                    'inbox' => 'fa-inbox',
                    'envelope' => 'fa-envelope'
                ],              
                'plugin_thumbnail' => [
                    'min_size' => 1,
                    'max_size' => '512000'                   
                ]  
            ],
        ]
    ]
];