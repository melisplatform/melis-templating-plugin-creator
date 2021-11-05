<?php

return [
    'plugins' => [
        'melisfront' => [
            'conf' => [
                // user rights exclusions
                'rightsDisplay' => 'none',
            ],
            'plugins' => [
                'ModuleTplPluginNamePlugin' => [
                    'front' => [
                        'template_path' => ['#template_path'],
                        'id' => 'pluginname',                                                         
                        // List the files to be automatically included for the correct display of the plugin
                        // To overide a key, just add it again in your site module
                        // To delete an entry, use the keyword "disable" instead of the file path for the same key
                        'files' => [
                            'css' => [
                                '/ModuleTpl/plugins/css/plugin.PluginName.css',
                            ],
                            'js' => [
                                '/ModuleTpl/plugins/js/plugin.PluginName.init.js',
                            ],
                        ],                       
                    ],
                    'melis' => [
                        'subcategory' => [
                            'id' => 'BASICS',
                            'title' => 'tr_MelisFrontSubcategoryPageBasics_Title'
                        ],
                        'name' => 'tr_ModuleTplPluginNamePlugin_Name',
                        'thumbnail' => '/ModuleTpl/plugins/images/ModuleTplPluginNamePlugin_thumbnail.jpg',
                        'description' => 'tr_ModuleTplPluginNamePlugin_Description',
                        'files' => [
                            'css' => [
                                '/ModuleTpl/plugins/css/plugin.PluginName.css',
                            ],
                            'js' => [
                                '/ModuleTpl/plugins/js/plugin.PluginName.init.js',
                            ],
                        ],
                        'js_initialization' => [],
                        /*
                        * if set to empty, plugin menu will be shown under 'Others' section                       
                        *  - available section for templating plugins as of 2019-05-16
                        *    - MelisCms
                        *    - MelisMarketing
                        *    - MelisSite
                        *    - Others
                        *    - CustomProjects
                        */
                        'section' => '',
                        'modal_form' => [
                            #TABPROPERTIES                                                    
                        ]
                    ],
                ],
            ],
        ],
    ],
];