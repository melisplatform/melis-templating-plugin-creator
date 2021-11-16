<?php
    return [
		'tr_melistemplatingplugincreator_title' => 'Cr�ateur de plugin de template',
		'tr_melistemplatingplugincreator_desc' => 'Le cr�ateur de plugin de template g�n�re des plugins de template pr�t � l\'emploi.',
		
		//Buttons
        'tr_melistemplatingplugincreator_back' => 'Retour',
        'tr_melistemplatingplugincreator_next' => 'Suivant',
        'tr_melistemplatingplugincreator_finish_and_create_the_plugin' => 'Terminer et cr�er le plugin',

		 // Warnings
	    'tr_melistemplatingplugincreator_fp_title' => 'Probl�me de droits',
	    'tr_melistemplatingplugincreator_fp_msg' => 'Pour cr�er ce plugin veuillez donner des droits d\'�criture pour les dossiers suivants ou contactez l\'administrateur.',
	    'tr_melistemplatingplugincreator_fp_config' => '<b>/config/melis.module.load.php</b> - Ce fichier est n�cessaire pour activer le nouveau module apr�s sa cr�ation',
	 	'tr_melistemplatingplugincreator_fp_module' => '<b>/module</b> - Le r�pertoire dans lequel les modules cr��s sont enregistr�s',
        'tr_melistemplatingplugincreator_fp_temp_thumbnail' => '<b>/melis-templating-plugin-creator/public/temp-thumbnail</b> - The directory where the uploaded plugin thumbnails are temporarily saved',

	 	// Error messages
	    'tr_melistemplatingplugincreator_err_message' => 'Impossible de proc�der � l\'�tape suivante',
	    'tr_melistemplatingplugincreator_err_invalid_name' => 'Seul les caract�res alphabetiques sont autoris�s',	
	    'tr_melistemplatingplugincreator_err_invalid_field_name' => 'La saisie doit �tre un nom d\'attribut valide.',
        'tr_melistemplatingplugincreator_err_long_50' => 'Valeur trop longue, elle doit �tre de moins de 50 caract�res',
	    'tr_melistemplatingplugincreator_err_empty' => 'Valeur requise, ne peut �tre vide',
	    'tr_melistemplatingplugincreator_greater_than_0' => 'The input must be greather than or equal to 1',
	    'tr_melistemplatingplugincreator_value_must_be_between_1_to_25' => 'Seules les valeurs entre 1 et 25 sont autoris�es',
        'tr_melistemplatingplugincreator_integer_only' => 'La valeur saisie doit �tre un nombre entier uniquement',
        'tr_melistemplatingplugincreator_digits_only' => 'Caract�res num�riques uniquement', 
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
		'tr_melistemplatingplugincreator_main_properties' => 'Propri�t�s principales',
		'tr_melistemplatingplugincreator_properties_translation' => 'Traduction des propri�t�s',
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
        'tr_melistemplatingplugincreator_destination_existing_opt' => 'Module du site existant',
        'tr_melistemplatingplugincreator_tpc_new_module_name' => 'Nouveau nom de module',
        'tr_melistemplatingplugincreator_tpc_new_module_name tooltip' => 'Saisissez le nom du module � cr�er',
        'tr_melistemplatingplugincreator_tpc_existing_module_name' => 'S�lectionnez un module de site',        
        'tr_melistemplatingplugincreator_tpc_existing_module_name tooltip' => 'S�lectionnez le module du site existant',
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
        'tr_melistemplatingplugincreator_title_step_3' => 'Propri�t�s principales du plugin',
        'tr_melistemplatingplugincreator_desc_step_3' => 'D�finir les propri�t�s principales du plugin',
        'tr_melistemplatingplugincreator_tpc_main_property_field_count' => 'Nombre de champs',
        'tr_melistemplatingplugincreator_tpc_main_property_field_count tooltip' => 'Saisissez le nombre de champs',
        'tr_melistemplatingplugincreator_tpc_field_name' => 'Nom',
        'tr_melistemplatingplugincreator_tpc_field_name tooltip' => 'Saisissez un nom d\'attribut valide (pas d\'espace, doit commencer par une lettre et ne comprendre que les caract�res suivants : alphanum�rique, underscore, tiret, virgule ou point)',
        'tr_melistemplatingplugincreator_tpc_field_display_type' => 'Type d\'affichage',
        'tr_melistemplatingplugincreator_tpc_field_display_type tooltip' => 'S�lectionnez le type d\'affichage du champ',
        'tr_melistemplatingplugincreator_tpc_field_is_required' => 'Requis',
        'tr_melistemplatingplugincreator_tpc_field_is_required tooltip' => 'S�lectionnez si le champ doit �tre requis ou non',
        'tr_melistemplatingplugincreator_tpc_field_default_options' => 'Options par d�faut',
        'tr_melistemplatingplugincreator_tpc_field_default_options tooltip' => 'D�finissez les options par d�faut',
        'tr_melistemplatingplugincreator_tpc_field_default_value' => 'Valeur par d�faut',
        'tr_melistemplatingplugincreator_tpc_field_default_value tooltip' => 'd�finissez la valuer par d�faut', 
        'tr_melistemplatingplugincreator_tpc_select_placeholder' => 'Choisissez',           
        'tr_melistemplatingplugincreator_tpc_field_is_required_yes' => 'Oui',
        'tr_melistemplatingplugincreator_tpc_field_is_required_no' => 'Non',  

        //Step4 Form
        'tr_melistemplatingplugincreator_title_step_4' => 'Traduction des propri�t�s',
        'tr_melistemplatingplugincreator_desc_step_4' => 'Saisissez la traduction du texte dans les diff�rentes langues, au moins une langue doit �tre saisie.',
        'tr_melistemplatingplugincreator_tpc_field_label'  => 'Label',
        'tr_melistemplatingplugincreator_tpc_field_label tooltip' => 'D�finissez le nom du label du champ',
        'tr_melistemplatingplugincreator_tpc_field_tooltip' => 'Tooltip',
        'tr_melistemplatingplugincreator_tpc_field_tooltip tooltip' => 'D�finissez la description du tooltip',
        'tr_melistemplatingplugincreator_tpc_dropdown_value_label' => 'Label pour la valeur du dropdown [%s]',           
        'tr_melistemplatingplugincreator_tpc_dropdown_value_label tooltip' => 'D�finissez le label pour la valeur du menu d�roulant',
        'tr_melistemplatingplugincreator_field' => 'Champ',

        //Step5 Form
        'tr_melistemplatingplugincreator_title_step_5' => 'R�capitulatif',
        'tr_melistemplatingplugincreator_desc_step_5' => 'V�rifiez les param�tres avant la cr�ation du plugin',

        //Step6 Form
        'tr_melistemplatingplugincreator_title_step_6' => 'Finalisation',
        'tr_melistemplatingplugincreator_desc_step_6' => 'Cochez la case pour activer le plugin � la cr�ation.',
        'tr_melistemplatingplugincreator_activate_plugin_after_creation' => 'Activer le plugin � la cr�ation',
        'tr_melistemplatingplugincreator_activate_plugin_note' => '<b>Note </b>: Activer le plugin rechargera la plateforme',
        'tr_melistemplatingplugincreator_finalization_success_title' => 'Le plugin a �t� cr�� avec succ�s',
        'tr_melistemplatingplugincreator_finalization_success_desc_with_counter' => 'La plateforme va se recharger dans <strong><span id="tc-restart-cd">5</span></strong>',
        'tr_melistemplatingplugincreator_finalization_success_desc' => 'Vous pouvez activer le plugin manuellement en rechargeant la page',
        'tr_melistemplatingplugincreator_execute_aadtnl_setup' => 'Derniers r�glages en cours',
        'tr_melistemplatingplugincreator_please_wait' => 'Veuillez patienter',
        'tr_melistemplatingplugincreator_refreshing' => 'Rechargement' 

    ];