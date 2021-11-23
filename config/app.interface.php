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
                    //value is 'false' if not local env
                    'disable_bundle' => false,
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
                'plugin_thumbnail' => [
                    'min_size' => 1,
                    'max_size' => '512000'                   
                ],
                //ref: https://www.php.net/manual/en/reserved.keywords.php
                'reserved_keywords' => array('__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor'),
            ],
        ]
    ]
];