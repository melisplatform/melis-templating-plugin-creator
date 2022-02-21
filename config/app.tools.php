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
            'forms' => [
                'melistemplatingplugincreator_step1_form' => [
                    'attributes' => [
                        'name' => 'templating-plugin-creator-step-1',
                        'id' => 'templating-plugin-creator-step-1',
                        'class' => 'templating-plugin-creator-step-1',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'type' => 'MelisText',
                                'name' => 'tpc_plugin_name',
                                'options' => [
                                    'label' => 'tr_melistemplatingplugincreator_tpc_plugin_name',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_plugin_name tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'tpc_plugin_name',
                                    'class' => 'form-control',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',
                                ],
                            ],
                        ],       
                        [
                            'spec' => [
                                'type' => 'Radio',
                                'name' => 'tpc_plugin_destination',
                                'options' => [
                                    'disable_inarray_validator' => true,
                                    'label' => 'tr_melistemplatingplugincreator_tpc_plugin_destination',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_plugin_destination tooltip',
                                    'radio-button' => true,
                                    'label_options' => [
                                        'disable_html_escape' => true,
                                    ],
                                    'value_options' => [
                                        'new_module' => 'tr_melistemplatingplugincreator_destination_new_opt',
                                        'existing_module' => 'tr_melistemplatingplugincreator_destination_existing_opt',
                                    ],                                   
                                ],
                                'attributes' => [
                                    'class' => 'form-control',
                                    'value' => '',
                                    'required' => 'required',                                    
                                ],
                            ]
                        ],
                        [
                            'spec' => [
                                'type' => 'MelisText',
                                'name' => 'tpc_new_module_name',
                                'options' => [
                                    'label' => 'tr_melistemplatingplugincreator_tpc_new_module_name',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_new_module_name tooltip',                                   
                                ],
                                'attributes' => [
                                    'id' => 'tpc_new_module_name',
                                    'class' => 'form-control',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',                                   
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'type' => 'MelisTemplatingPluginCreatorModuleSelect',
                                'name' => 'tpc_existing_module_name',
                                'options' => [
                                    'disable_inarray_validator' => true,
                                    'label' => 'tr_melistemplatingplugincreator_tpc_existing_module_name',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_existing_module_name tooltip',  
                                    'empty_option' => 'tr_melistemplatingplugincreator_tpc_existing_module_placeholder',                                 
                                ],
                                'attributes' => [
                                    'id' => 'tpc_existing_module_name',
                                    'class' => 'form-control',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',                                    
                                ],
                            ],
                        ],
                    ],
                    'input_filter' => [
                        'tpc_plugin_name' => [
                            'name'     => 'tpc_plugin_name',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'break_chain_on_failure' => true,
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistemplatingplugincreator_err_empty',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'regex',
                                    'break_chain_on_failure' => true,
                                    'options' => [
                                        'pattern' => '/^[a-zA-Z\x7f-\xff][a-zA-Z\x7f-\xff]*$/',
                                        'messages' => [\Laminas\Validator\Regex::NOT_MATCH => 'tr_melistemplatingplugincreator_err_invalid_name'],
                                        'encoding' => 'UTF-8',
                                    ],
                                ],
                                [
                                    'name'    => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max'      => 50,
                                        'messages' => [
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_melistemplatingplugincreator_err_long_50',
                                        ],
                                    ],
                                ], 
                               
                            ],
                            'filters'  => [                              
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        
                        'tpc_plugin_destination' => [
                            'name'     => 'tpc_plugin_destination',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistemplatingplugincreator_err_empty',
                                        ],
                                    ],
                                ]                              
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tpc_new_module_name' => [
                            'name'     => 'tpc_new_module_name',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'break_chain_on_failure' => true,
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistemplatingplugincreator_err_empty',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'regex',
                                    'break_chain_on_failure' => true,
                                    'options' => [
                                        'pattern' => '/^[a-zA-Z\x7f-\xff][a-zA-Z\x7f-\xff]*$/',
                                        'messages' => [\Laminas\Validator\Regex::NOT_MATCH => 'tr_melistemplatingplugincreator_err_invalid_name'],
                                        'encoding' => 'UTF-8',
                                    ],
                                ],   
                                [
                                    'name'    => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max'      => 50,
                                        'messages' => [
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_melistemplatingplugincreator_err_long_50',
                                        ],
                                    ],
                                ],  
                            ],
                            'filters'  => [
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tpc_existing_module_name' => [
                            'name'     => 'tpc_existing_module_name',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistemplatingplugincreator_err_empty',
                                        ],
                                    ],
                                ]                              
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                    ],
                ],

                //language form
                'melistemplatingplugincreator_step2_form1' => [
                    'attributes' => [
                        'name' => 'templating-plugin-creator-step-2-language-form',
                        'class' => 'templating-plugin-creator-step-2',
                        'id' => 'templating-plugin-creator-step-2',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializable',
                    'elements' => [                       
                        [
                            'spec' => [
                                'name' => 'tpc_plugin_title',
                                'type' => 'MelisText',
                                'options' => [
                                    'label' => 'tr_melistemplatingplugincreator_tpc_plugin_title',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_plugin_title tooltip',
                                ],
                                'attributes' => [
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tpc_plugin_desc',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_melistemplatingplugincreator_tpc_plugin_desc',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_plugin_desc tooltip',
                                ],
                                'attributes' => [
                                    'value' => '',
                                    'placeholder' => '',
                                    'class' => 'form-control',
                                    'rows' => 4
                                ],
                            ],
                        ],    
                        [
                            'spec' => [
                                'name' => 'tpc_lang_local',
                                'type' => 'Hidden',
                                'id' => ''
                            ],
                        ],                  
                    ],
                    'input_filter' => [
                        'tpc_plugin_title' => [
                            'name'     => 'tpc_plugin_title',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'break_chain_on_failure' => true,
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistemplatingplugincreator_err_empty',
                                        ],
                                    ],
                                ],
                                [
                                    'name'    => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max'      => 100,
                                        'messages' => [
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_melistemplatingplugincreator_err_long_100',
                                        ],
                                    ],
                                ]                               
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tpc_plugin_desc' => [
                            'name'     => 'tpc_plugin_desc',
                            'required' => false,
                            'validators' => [
                                [
                                    'name'    => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max'      => 250,
                                        'messages' => [
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_melistemplatingplugincreator_err_long_250',
                                        ],
                                    ],
                                ]
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                    ],
                ],

               //plugin thumbnail upload form
                'melistemplatingplugincreator_step2_form2' => [
                    'attributes' => array(
                        'name' => 'templating-plugin-creator-thumbnail-upload-form',
                        'id' => 'id-templating-plugin-creator-thumbnail-upload-form',
                        'class' => 'templating-plugin-creator-step-2',
                        'method' => 'POST',
                        'action' => '',
                    ),
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'tpc_plugin_upload_thumbnail',
                                'type' => 'file',
                                'options' => [
                                    'label' => 'tr_melistemplatingplugincreator_upload_thumbnail',
                                    'tooltip' => 'tr_melistemplatingplugincreator_upload_thumbnail tooltip',
                                ],
                                'attributes' => [
                                    'id' => 'tpc_plugin_upload_thumbnail',
                                    'accept' => ".gif,.jpg,.jpeg,.png",
                                    'data-classButton' => 'btn btn-primary',
                                    'class' => 'filestyle form-control',
                                    'required' => 'required',    
                                ]
                            ]
                        ],
                    ],
                    'input_filter' => [                                          
                    ],
                ],

                //step 3 form
                'melistemplatingplugincreator_step3_form1' => [
                    'attributes' => [
                        'name' => 'templating-plugin-creator-step-3-form',
                        'class' => 'templating-plugin-creator-step-3',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializable',
                    'elements' => [                    
                        [
                            'spec' => [
                                'name' => 'tpc_main_property_field_count',
                                'type' => 'MelisText',
                                'options' => [
                                    'label' => 'tr_melistemplatingplugincreator_tpc_main_property_field_count',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_main_property_field_count tooltip',
                                ],
                                'attributes' => [
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',
                                ],
                            ],
                        ],  
                        [
                            'spec' => [
                                'name' => 'tpc_property_tab_number',
                                'type' => 'Hidden',
                                'id' => '',
                                'attributes' => [
                                    'value' => '1',                                    
                                ],
                            ],
                        ],                     
                    ],
                    'input_filter' => [
                         'tpc_main_property_field_count' => [
                            'name'     => 'tpc_main_property_field_count',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'break_chain_on_failure' => true,
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistemplatingplugincreator_err_empty',
                                        ],
                                    ],
                                ],    
                                [
                                    'name' => 'IsInt',
                                    'break_chain_on_failure' => true,
                                    'options' => [
                                        'messages' => [
                                            \Laminas\I18n\Validator\IsInt::NOT_INT  => 'tr_melistemplatingplugincreator_integer_only'                                            
                                        ],                                                                       
                                    ],
                                ],    
                                [
                                    'name' => 'Between',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\Between::NOT_BETWEEN => 'tr_melistemplatingplugincreator_value_must_be_between_1_to_25',
                                            'valueNotNumeric' => 'tr_melistemplatingplugincreator_value_must_be_between_1_to_25',
                                        ],
                                        'min' => 1,
                                        'max' => 25                                  
                                    ],
                                ],                                         
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],              
                    ],
                ],

                //step 3's field form
                'melistemplatingplugincreator_step3_field_form' => [
                    'attributes' => [
                        'name' => 'templating-plugin-creator-step-3-field-form',
                        'id' => 'templating-plugin-creator-step-3-field-form',
                        'class' => 'templating-plugin-creator-step-3',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializable',
                    'elements' => [                       
                        [
                            'spec' => [
                                'name' => 'tpc_field_name',
                                'type' => 'MelisText',
                                'options' => [
                                    'label' => 'tr_melistemplatingplugincreator_tpc_field_name',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_field_name tooltip',
                                ],
                                'attributes' => [
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',
                                ],
                            ],
                        ],

                        [
                            'spec' => [
                                'name' => 'tpc_field_display_type',
                                'type' => 'Select',
                                'options' => [
                                    'disable_inarray_validator' => true,
                                    'label' => 'tr_melistemplatingplugincreator_tpc_field_display_type',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_field_display_type tooltip',  
                                    'empty_option' => 'tr_melistemplatingplugincreator_tpc_select_placeholder',  
                                    'value_options' => [
                                        'MelisText' => 'Text / Classic input',
                                        'Dropdown' => 'Dropdown',
                                        'DatePicker' => 'Date Picker',
                                        'DateTimePicker' => 'Datetime Picker', 
                                        'PageInput' => 'Page Input', 
                                        'NumericInput' => 'Numeric Input',  
                                        'Switch' => 'Switch ON/OFF green/red',                                     
                                        'Textarea' => 'Textarea',
                                        'MelisCoreTinyMCE' => 'HTML Rich (TinyMCE)',  
                                    ],
                                ],

                               'attributes' => [
                                    'id' => 'tpc_field_display_type',
                                    'class' => 'form-control',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',                                    
                                ],
                            ],
                        ],

                        [
                            'spec' => [
                                'name' => 'tpc_field_is_required',
                                'type' => 'Select',
                                'options' => [
                                    'disable_inarray_validator' => true,
                                    'label' => 'tr_melistemplatingplugincreator_tpc_field_is_required',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_field_is_required tooltip',  
                                    'empty_option' => 'tr_melistemplatingplugincreator_tpc_select_placeholder',  
                                    'value_options' => [
                                        '1' => 'tr_melistemplatingplugincreator_tpc_field_is_required_yes',
                                        '0' => 'tr_melistemplatingplugincreator_tpc_field_is_required_no',
                                    ],
                                ],

                               'attributes' => [
                                    'id' => 'tpc_field_is_required',
                                    'class' => 'form-control',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',                                    
                                ],
                            ],
                        ],

                        [
                            'spec' => [
                                'name' => 'tpc_field_default_options',
                                'type' => 'MelisText',
                                'options' => [
                                    'disable_inarray_validator' => true,
                                    'label' => 'tr_melistemplatingplugincreator_tpc_field_default_options',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_field_default_options tooltip',  
                               ],

                               'attributes' => [
                                    'id' => 'tpc_field_default_options',
                                    'class' => 'form-control',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => '',                                    
                                ],
                            ],
                        ],

                        [
                            'spec' => [
                                'name' => 'tpc_field_default_value',
                                'type' => 'MelisText',
                                'options' => [
                                    'disable_inarray_validator' => true,
                                    'label' => 'tr_melistemplatingplugincreator_tpc_field_default_value',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_field_default_value tooltip',  
                               ],

                               'attributes' => [
                                    'id' => 'tpc_field_default_value',
                                    'class' => 'form-control',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => '',                                    
                                ],
                            ],
                        ],                                      
                    ],
                    'input_filter' => [
                        'tpc_field_name' => [
                            'name'     => 'tpc_field_name',
                            'required' => true,
                            'validators' => [                                
                                [
                                    'name' => 'NotEmpty',
                                    'break_chain_on_failure' => true,
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistemplatingplugincreator_err_empty',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'regex',
                                    'options' => [
                                        'pattern' => '/^[A-Za-z]+[\w]*$/',
                                        'messages' => [\Laminas\Validator\Regex::NOT_MATCH => 'tr_melistemplatingplugincreator_err_invalid_field_name'],
                                        'encoding' => 'UTF-8',
                                    ],
                                ],
                            ],
                            'filters'  => [
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tpc_field_display_type' => [
                            'name'     => 'tpc_field_display_type',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistemplatingplugincreator_err_empty',
                                        ],
                                    ],
                                ],
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tpc_field_is_required' => [
                            'name'     => 'tpc_field_is_required',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistemplatingplugincreator_err_empty',
                                        ],
                                    ],
                                ],
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tpc_field_default_value' => [
                            'name'     => 'tpc_field_default_value',
                            'required' => false,                           
                            'filters'  => [
                               ['name' => 'StringTrim'],
                            ],
                        ],
                    ],
                ],

                //properties' translation form
                'melistemplatingplugincreator_step4' => [
                    'attributes' => [
                        'name' => 'templating-plugin-creator-step-4-language-form',
                        'class' => 'templating-plugin-creator-step-4',
                        'id' => 'templating-plugin-creator-step-4',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'tpc_lang_local',
                                'type' => 'Hidden',
                                'id' => ''
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tpc_tab_num',
                                'type' => 'Hidden',
                                'id' => ''
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tpc_field_num',
                                'type' => 'Hidden',
                                'id' => ''
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tpc_field_name',
                                'type' => 'Hidden',
                                'id' => ''
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tpc_field_label',
                                'type' => 'MelisText',
                                'options' => [
                                    'label' => 'tr_melistemplatingplugincreator_tpc_field_label',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_field_label tooltip',
                                ],
                                'attributes' => [
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'tpc_field_tooltip',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_melistemplatingplugincreator_tpc_field_tooltip',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_field_tooltip tooltip',
                                ],
                                'attributes' => [
                                    'value' => '',
                                    'placeholder' => '',
                                    'class' => 'form-control',
                                    'rows' => 4
                                ],
                            ],
                        ],                      
                    ],
                    'input_filter' => [
                        'tpc_field_label' => [
                            'name'     => 'tpc_field_label',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'break_chain_on_failure' => true,
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_melistemplatingplugincreator_err_empty',
                                        ],
                                    ],
                                ],
                                [
                                    'name'    => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max'      => 100,
                                        'messages' => [
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_melistemplatingplugincreator_err_long_100',
                                        ],
                                    ],
                                ],
                                
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'tpc_field_tooltip' => [
                            'name'     => 'tpc_field_tooltip',
                            'required' => false,
                            'validators' => [
                                [
                                    'name'    => 'StringLength',
                                    'options' => [
                                        'encoding' => 'UTF-8',
                                        'max'      => 250,
                                        'messages' => [
                                            \Laminas\Validator\StringLength::TOO_LONG => 'tr_melistemplatingplugincreator_err_long_250',
                                        ],
                                    ],
                                ]
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                                ['name' => 'StringTrim'],
                            ],
                        ],
                    ],
                ],

                'melistemplatingplugincreator_step6_form' => [
                    'attributes' => [
                        'name' => 'templating-plugin-creator-step-6',
                        'id' => 'templating-plugin-creator-step-6',
                        'class' => 'templating-plugin-creator-step-6',
                        'method' => 'POST',
                        'action' => '',
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializable',
                    'elements' => [                      
                        [
                            'spec' => [
                                'type' => 'MelisTemplatingPluginCreatorSiteSelect',
                                'name' => 'tpc_existing_site_name',
                                'options' => [
                                    'disable_inarray_validator' => true,
                                    'label' => 'tr_melistemplatingplugincreator_tpc_existing_site_name',
                                    'tooltip' => 'tr_melistemplatingplugincreator_tpc_existing_site_name tooltip',  
                                    'empty_option' => 'tr_melistemplatingplugincreator_tpc_existing_site_placeholder',                                 
                                ],
                                'attributes' => [
                                    'id' => 'tpc_existing_site_name',
                                    'class' => 'form-control',
                                    'value' => '',
                                    'placeholder' => '',
                                    'required' => 'required',                                    
                                ],
                            ],
                        ],                     
                    ],                                                       
                ],
            ]
        ]
    ]
];

