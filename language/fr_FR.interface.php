<?php
    return [
		'tr_melistemplatingplugincreator_title' => 'Templating Plugin Creator',
		'tr_melistemplatingplugincreator_desc' => '',
		
		//Buttons
        'tr_melistemplatingplugincreator_back' => 'Retour',
        'tr_melistemplatingplugincreator_next' => 'Suivant',
        'tr_melistemplatingplugincreator_finish_and_create_the_plugin' => 'Terminer et créer le plugin',

		 // Warnings
	    'tr_melistemplatingplugincreator_fp_title' => 'Problème de droits',
	    'tr_melistemplatingplugincreator_fp_msg' => 'Pour créer ce plugin veuillez donner des droits d\'écriture pour les dossiers suivants ou contactez l\'administrateur.',
	    'tr_melistemplatingplugincreator_fp_config' => '<b>/config/melis.module.load.php</b> - Ce fichier est nécessaire pour activer le nouveau module après sa création',
	 	'tr_melistemplatingplugincreator_fp_module' => '<b>/module</b> - Le répertoire dans lequel les modules créés sont enregistrés',

	 	 // Error messages
	    'tr_melistemplatingplugincreator_err_message' => 'Impossible de procéder à l\'étape suivante',
	    'tr_melistemplatingplugincreator_err_invalid_name' => 'Seul les caractères alphabetiques sont autorisés',	

	    /*TO FOLLOW*/
	    'tr_melistemplatingplugincreator_err_invalid_field_name' => 'The input must be a valid name attribute.',

        'tr_melistemplatingplugincreator_err_long_50' => 'Valeur trop longue, elle doit être de moins de 50 caractères',
	    'tr_melistemplatingplugincreator_err_empty' => 'Valeur requise, ne peut être vide',
	    'tr_melistemplatingplugincreator_greater_than_0' => 'The input must be greather than or equal to 1',
	    'tr_melistemplatingplugincreator_value_must_be_between_1_to_25' => 'Seules les valeurs entre 1 et 25 sont autorisées',
        'tr_melistemplatingplugincreator_integer_only' => 'La valeur saisie doit être un nombre entier uniquement',
        'tr_melistemplatingplugincreator_digits_only' => 'The input must be in digits', 

        'tr_melistemplatingplugincreator_save_upload_image_imageFalseType' => 'Format d\'image invalide',
        'tr_melistemplatingplugincreator_save_upload_image_imageNotDetected' => 'Format d\'image inconnu',
        'tr_melistemplatingplugincreator_save_upload_image_imageNotReadable' => 'L\'image n\'existe pas ou n\'est pas lisible',
        'tr_melistemplatingplugincreator_save_upload_file_path_rights_error' => 'Vous n\'avez pas les droits pour éxéctuer cette action, veuillez contacter l\'administrateur',
        'tr_melistemplatingplugincreator_save_upload_empty_file' => 'Veuillez ajouter une image',
        'tr_melistemplatingplugincreator_save_upload_error_encounter' => 'Erreur lors de l\'upload de l\'image.',
        'tr_melistemplatingplugincreator_save_upload_file' => 'Fichier',
        'tr_melistemplatingplugincreator_err_module_exist' => '"%s" existe déjà, veuillez en essayer un autre',
        'tr_melistemplatingplugincreator_err_plugin_name_exist' => '"%s" existe déjà pour le module sélectionné, veuillez en choisir un autre',
        'tr_melistemplatingplugincreator_err_plugin_title_exist' => 'Le titre du plugin "%s" existe déjà pour la langue "%s" du module sélectionné',
        'tr_melistemplatingplugincreator_generate_plugin_error_encountered' => 'Erreurs lors de la génération du plugin.',
        'tr_melistemplatingplugincreator_upload_too_big' => 'La taille de l\'image ne doit pas excéder %s',

		/*Steps*/
		'tr_melistemplatingplugincreator_plugin' => 'Plugin',
		'tr_melistemplatingplugincreator_menu_texts_display' => 'Textes du menu et affichage',

		 /*TO FOLLOW*/
		'tr_melistemplatingplugincreator_main_properties' => 'Main Properties',
		'tr_melistemplatingplugincreator_properties_translation' => 'Properties\' Translation',

		'tr_melistemplatingplugincreator_summary' => 'Récapitulatif',
		'tr_melistemplatingplugincreator_finalization' => 'Finalisation',
		
		/*Step1 Form*/
		'tr_melistemplatingplugincreator_title_step_1' => 'Propriétés du plugin',
        'tr_melistemplatingplugincreator_desc_step_1' => 'Saisissez le nom du plugin.<br>Veuillez ensuite choisir la destination du code, nouveau module ou module existant.',
	    'tr_melistemplatingplugincreator_tpc_plugin_name' => 'Nom du plugin',
	    'tr_melistemplatingplugincreator_tpc_plugin_name tooltip' => 'Saisissez le nom du plugin.',	       
        'tr_melistemplatingplugincreator_tpc_plugin_destination' => 'Destination',
        'tr_melistemplatingplugincreator_tpc_plugin_destination tooltip' => 'Sélectionnez la destinations du plugin',
        'tr_melistemplatingplugincreator_destination_new_opt' => 'Nouveau module',

         /*TO FOLLOW*/
        'tr_melistemplatingplugincreator_destination_existing_opt' => 'Existing site module',

        'tr_melistemplatingplugincreator_tpc_new_module_name' => 'Nouveau nom de module',
        'tr_melistemplatingplugincreator_tpc_new_module_name tooltip' => 'Saisissez le nom du module à créer',

         /*TO FOLLOW*/
        'tr_melistemplatingplugincreator_tpc_existing_module_name' => 'Choose a site module',        
        'tr_melistemplatingplugincreator_tpc_existing_module_name tooltip' => 'Select existing site module',

        'tr_melistemplatingplugincreator_tpc_existing_module_placeholder' => 'Choisissez un module',

         //Step2 Form
        'tr_melistemplatingplugincreator_title_step_2' => 'Traductions et images du menu des plugins',
        'tr_melistemplatingplugincreator_desc_step_2' => 'Saisissez la traduction du texte dans les différentes langues, au moins une langue doit être saisie.<br>Choisissez l\'image du plugin qui apparaîtra dans le menu de droite.',
        'tr_melistemplatingplugincreator_tpc_plugin_title' => 'Titre du plugin',
        'tr_melistemplatingplugincreator_tpc_plugin_title tooltip' => 'Saisissez le titre du plugin',
        'tr_melistemplatingplugincreator_tpc_plugin_desc' => 'Description du plugin',
        'tr_melistemplatingplugincreator_tpc_plugin_desc tooltip' => 'Saisissez la description',       
        'tr_melistemplatingplugincreator_upload_thumbnail' => 'Image du plugin(190x100 recommandé)',
        'tr_melistemplatingplugincreator_upload_thumbnail tooltip' => 'Uploadez l\'image du pluginl',
        'tr_melistemplatingplugincreator_common_label_yes' => 'Oui',
        'tr_melistemplatingplugincreator_common_label_no' => 'Non',
        'tr_melistemplatingplugincreator_delete_plugin_thumbnail_title' => 'Supprimer l\'image',
        'tr_melistemplatingplugincreator_delete_plugin_thumbnail_confirm' => 'Êtes-vous sûr(e) de vouloir supprimer l\'image?', 
        'tr_melistemplatingplugincreator_delete_plugin_thumbnail_error' => 'Erreur lors de la suppression de l\'image',
        'tr_melistemplatingplugincreator_input_file_uploading' => 'Upload...', 

        //Step3 Form

        /*TO FOLLOW*/
        'tr_melistemplatingplugincreator_title_step_3' => 'Plugin\'s main properties',
        'tr_melistemplatingplugincreator_desc_step_3' => 'Set the main properties of the plugin',
        'tr_melistemplatingplugincreator_tpc_main_property_field_count' => '# of Fields',
        'tr_melistemplatingplugincreator_tpc_main_property_field_count tooltip' => 'Enter the number of fields',
        'tr_melistemplatingplugincreator_tpc_field_name' => 'Name',
        'tr_melistemplatingplugincreator_tpc_field_name tooltip' => 'Enter a valid name attribute (no spaces, must start with a letter and the rest must be either of the ff: alphanumeric character, an underscore, a dash, a colon or a period)',
        'tr_melistemplatingplugincreator_tpc_field_display_type' => 'Display Type',
        'tr_melistemplatingplugincreator_tpc_field_display_type tooltip' => 'Select the field\'s display type',
        'tr_melistemplatingplugincreator_tpc_field_is_required' => 'Is Required',
        'tr_melistemplatingplugincreator_tpc_field_is_required tooltip' => 'Select if the field is required or not',
        'tr_melistemplatingplugincreator_tpc_field_default_options' => 'Default Options',
        'tr_melistemplatingplugincreator_tpc_field_default_options tooltip' => 'Set default options',
        'tr_melistemplatingplugincreator_tpc_field_default_value' => 'Default Value',
        'tr_melistemplatingplugincreator_tpc_field_default_value tooltip' => 'Set the default value',  


        'tr_melistemplatingplugincreator_tpc_select_placeholder' => 'Choisissez',           
        'tr_melistemplatingplugincreator_tpc_field_is_required_yes' => 'Oui',
        'tr_melistemplatingplugincreator_tpc_field_is_required_no' => 'Non',  

        //Step4 Form
        'tr_melistemplatingplugincreator_title_step_4' => 'Properties\' Translation',
        'tr_melistemplatingplugincreator_desc_step_4' => 'Saisissez la traduction du texte dans les différentes langues, au moins une langue doit être saisie.',

        /*To Follow*/
        'tr_melistemplatingplugincreator_tpc_field_label'  => 'Label',
        'tr_melistemplatingplugincreator_tpc_field_label tooltip' => 'Set the field\'s name label',
        'tr_melistemplatingplugincreator_tpc_field_tooltip' => 'Tooltip',
        'tr_melistemplatingplugincreator_tpc_field_tooltip tooltip' => 'Set the tooltip description',
        'tr_melistemplatingplugincreator_tpc_dropdown_value_label' => 'Label for Dropdown Value [%s]',           
        'tr_melistemplatingplugincreator_tpc_dropdown_value_label tooltip' => 'Set the label for the dropdown value',
        'tr_melistemplatingplugincreator_field' => 'Field',


         //Step5 Form
        'tr_melistemplatingplugincreator_title_step_5' => 'Récapitulatif',
        'tr_melistemplatingplugincreator_desc_step_5' => 'Vérifiez les paramètres avant la création du plugin',

        //Step6 Form
        'tr_melistemplatingplugincreator_title_step_6' => 'Finalisation',
        'tr_melistemplatingplugincreator_desc_step_6' => 'Cochez la case pour activer le plugin à la création.',
        'tr_melistemplatingplugincreator_activate_plugin_after_creation' => 'Activer le plugin à la création',
        'tr_melistemplatingplugincreator_activate_plugin_note' => '<b>Note </b>: Activer le plugin rechargera la plateforme',
        'tr_melistemplatingplugincreator_finalization_success_title' => 'Le plugin a été créé avec succès',
        'tr_melistemplatingplugincreator_finalization_success_desc_with_counter' => 'La plateforme va se recharger dans <strong><span id="dpc-restart-cd">5</span></strong>',
        'tr_melistemplatingplugincreator_finalization_success_desc' => 'Vous pouvez activer le plugin manuellement en rechargeant la page',
        'tr_melistemplatingplugincreator_execute_aadtnl_setup' => 'Derniers réglages en cours',
        'tr_melistemplatingplugincreator_please_wait' => 'Veuillez patienter',
        'tr_melistemplatingplugincreator_refreshing' => 'Rechargement' 

    ];