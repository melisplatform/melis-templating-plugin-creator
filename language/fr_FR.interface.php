<?php
    return [
		'tr_melistemplatingplugincreator_title' => 'Templating Plugin Creator',
		'tr_melistemplatingplugincreator_desc' => '',
		
		//Buttons
        'tr_melistemplatingplugincreator_back' => 'Retour',
        'tr_melistemplatingplugincreator_next' => 'Suivant',
        'tr_melistemplatingplugincreator_finish_and_create_the_plugin' => 'Terminer et cr�er le plugin',

		 // Warnings
	    'tr_melistemplatingplugincreator_fp_title' => 'Probl�me de droits',
	    'tr_melistemplatingplugincreator_fp_msg' => 'Pour cr�er ce plugin veuillez donner des droits d\'�criture pour les dossiers suivants ou contactez l\'administrateur.',
	    'tr_melistemplatingplugincreator_fp_config' => '<b>/config/melis.module.load.php</b> - Ce fichier est n�cessaire pour activer le nouveau module apr�s sa cr�ation',
	 	'tr_melistemplatingplugincreator_fp_module' => '<b>/module</b> - Le r�pertoire dans lequel les modules cr��s sont enregistr�s',

	 	 // Error messages
	    'tr_melistemplatingplugincreator_err_message' => 'Impossible de proc�der � l\'�tape suivante',
	    'tr_melistemplatingplugincreator_err_invalid_name' => 'Seul les caract�res alphabetiques sont autoris�s',	

	    /*TO FOLLOW*/
	    'tr_melistemplatingplugincreator_err_invalid_field_name' => 'The input must be a valid name attribute.',

        'tr_melistemplatingplugincreator_err_long_50' => 'Valeur trop longue, elle doit �tre de moins de 50 caract�res',
	    'tr_melistemplatingplugincreator_err_empty' => 'Valeur requise, ne peut �tre vide',
	    'tr_melistemplatingplugincreator_greater_than_0' => 'The input must be greather than or equal to 1',
	    'tr_melistemplatingplugincreator_value_must_be_between_1_to_25' => 'Seules les valeurs entre 1 et 25 sont autoris�es',
        'tr_melistemplatingplugincreator_integer_only' => 'La valeur saisie doit �tre un nombre entier uniquement',
        'tr_melistemplatingplugincreator_digits_only' => 'The input must be in digits', 

        'tr_melistemplatingplugincreator_save_upload_image_imageFalseType' => 'Format d\'image invalide',
        'tr_melistemplatingplugincreator_save_upload_image_imageNotDetected' => 'Format d\'image inconnu',
        'tr_melistemplatingplugincreator_save_upload_image_imageNotReadable' => 'L\'image n\'existe pas ou n\'est pas lisible',
        'tr_melistemplatingplugincreator_save_upload_file_path_rights_error' => 'Vous n\'avez pas les droits pour �x�ctuer cette action, veuillez contacter l\'administrateur',
        'tr_melistemplatingplugincreator_save_upload_empty_file' => 'Veuillez ajouter une image',
        'tr_melistemplatingplugincreator_save_upload_error_encounter' => 'Erreur lors de l\'upload de l\'image.',
        'tr_melistemplatingplugincreator_save_upload_file' => 'Fichier',
        'tr_melistemplatingplugincreator_err_module_exist' => '"%s" existe d�j�, veuillez en essayer un autre',
        'tr_melistemplatingplugincreator_err_plugin_name_exist' => '"%s" existe d�j� pour le module s�lectionn�, veuillez en choisir un autre',
        'tr_melistemplatingplugincreator_err_plugin_title_exist' => 'Le titre du plugin "%s" existe d�j� pour la langue "%s" du module s�lectionn�',
        'tr_melistemplatingplugincreator_generate_plugin_error_encountered' => 'Erreurs lors de la g�n�ration du plugin.',
        'tr_melistemplatingplugincreator_upload_too_big' => 'La taille de l\'image ne doit pas exc�der %s',

		/*Steps*/
		'tr_melistemplatingplugincreator_plugin' => 'Plugin',
		'tr_melistemplatingplugincreator_menu_texts_display' => 'Textes du menu et affichage',

		 /*TO FOLLOW*/
		'tr_melistemplatingplugincreator_main_properties' => 'Main Properties',
		'tr_melistemplatingplugincreator_properties_translation' => 'Properties\' Translation',

		'tr_melistemplatingplugincreator_summary' => 'R�capitulatif',
		'tr_melistemplatingplugincreator_finalization' => 'Finalisation',
		
		/*Step1 Form*/
		'tr_melistemplatingplugincreator_title_step_1' => 'Propri�t�s du plugin',
        'tr_melistemplatingplugincreator_desc_step_1' => 'Saisissez le nom du plugin.<br>Veuillez ensuite choisir la destination du code, nouveau module ou module existant.',
	    'tr_melistemplatingplugincreator_tpc_plugin_name' => 'Nom du plugin',
	    'tr_melistemplatingplugincreator_tpc_plugin_name tooltip' => 'Saisissez le nom du plugin.',	       
        'tr_melistemplatingplugincreator_tpc_plugin_destination' => 'Destination',
        'tr_melistemplatingplugincreator_tpc_plugin_destination tooltip' => 'S�lectionnez la destinations du plugin',
        'tr_melistemplatingplugincreator_destination_new_opt' => 'Nouveau module',

         /*TO FOLLOW*/
        'tr_melistemplatingplugincreator_destination_existing_opt' => 'Existing site module',

        'tr_melistemplatingplugincreator_tpc_new_module_name' => 'Nouveau nom de module',
        'tr_melistemplatingplugincreator_tpc_new_module_name tooltip' => 'Saisissez le nom du module � cr�er',

         /*TO FOLLOW*/
        'tr_melistemplatingplugincreator_tpc_existing_module_name' => 'Choose a site module',        
        'tr_melistemplatingplugincreator_tpc_existing_module_name tooltip' => 'Select existing site module',

        'tr_melistemplatingplugincreator_tpc_existing_module_placeholder' => 'Choisissez un module',

         //Step2 Form
        'tr_melistemplatingplugincreator_title_step_2' => 'Traductions et images du menu des plugins',
        'tr_melistemplatingplugincreator_desc_step_2' => 'Saisissez la traduction du texte dans les diff�rentes langues, au moins une langue doit �tre saisie.<br>Choisissez l\'image du plugin qui appara�tra dans le menu de droite.',
        'tr_melistemplatingplugincreator_tpc_plugin_title' => 'Titre du plugin',
        'tr_melistemplatingplugincreator_tpc_plugin_title tooltip' => 'Saisissez le titre du plugin',
        'tr_melistemplatingplugincreator_tpc_plugin_desc' => 'Description du plugin',
        'tr_melistemplatingplugincreator_tpc_plugin_desc tooltip' => 'Saisissez la description',       
        'tr_melistemplatingplugincreator_upload_thumbnail' => 'Image du plugin(190x100 recommand�)',
        'tr_melistemplatingplugincreator_upload_thumbnail tooltip' => 'Uploadez l\'image du pluginl',
        'tr_melistemplatingplugincreator_common_label_yes' => 'Oui',
        'tr_melistemplatingplugincreator_common_label_no' => 'Non',
        'tr_melistemplatingplugincreator_delete_plugin_thumbnail_title' => 'Supprimer l\'image',
        'tr_melistemplatingplugincreator_delete_plugin_thumbnail_confirm' => '�tes-vous s�r(e) de vouloir supprimer l\'image?', 
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
        'tr_melistemplatingplugincreator_desc_step_4' => 'Saisissez la traduction du texte dans les diff�rentes langues, au moins une langue doit �tre saisie.',

        /*To Follow*/
        'tr_melistemplatingplugincreator_tpc_field_label'  => 'Label',
        'tr_melistemplatingplugincreator_tpc_field_label tooltip' => 'Set the field\'s name label',
        'tr_melistemplatingplugincreator_tpc_field_tooltip' => 'Tooltip',
        'tr_melistemplatingplugincreator_tpc_field_tooltip tooltip' => 'Set the tooltip description',
        'tr_melistemplatingplugincreator_tpc_dropdown_value_label' => 'Label for Dropdown Value [%s]',           
        'tr_melistemplatingplugincreator_tpc_dropdown_value_label tooltip' => 'Set the label for the dropdown value',
        'tr_melistemplatingplugincreator_field' => 'Field',


         //Step5 Form
        'tr_melistemplatingplugincreator_title_step_5' => 'R�capitulatif',
        'tr_melistemplatingplugincreator_desc_step_5' => 'V�rifiez les param�tres avant la cr�ation du plugin',

        //Step6 Form
        'tr_melistemplatingplugincreator_title_step_6' => 'Finalisation',
        'tr_melistemplatingplugincreator_desc_step_6' => 'Cochez la case pour activer le plugin � la cr�ation.',
        'tr_melistemplatingplugincreator_activate_plugin_after_creation' => 'Activer le plugin � la cr�ation',
        'tr_melistemplatingplugincreator_activate_plugin_note' => '<b>Note </b>: Activer le plugin rechargera la plateforme',
        'tr_melistemplatingplugincreator_finalization_success_title' => 'Le plugin a �t� cr�� avec succ�s',
        'tr_melistemplatingplugincreator_finalization_success_desc_with_counter' => 'La plateforme va se recharger dans <strong><span id="dpc-restart-cd">5</span></strong>',
        'tr_melistemplatingplugincreator_finalization_success_desc' => 'Vous pouvez activer le plugin manuellement en rechargeant la page',
        'tr_melistemplatingplugincreator_execute_aadtnl_setup' => 'Derniers r�glages en cours',
        'tr_melistemplatingplugincreator_please_wait' => 'Veuillez patienter',
        'tr_melistemplatingplugincreator_refreshing' => 'Rechargement' 

    ];