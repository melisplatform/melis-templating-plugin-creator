<?php
    return [
		'tr_melistemplatingplugincreator_title' => 'Créateur de plugin de template',
		'tr_melistemplatingplugincreator_desc' => 'Le créateur de plugin de template génère des plugins de template prêt à l\'emploi.',
		
		//Buttons
        'tr_melistemplatingplugincreator_back' => 'Retour',
        'tr_melistemplatingplugincreator_next' => 'Suivant',
        'tr_melistemplatingplugincreator_finish_and_create_the_plugin' => 'Terminer et créer le plugin',

		 // Warnings
	    'tr_melistemplatingplugincreator_fp_title' => 'Problème de droits',
	    'tr_melistemplatingplugincreator_fp_msg' => 'Pour créer ce plugin veuillez donner des droits d\'écriture pour les dossiers suivants ou contactez l\'administrateur.',
	    'tr_melistemplatingplugincreator_fp_config' => '<b>/config/melis.module.load.php</b> - Ce fichier est nécessaire pour activer le nouveau module après sa création',
	 	'tr_melistemplatingplugincreator_fp_module' => '<b>/module</b> - Le répertoire dans lequel les modules créés sont enregistrés',
        'tr_melistemplatingplugincreator_fp_temp_thumbnail' => '<b>/melis-templating-plugin-creator/public/temp-thumbnail</b> - The directory where the uploaded plugin thumbnails are temporarily saved',

	 	// Error messages
	    'tr_melistemplatingplugincreator_err_message' => 'Impossible de procéder à l\'étape suivante',
	    'tr_melistemplatingplugincreator_err_invalid_name' => 'Seul les caractères alphabetiques sont autorisés',	
	    'tr_melistemplatingplugincreator_err_invalid_field_name' => 'La saisie doit être un nom d\'attribut valide.',
        'tr_melistemplatingplugincreator_err_long_50' => 'Valeur trop longue, elle doit être de moins de 50 caractères',
	    'tr_melistemplatingplugincreator_err_empty' => 'Valeur requise, ne peut être vide',
	    'tr_melistemplatingplugincreator_greater_than_0' => 'The input must be greather than or equal to 1',
	    'tr_melistemplatingplugincreator_value_must_be_between_1_to_25' => 'Seules les valeurs entre 1 et 25 sont autorisées',
        'tr_melistemplatingplugincreator_integer_only' => 'La valeur saisie doit être un nombre entier uniquement',
        'tr_melistemplatingplugincreator_digits_only' => 'Caractères numériques uniquement', 
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
		'tr_melistemplatingplugincreator_main_properties' => 'Propriétés principales',
		'tr_melistemplatingplugincreator_properties_translation' => 'Traduction des propriétés',
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
        'tr_melistemplatingplugincreator_destination_existing_opt' => 'Module du site existant',
        'tr_melistemplatingplugincreator_tpc_new_module_name' => 'Nouveau nom de module',
        'tr_melistemplatingplugincreator_tpc_new_module_name tooltip' => 'Saisissez le nom du module à créer',
        'tr_melistemplatingplugincreator_tpc_existing_module_name' => 'Sélectionnez un module de site',        
        'tr_melistemplatingplugincreator_tpc_existing_module_name tooltip' => 'Sélectionnez le module du site existant',
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
        'tr_melistemplatingplugincreator_title_step_3' => 'Propriétés principales du plugin',
        'tr_melistemplatingplugincreator_desc_step_3' => 'Définir les propriétés principales du plugin',
        'tr_melistemplatingplugincreator_tpc_main_property_field_count' => 'Nombre de champs',
        'tr_melistemplatingplugincreator_tpc_main_property_field_count tooltip' => 'Saisissez le nombre de champs',
        'tr_melistemplatingplugincreator_tpc_field_name' => 'Nom',
        'tr_melistemplatingplugincreator_tpc_field_name tooltip' => 'Saisissez un nom d\'attribut valide (pas d\'espace, doit commencer par une lettre et ne comprendre que les caractères suivants : alphanumérique, underscore, tiret, virgule ou point)',
        'tr_melistemplatingplugincreator_tpc_field_display_type' => 'Type d\'affichage',
        'tr_melistemplatingplugincreator_tpc_field_display_type tooltip' => 'Sélectionnez le type d\'affichage du champ',
        'tr_melistemplatingplugincreator_tpc_field_is_required' => 'Requis',
        'tr_melistemplatingplugincreator_tpc_field_is_required tooltip' => 'Sélectionnez si le champ doit être requis ou non',
        'tr_melistemplatingplugincreator_tpc_field_default_options' => 'Options par défaut',
        'tr_melistemplatingplugincreator_tpc_field_default_options tooltip' => 'Définissez les options par défaut',
        'tr_melistemplatingplugincreator_tpc_field_default_value' => 'Valeur par défaut',
        'tr_melistemplatingplugincreator_tpc_field_default_value tooltip' => 'définissez la valuer par défaut', 
        'tr_melistemplatingplugincreator_tpc_select_placeholder' => 'Choisissez',           
        'tr_melistemplatingplugincreator_tpc_field_is_required_yes' => 'Oui',
        'tr_melistemplatingplugincreator_tpc_field_is_required_no' => 'Non',  

        //Step4 Form
        'tr_melistemplatingplugincreator_title_step_4' => 'Traduction des propriétés',
        'tr_melistemplatingplugincreator_desc_step_4' => 'Saisissez la traduction du texte dans les différentes langues, au moins une langue doit être saisie.',
        'tr_melistemplatingplugincreator_tpc_field_label'  => 'Label',
        'tr_melistemplatingplugincreator_tpc_field_label tooltip' => 'Définissez le nom du label du champ',
        'tr_melistemplatingplugincreator_tpc_field_tooltip' => 'Tooltip',
        'tr_melistemplatingplugincreator_tpc_field_tooltip tooltip' => 'Définissez la description du tooltip',
        'tr_melistemplatingplugincreator_tpc_dropdown_value_label' => 'Label pour la valeur du dropdown [%s]',           
        'tr_melistemplatingplugincreator_tpc_dropdown_value_label tooltip' => 'Définissez le label pour la valeur du menu déroulant',
        'tr_melistemplatingplugincreator_field' => 'Champ',

        //Step5 Form
        'tr_melistemplatingplugincreator_title_step_5' => 'Récapitulatif',
        'tr_melistemplatingplugincreator_desc_step_5' => 'Vérifiez les paramètres avant la création du plugin',

        //Step6 Form
        'tr_melistemplatingplugincreator_title_step_6' => 'Finalisation',
        'tr_melistemplatingplugincreator_desc_step_6' => 'Cochez la case pour activer le plugin à la création.',
        'tr_melistemplatingplugincreator_activate_plugin_after_creation' => 'Activer le plugin à la création',
        'tr_melistemplatingplugincreator_activate_plugin_note' => '<b>Note </b>: Activer le plugin rechargera la plateforme',
        'tr_melistemplatingplugincreator_finalization_success_title' => 'Le plugin a été créé avec succès',
        'tr_melistemplatingplugincreator_finalization_success_desc_with_counter' => 'La plateforme va se recharger dans <strong><span id="tc-restart-cd">5</span></strong>',
        'tr_melistemplatingplugincreator_finalization_success_desc' => 'Vous pouvez activer le plugin manuellement en rechargeant la page',
        'tr_melistemplatingplugincreator_execute_aadtnl_setup' => 'Derniers réglages en cours',
        'tr_melistemplatingplugincreator_please_wait' => 'Veuillez patienter',
        'tr_melistemplatingplugincreator_refreshing' => 'Rechargement' 

    ];