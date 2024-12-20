<?php
    return [    	
        // Tool Title & Desc
        'tr_melistemplatingplugincreator_title' => 'Templating Plugin Creator',
        'tr_melistemplatingplugincreator_desc' => 'The templating plugin creator generates new ready-to-use templating plugins.',

        //Buttons
        'tr_melistemplatingplugincreator_back' => 'Back',
        'tr_melistemplatingplugincreator_next' => 'Next',
        'tr_melistemplatingplugincreator_finish_and_create_the_plugin' => 'Finish and create the plugin',

        // Warnings
        'tr_melistemplatingplugincreator_fp_title' => 'File permission denied',
        'tr_melistemplatingplugincreator_fp_msg' => 'In-order to create templating plugin using this module, please give the rights to write in the following directories or contact the administrator if the problem persists',
        'tr_melistemplatingplugincreator_fp_config' => 'In order to activate the plugin, please give the rights to write in the following file: <b>%s/config/module.load.php</b>',
        'tr_melistemplatingplugincreator_fp_config_root' => 'In order to activate the plugin, please give the rights to write in the following file: <b>%s/config/melis.module.load.php</b>',
        'tr_melistemplatingplugincreator_fp_module' => '<b>/module</b> - The directory where the created modules are saved',
        'tr_melistemplatingplugincreator_fp_temp_thumbnail' => '<b>/melis-templating-plugin-creator/public/temp-thumbnail</b> - The directory where the uploaded plugin thumbnails are temporarily saved',
        'tr_melistemplatingplugincreator_gd_library_title' => 'Missing PHP Library',
        'tr_melistemplatingplugincreator_gd_library_not_found' => 'The GD library must be installed to use this tool',

        // Error messages
        'tr_melistemplatingplugincreator_err_message' => 'Unable to proceed to the next step, please try again',
        'tr_melistemplatingplugincreator_err_invalid_name' => 'Only alphabetic characters are authorized',	
        'tr_melistemplatingplugincreator_err_invalid_field_name' => 'The input must be a valid name attribute.',
        'tr_melistemplatingplugincreator_err_long_50' => 'Value is too long, it should be less than 50 characters',
        'tr_melistemplatingplugincreator_err_empty' => 'The input is required and cannot be empty',
        'tr_melistemplatingplugincreator_greater_than_0' => 'The input must be greather than or equal to 1',
        'tr_melistemplatingplugincreator_value_must_be_between_1_to_25' => 'The input must be between 1 and 25',
        'tr_melistemplatingplugincreator_integer_only' => 'The input must be integer only',
        'tr_melistemplatingplugincreator_digits_only' => 'The input must be in digits', 

        'tr_melistemplatingplugincreator_save_upload_image_imageFalseType' => 'Invalid image format, please upload a valid image',
        'tr_melistemplatingplugincreator_save_upload_image_imageNotDetected' => 'Unknown image format, please upload a valid image',
        'tr_melistemplatingplugincreator_save_upload_image_imageNotReadable' => 'Image does not exists, or is not readable',
        'tr_melistemplatingplugincreator_save_upload_file_path_rights_error' => 'You do not have the rights to execute this action, please contact the administrator',
        'tr_melistemplatingplugincreator_save_upload_empty_file' => 'Please submit an image file',
        'tr_melistemplatingplugincreator_save_upload_error_encounter' => 'Error encountered while uploading the thumbnail.',
        'tr_melistemplatingplugincreator_save_upload_file' => 'File',
        'tr_melistemplatingplugincreator_err_module_exist' => '"%s" already exists, please try another one',
        'tr_melistemplatingplugincreator_err_module_name_reserved_keyword' => '"%s" is a reserved keyword, please try another one',
        'tr_melistemplatingplugincreator_err_plugin_name_exist' => '"%s" plugin name  already exists for the selected module, please try another one',
        'tr_melistemplatingplugincreator_err_plugin_title_exist' => '"%s" plugin title already exists for the "%s" language of the selected module, please try another one',
        'tr_melistemplatingplugincreator_generate_plugin_error_encountered' => 'Error encountered while generating the templating plugin.',
        'tr_melistemplatingplugincreator_upload_too_big' => 'The picture size should not exceed %s',

        // steps
        'tr_melistemplatingplugincreator_steps' => 'Étape',

        /*Steps*/
        'tr_melistemplatingplugincreator_plugin' => 'Plugin',
        'tr_melistemplatingplugincreator_menu_texts_display' => 'Menu Texts & Display',
        'tr_melistemplatingplugincreator_main_properties' => 'Main Properties',
        'tr_melistemplatingplugincreator_properties_translation' => 'Properties\' Translation',
        'tr_melistemplatingplugincreator_summary' => 'Summary',
        'tr_melistemplatingplugincreator_finalization' => 'Finalization',
        
        /*Step1 Form*/
        'tr_melistemplatingplugincreator_title_step_1' => 'Plugin’s properties',
        'tr_melistemplatingplugincreator_desc_step_1' => 'Enter the name of the plugin.<br>Then choose the code’s destination, new module or existing site module.',
        'tr_melistemplatingplugincreator_tpc_plugin_name' => 'Plugin name',
        'tr_melistemplatingplugincreator_tpc_plugin_name tooltip' => 'Enter Plugin name.',	    
        'tr_melistemplatingplugincreator_tpc_plugin_destination' => 'Destination',
        'tr_melistemplatingplugincreator_tpc_plugin_destination tooltip' => 'Select the plugin\'s destination',
        'tr_melistemplatingplugincreator_destination_new_opt' => 'New module',
        'tr_melistemplatingplugincreator_destination_existing_opt' => 'Existing site module',
        'tr_melistemplatingplugincreator_tpc_new_module_name' => 'New module name',
        'tr_melistemplatingplugincreator_tpc_new_module_name tooltip' => 'Enter the name of the module to be created',
        'tr_melistemplatingplugincreator_tpc_existing_module_name' => 'Choose a site module',
        'tr_melistemplatingplugincreator_tpc_existing_module_name tooltip' => 'Select existing site module',
        'tr_melistemplatingplugincreator_tpc_existing_module_placeholder' => 'Choose a module',

        //Step2 Form
        'tr_melistemplatingplugincreator_title_step_2' => 'Plugin’s menu translations and image',
        'tr_melistemplatingplugincreator_desc_step_2' => 'Enter the text translations in different languages, at least one language must be filled in.<br>Choose the image of your plugin that will appear in the right expandable menu.',
        'tr_melistemplatingplugincreator_tpc_plugin_title' => 'Plugin title',
        'tr_melistemplatingplugincreator_tpc_plugin_title tooltip' => 'Enter the plugin title',
        'tr_melistemplatingplugincreator_tpc_plugin_desc' => 'Plugin description',
        'tr_melistemplatingplugincreator_tpc_plugin_desc tooltip' => 'Enter the description',       
        'tr_melistemplatingplugincreator_upload_thumbnail' => 'Plugin thumbnail image (recommended 190x100)',
        'tr_melistemplatingplugincreator_upload_thumbnail tooltip' => 'Upload the plugin\'s thumbnail',
        'tr_melistemplatingplugincreator_common_label_yes' => 'Yes',
        'tr_melistemplatingplugincreator_common_label_no' => 'No',
        'tr_melistemplatingplugincreator_delete_plugin_thumbnail_title' => 'Delete Plugin Thumbnail',
        'tr_melistemplatingplugincreator_delete_plugin_thumbnail_confirm' => 'Are you sure you want to delete this thumbnail?', 
        'tr_melistemplatingplugincreator_delete_plugin_thumbnail_error' => 'Error encountered while deleting the plugin thumbnail',
        'tr_melistemplatingplugincreator_input_file_uploading' => 'Uploading', 
        'tr_melistemplatingplugincreator_input_file_upload_label' => 'Browse...',
        'tr_melistemplatingplugincreator_input_file_upload_placeholder' => 'No file selected.',

        //Step3 Form
        'tr_melistemplatingplugincreator_title_step_3' => 'Plugin\'s main properties',
        'tr_melistemplatingplugincreator_desc_step_3' => 'Set the main properties of the plugin',
        'tr_melistemplatingplugincreator_tpc_main_property_field_count' => 'Number of Fields',
        'tr_melistemplatingplugincreator_tpc_main_property_field_count tooltip' => 'Enter the number of fields',
        'tr_melistemplatingplugincreator_tpc_field_name' => 'Name',
        'tr_melistemplatingplugincreator_tpc_field_name tooltip' => 'Enter a valid name attribute (no spaces, must start with a letter and the rest must be either of the following: alphanumeric character or underscore)',
        'tr_melistemplatingplugincreator_tpc_field_display_type' => 'Display Type',
        'tr_melistemplatingplugincreator_tpc_field_display_type tooltip' => 'Select the field\'s display type',
        'tr_melistemplatingplugincreator_tpc_field_is_required' => 'Is Required',
        'tr_melistemplatingplugincreator_tpc_field_is_required tooltip' => 'Select if the field is required or not',
        'tr_melistemplatingplugincreator_tpc_field_default_options' => 'Default Options',
        'tr_melistemplatingplugincreator_tpc_field_default_options tooltip' => 'Set default options',
        'tr_melistemplatingplugincreator_tpc_field_default_value' => 'Default Value',
        'tr_melistemplatingplugincreator_tpc_field_default_value tooltip' => 'Set the default value',        
        'tr_melistemplatingplugincreator_tpc_select_placeholder' => 'Choose',           
        'tr_melistemplatingplugincreator_tpc_field_is_required_yes' => 'Yes',
        'tr_melistemplatingplugincreator_tpc_field_is_required_no' => 'No',  
        'tr_melistemplatingplugincreator_tpc_field_name_exist' => '"%s" field name already exists under Field "%s", please try another one',

        //Step4 Form
        'tr_melistemplatingplugincreator_title_step_4' => 'Properties\' Translation',
        'tr_melistemplatingplugincreator_desc_step_4' => 'Enter the text translations in different languages, at least one language must be filled in.',
        'tr_melistemplatingplugincreator_tpc_field_label'  => 'Label',
        'tr_melistemplatingplugincreator_tpc_field_label tooltip' => 'Set the field\'s name label',
        'tr_melistemplatingplugincreator_tpc_field_tooltip' => 'Tooltip',
        'tr_melistemplatingplugincreator_tpc_field_tooltip tooltip' => 'Set the tooltip description',
        'tr_melistemplatingplugincreator_tpc_dropdown_value_label' => 'Label for Dropdown Value [%s]',           
        'tr_melistemplatingplugincreator_tpc_dropdown_value_label tooltip' => 'Set the label for the dropdown value',
        'tr_melistemplatingplugincreator_field' => 'Field',

        //Step5 Form
        'tr_melistemplatingplugincreator_title_step_5' => 'Summary',
        'tr_melistemplatingplugincreator_desc_step_5' => 'Review your settings before creating the templating plugin',

        //Step6 Form
        'tr_melistemplatingplugincreator_title_step_6' => 'Finalization',
        'tr_melistemplatingplugincreator_desc_step_6' => 'Tick the box below if you wish to activate the plugin upon creation.',
        'tr_melistemplatingplugincreator_desc_step_6_new_module' => 'Choose a site on which the plugin will be activated.',
        'tr_melistemplatingplugincreator_desc_step_6_existing_module' => 'Click the <b>\'Finish and create the plugin\'</b> button to generate and activate the plugin', 
        'tr_melistemplatingplugincreator_activate_plugin_after_creation' => 'Activate plugin after creation',
        'tr_melistemplatingplugincreator_activate_plugin_note' => '<b>Note</b>: Activating the plugin will require to restart the platform',
        'tr_melistemplatingplugincreator_finalization_success_title' => 'The plugin has been successfully created',
        'tr_melistemplatingplugincreator_finalization_success_desc_with_counter' => 'The platform will refresh in <strong><span id="tpc-restart-cd">5</span></strong>',
        'tr_melistemplatingplugincreator_finalization_success_desc' => 'You can manually activate the plugin by reloading the page',
        'tr_melistemplatingplugincreator_execute_aadtnl_setup' => 'Executing additional setup',
        'tr_melistemplatingplugincreator_please_wait' => 'Please wait',
        'tr_melistemplatingplugincreator_refreshing' => 'Refreshing' ,
        'tr_melistemplatingplugincreator_tpc_existing_site_name' => 'Site',
        'tr_melistemplatingplugincreator_tpc_existing_site_name tooltip' => 'Select the site on which the plugin will be activated.',
        'tr_melistemplatingplugincreator_tpc_existing_site_placeholder' => 'Choose',
        'tr_melistemplatingplugincreator_tpc_existing_site_name_none' => 'None', 

    ];