<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisTemplatingPluginCreator\Controller;

use Laminas\Session\Container;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Laminas\Form\Form;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\File\IsImage;
use Laminas\Validator\File\Size;
use Laminas\File\Transfer\Adapter\Http;
use MelisCore\Controller\MelisAbstractActionController;

class TemplatingPluginCreatorController extends MelisAbstractActionController
{
    const NEW_MODULE = "new_module";
    const DROPDOWN = "Dropdown";
    const NUMERIC_INPUT = "NumericInput";
    const PAGE_INPUT = "PageInput";
      
    /**
     * This will render the templating plugin creator tool
     * @return ViewModel
    */     
    public function renderToolAction()
    {
        $view = new ViewModel();

        // Initializing the Templating Plugin creator session container
        $container = new Container('templatingplugincreator');
        $container['melis-templatingplugincreator'] = [];

        //generate new session id, this will be used in creating a temp folder path for plugin thumbnails
        $container->getManager()->regenerateId();

        return $view;
    }

    /**
     * This will render the header of the tool
     * @return ViewModel
    */ 
    public function renderToolHeaderAction()
    {
        $view = new ViewModel();
        return $view;
    }


    /**
     * This will render the content of the tool
     * @return ViewModel
    */ 
    public function renderToolContentAction()
    {
        $view = new ViewModel();

        /**
         * Checking file permission to file and directories needed to create and activate the plugin
         */
        $filePermissionErr = [];
        if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/../config/melis.module.load.php'))
            $filePermissionErr[] = 'tr_melistemplatingplugincreator_fp_config';

        if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/../module'))
            $filePermissionErr[] = 'tr_melistemplatingplugincreator_fp_module';

        //check if temp-thumbnail directory exists and is writable
        $tempThumbnailDirectory = $this->getTempThumbnailDirectory();    
        if(is_writable($tempThumbnailDirectory)){
            dump('temp-thumbnail is writable');
        }else{
            dump('temp-thumbnail not writable');
        }

        if (file_exists($tempThumbnailDirectory)) {
            $stat = chown($tempThumbnailDirectory, 'www-data');           
        } else {             
            if (mkdir($tempThumbnailDirectory, 0755, true)) {
                $stat = chown($tempThumbnailDirectory, 'www-data'); 
            } else {
                $stat = false;
            }           
        }
        
        if(!$stat){
            $filePermissionErr[] = 'tr_melistemplatingplugincreator_fp_temp_thumbnail';
        }
       

        if (!empty($filePermissionErr)){
            $view->fPErr = $filePermissionErr;
            return $view;
        }

        //get the tool steps defined in the config          
        $view->toolSteps = $this->getStepConfig();
        return $view;
    }


     /**
     * This method render the steps of the tool
     * this will call dynamically the requested step 
     * @return ViewModel
     */
    public function renderTemplatingPluginCreatorStepsAction()
    {  
        $view = new ViewModel();

        // The steps requested
        $curStep = $this->params()->fromPost('curStep', 1);
        $nextStep = $this->params()->fromPost('nextStep', 1);
        $validate = $this->params()->fromPost('validate', false);

        // Current viewModel
        $viewStep = new ViewModel();
        $viewStep->setTemplate('melis-templating-plugin-creator/render-form');
        $viewRender = $this->getServiceManager()->get('ViewRenderer');

        /**
         * This will validate the current step if 'next' button is clicked, and get the form/data of the next step if no errors in validation,
         * else, it will just get the current step form
         * 
         */
        $stepFunction = 'processStep'.$curStep;
        $viewStep = $this->$stepFunction($viewStep, $nextStep, $validate); 
        $viewStep->id = 'melistemplatingplugincreator_step'.$nextStep;   
        $viewStep->curStep = $nextStep;
        if ($nextStep) {
            $viewStep->nextStep = $nextStep + 1; 
        }         

        //if 'back' or 'next' step button is triggered
        if ($validate || ($curStep > $nextStep)) {
            // Retrieving steps form config
            $stepsConfig = $this->getStepConfig();           
            $translator = $this->getServiceManager()->get('translator');

            if (!empty($viewStep->errors)) {               
                $results['errors'] = $viewStep->errors;
                $results['textMessage'] = $translator->translate('tr_melistemplatingplugincreator_err_message');
                $results['textTitle'] = $translator->translate($stepsConfig['melistemplatingplugincreator_step'.$curStep]['name']);
            } else {   

                if (isset($viewStep->restartRequired)) {
                    $viewStep->setTemplate('melis-templating-plugin-creator/render-step6-finalization');
                }

                $results = [
                    'textTitle' => $translator->translate($stepsConfig['melistemplatingplugincreator_step'.$curStep]['name']),
                    'textMessage' => isset($viewStep->textMessage)?$viewStep->textMessage:null,
                    'html' => $viewRender->render($viewStep), // Sending the step view without container
                    'restartRequired' => isset($viewStep->restartRequired)?$viewStep->restartRequired:null
                ];
            }

            return new JsonModel($results);
        }
             
        // Rendering the result view and attach to the container   
        $view->step = $viewRender->render($viewStep);
        return $view;
    }

    
    /**
     * This will process step 1 of the tool
     * @param ViewModel $viewStep
     * @param int $nextStep
     * @param boolean $validate
     * @return ViewModel
    */ 
    private function processStep1($viewStep, $nextStep, $validate){
        $container = new Container('templatingplugincreator');//session container
        $curStep = 1;       
        $data = array();
        $errorMessages = array();
        $stepForm = null; 
      
        //validate form if Next button is triggered
        if ($validate) {  
            $request = $this->getRequest();
            $postValues = get_object_vars($request->getPost()); 
            
            //get current step's form and data
            list($stepForm, $data) = $this->getStepFormAndData($curStep);
            
            $stepForm->setData($postValues['step-form']);              
         
            //if plugin destination is new, remove the validation for the existing module and vice versa
            if (!empty($postValues['step-form']['tpc_plugin_destination'])) {
                if ($postValues['step-form']['tpc_plugin_destination'] == self::NEW_MODULE) {
                    $stepForm->getInputFilter()->remove('tpc_existing_module_name');
                } else {
                    $stepForm->getInputFilter()->remove('tpc_new_module_name');
                }
            } else {
                $stepForm->getInputFilter()->remove('tpc_new_module_name');
                $stepForm->getInputFilter()->remove('tpc_existing_module_name');
            }              

            //if current step is valid, save form data to session and get the view of the next step 
            if ($stepForm->isValid()) { 
                //validate new module name entered for duplicates
                if (!empty($postValues['step-form']['tpc_new_module_name'])) {
                    /**
                     * Validating the module entered if it's already existing on the platform
                     */
                    $modulesSvc = $this->getServiceManager()->get('ModulesService');
                    $assetModuleService = $this->getServiceManager()->get('MelisAssetManagerModulesService');
                    $existingModules = array_merge($modulesSvc->getModulePlugins(), \MelisCore\MelisModuleManager::getModules(), $assetModuleService->getSitesModules());
                    $existingModules = array_map('strtolower', $existingModules);
                    $newModuleName = strtolower($postValues['step-form']['tpc_new_module_name']);

                    //set error if the entered module name has duplicate
                    if (in_array(trim($newModuleName), $existingModules)) {                      
                        // Adding error message to form field
                        $translator = $this->getServiceManager()->get('translator');
                        $stepForm->get('tpc_new_module_name')->setMessages([
                            'ModuleExist' => sprintf($translator->translate('tr_melistemplatingplugincreator_err_module_exist'), $postValues['step-form']['tpc_new_module_name'])
                        ]);

                        $errorMessages = $stepForm->getMessages();
                    }
                }
                    
                //validate templating plugin name if it already exists for the selected existing module
                if (!empty($postValues['step-form']['tpc_existing_module_name'])) {  
                    $templatingPluginCreatorSrv = $this->getServiceManager()->get('MelisTemplatingPluginCreatorService');
                    $existingModuleName = trim($postValues['step-form']['tpc_existing_module_name']);
                    $newPluginName = trim($postValues['step-form']['tpc_plugin_name']);
                    $pluginKey = strtolower($existingModuleName.$newPluginName.'plugin');
                    
                    //retrieve existing templating plugin names of the selected site module               
                    $siteTemplatingPlugins = $templatingPluginCreatorSrv->getSiteTemplatingPluginNames($existingModuleName);
                    $siteTemplatingPlugins = array_map('strtolower', $siteTemplatingPlugins);   
                    
                    //if has duplicates, add error message to form field
                    if ($siteTemplatingPlugins && in_array($pluginKey, $siteTemplatingPlugins)) {                       
                        $translator = $this->getServiceManager()->get('translator');
                        $stepForm->get('tpc_plugin_name')->setMessages([
                            'PluginExist' => sprintf($translator->translate('tr_melistemplatingplugincreator_err_plugin_name_exist'), $postValues['step-form']['tpc_plugin_name'])
                        ]);

                        //adding a variable to viewmodel to flag an error
                       $errorMessages = $stepForm->getMessages();                        
                    }          
                }

                //if current step form is valid, save form data to session and get the next step's form 
                if(empty($errorMessages)){
                    //save to session   
                    $container['melis-templatingplugincreator']['step_1'] = $stepForm->getData(); 
                                                  
                    //get next step's form and data
                    list($stepForm,$data) = $this->getStepFormAndData($nextStep);
                }       

            }else{     
                $errorMessages = $stepForm->getMessages();               
            }

            //format error labels
            if ($errorMessages) {
                foreach ($errorMessages as $keyError => $valueError)
                {
                    foreach ($stepForm->getElements() as $keyForm => $valueForm)
                    {
                        $elementName = $valueForm->getAttribute('name');
                        $elementLabel = $valueForm->getLabel();              

                        if ($elementName == $keyError &&
                            !empty($elementLabel))
                            $errorMessages[$keyError]['label'] = $elementLabel;
                    }
                }               
            }
        } else {
            list($stepForm, $data) = $this->getStepFormAndData($nextStep);          
        }
           
        $viewStep->stepForm = $stepForm;//the form to be displayed
        $viewStep->errors = $errorMessages;
        $viewStep->data = $data;
        return $viewStep;
    }
    
    
    /**
     * This will process step 2 of the tool
     * @param ViewModel $viewStep
     * @param int $nextStep
     * @param boolean $validate
     * @return ViewModel
    */ 
    private function processStep2($viewStep, $nextStep, $validate){   
        $container = new Container('templatingplugincreator');//session container
        $sessionID = $container->getManager()->getId(); 
        $curStep = 2;       
        $data = array();
        $errors = array();
        $languageFormErrorMessages = array();
        $uploadFormErrorMessages = array();
        $textMessage = '';
        $stepForm = null; 
        $isValid = 0;
        $isValid2ndForm = 0;
        $pluginThumbnail = null;

        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');   

        //validate form if Next button is triggered
        if ($validate) {         
            $request = $this->getRequest();
            $postValues = get_object_vars($request->getPost()); 
            $uploadedFile = $request->getFiles()->toArray();

            //merge upload data with the other posted values
            if (!empty($uploadedFile)) {
                $postValues = array_merge_recursive($postValues,$uploadedFile);               
            }

            //validate language form
            list($isValidLanguageForm, $languageFormErrorMessages) = $this->validateMultiLanguageForm($curStep, $postValues);  

            //validate upload form
            $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step2_form2', 'melistemplatingplugincreator_step2_form2');
            $stepForm2 = $factory->createForm($appConfigForm);         
            $stepForm2->setData($postValues['step-form']);

            if ($stepForm2->isValid()) {        
                //get the saved thumbnail done via ajax
                $pluginThumbnail = !empty($container['melis-templatingplugincreator']['step_2']['plugin_thumbnail'])?$container['melis-templatingplugincreator']['step_2']['plugin_thumbnail']:null;

                //if no saved uploaded thumbnail, try to upload again
                if(empty($pluginThumbnail)){

                    //process uploading of thumbnail
                    list($isValid2ndForm, $pluginThumbnail, $textMessage) = $this->uploadPluginThumbnail($postValues['step-form']['tpc_plugin_upload_thumbnail']);

                    if ($isValid2ndForm) {
                        //save to session   
                        $container['melis-templatingplugincreator']['step_2']['plugin_thumbnail'] = '/Melistemplatingplugincreator/temp-thumbnail/'.$sessionID.'/'.pathinfo($pluginThumbnail, PATHINFO_FILENAME).'.'.pathinfo($pluginThumbnail, PATHINFO_EXTENSION); 

                    } else {         
                        //unset previously uploaded file
                        if (!empty($container['melis-templatingplugincreator']['step_2']['plugin_thumbnail'])) {
                            unset($container['melis-templatingplugincreator']['step_2']['plugin_thumbnail']); 
                        }                
              
                        $stepForm2->get('tpc_plugin_upload_thumbnail')->setMessages([
                            'pluginError' => $textMessage,
                            'label' => 'Plugin thumbnail'
                        ]);

                        $uploadFormErrorMessages = $this->formatErrors($stepForm2->getMessages(), $stepForm2->getElements());   
                    }  
                } else {
                    $isValid2ndForm = 1;
                }

            } else {    
                $uploadFormErrorMessages = $this->formatErrors($stepForm2->getMessages(), $stepForm2->getElements());
            }    
             
            //check if the forms for the current step are all valid
            if ($isValidLanguageForm && $isValid2ndForm) {
                $isValid = 1;
            } else {
                //merge language and upload form errors 
                $errors = ArrayUtils::merge($languageFormErrorMessages, $uploadFormErrorMessages);                
            }      

            //get next step's form
            if ($isValid) {                
                list($stepForm, $data) = $this->getStepFormAndData($nextStep);
            } 
        } else { 
            list($stepForm, $data) = $this->getStepFormAndData($nextStep); 
        }

        $viewStep->stepForm = $stepForm;//the form to be displayed
        $viewStep->errors = $errors;
        $viewStep->data = $data;
        return $viewStep;
    }
    
    /**
     * This will process step 3 of the tool
     * @param ViewModel $viewStep
     * @param int $nextStep
     * @param boolean $validate
     * @return ViewModel
    */ 
    private function processStep3($viewStep, $nextStep, $validate){       
        $container = new Container('templatingplugincreator');//session container
        $curStep = 3;       
        $data = array();
        $errors = array();
        $fieldFormErrorMessages = array();
        $mainFormErrorMessages = array();
        $stepForm = null; 
        $isValid = 0;
        $isValidMainForm = 0;
        $isValidFieldForm = 0;

        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');   

        //validate form if Next button is triggered
        if ($validate) {              
            $request = $this->getRequest();
            $postValues = get_object_vars($request->getPost()); 

            //validate the step 3's main form
            $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step3_form1', 'melistemplatingplugincreator_step3_form1');
            $stepForm = $factory->createForm($appConfigForm);         
            $stepForm->setData($postValues['step-form']);

            if ($stepForm->isValid()) {
                $isValidMainForm = 1;
                $container['melis-templatingplugincreator']['step_3']['main_form'] = $stepForm->getData();  
            } else {
                $mainFormErrorMessages = $this->formatErrors($stepForm->getMessages(), $stepForm->getElements());
            }
                      
            //validate field form 
            list($isValidFieldForm, $fieldFormErrorMessages) = $this->validateFieldForm($curStep, $postValues); 
          
            //get next step's form if all forms are valid
            if ($isValidMainForm && $isValidFieldForm) {
                list($stepForm, $data) = $this->getStepFormAndData($nextStep);
            }else{
                //merge language and upload form errors 
                $errors = ArrayUtils::merge($mainFormErrorMessages, $fieldFormErrorMessages);  
            }

        } else { 
            list($stepForm, $data) = $this->getStepFormAndData($nextStep); 
        }

        $viewStep->stepForm = $stepForm;
        $viewStep->errors = $errors;
        $viewStep->data = $data;
        return $viewStep;
    }

    /**
     * This will process step 4 of the tool
     * @param ViewModel $viewStep
     * @param int $nextStep
     * @param boolean $validate
     * @return ViewModel
    */ 
    private function processStep4($viewStep, $nextStep, $validate){   
        $container = new Container('templatingplugincreator');//session container
        $sessionID = $container->getManager()->getId(); 
        $curStep = 4;       
        $data = array();
        $errors = array();       
        $textMessage = '';
        $stepForm = null; 
        $isValid = 0;        

        //validate form if Next button is triggered
        if ($validate) {         
            $request = $this->getRequest();
            $postValues = get_object_vars($request->getPost()); 
                
            //validate translation form
            list($isValid, $errors) = $this->validateFieldTranslations($curStep, $postValues);  

            //get next step's form
            if ($isValid) {                
                list($stepForm, $data) = $this->getStepFormAndData($nextStep);
            }

        } else { 
            list($stepForm, $data) = $this->getStepFormAndData($nextStep); 
        }

        $viewStep->stepForm = $stepForm;//the form to be displayed
        $viewStep->errors = $errors;
        $viewStep->data = $data;
        return $viewStep;
    }



    /**
     * This will process step 5 of the tool
     * @param ViewModel $viewStep
     * @param int $nextStep
     * @param boolean $validate
     * @return ViewModel
    */  
    private function processStep5($viewStep, $nextStep, $validate){           
        $data = array();        
        $stepForm = null;   

        list($stepForm, $data) = $this->getStepFormAndData($nextStep); 

        $viewStep->stepForm = $stepForm;//the form to be displayed       
        $viewStep->data = $data;
        return $viewStep;
    }


    /**
     * This will process step 5 of the tool
     * @param ViewModel $viewStep
     * @param int $nextStep
     * @param boolean $validate
     * @return ViewModel
    */
    private function processStep6($viewStep, $nextStep, $validate){
        $container = new Container('templatingplugincreator');//session container             
        $data = array();       
        $errors = array();
        $stepForm = null; 
      
        //generate templating plugin
        if ($validate) { 
            $request = $this->getRequest();
            $postValues = get_object_vars($request->getPost());          
            $isActivatePlugin = !empty($postValues['step-form']['tpc_activate_plugin'])?1:0;

            //if destination of the plugin is the new module, create first the new module before adding the templating plugin files
            if ($container['melis-templatingplugincreator']['step_1']['tpc_plugin_destination'] == self::NEW_MODULE) {              
                // Initializing the Tool creator session container
                $toolContainer = new Container('melistoolcreator');
                $toolContainer['melis-toolcreator'] = [];    
                $toolContainer['melis-toolcreator']['step1']['tcf-name'] = $container['melis-templatingplugincreator']['step_1']['tpc_new_module_name'];
                $toolContainer['melis-toolcreator']['step1']['tcf-tool-type'] = 'blank';
                $toolContainer['melis-toolcreator']['step1']['tcf-tool-edit-type'] = 'modal';
        
                $toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
                $toolCreatorSrv->createTool();             
            }
       
            //call service to generate the templating plugin 
            $tpcService = $this->getServiceManager()->get('MelisTemplatingPluginCreatorService');
            $result = $tpcService->generateTemplatingPlugin();

            if ($result) {
                if ($container['melis-templatingplugincreator']['step_1']['tpc_plugin_destination'] == self::NEW_MODULE) {
                    // Activate new module
                    $moduleSvc = $this->getServiceManager()->get('ModulesService');
                    $moduleSvc->activateModule($toolCreatorSrv->moduleName());

                    // Reloading module paths
                    unlink($_SERVER['DOCUMENT_ROOT'].'/../config/melis.modules.path.php');

                    //unset tool container
                    unset($toolContainer['melis-toolcreator']);  
                }
               
                //reload page to activate the plugin
                if ($isActivatePlugin) {   
                    $viewStep->restartRequired = 1;
                } else {      
                    $viewStep->restartRequired = 0;
                }   

            } else {
                //set errors
                $translator = $this->getServiceManager()->get('translator');
                $viewStep->textMessage = $translator->translate('tr_melistemplatingplugincreator_generate_plugin_error_encountered');
            }    
        }
           
        list($stepForm, $data) = $this->getStepFormAndData($nextStep);              
        $viewStep->stepForm = $stepForm;//the form to be displayed       
        $viewStep->data = $data;
        return $viewStep;            
    }


    /**
     * This will retrieve the available forms and data for the given step
     * @param int $curStep
     * @param bool $validate
     * @return array
    */
    private function getStepFormAndData($curStep, $validate = false){
        $container = new Container('templatingplugincreator');//session container

        //get the current step's form config
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');          
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements); 
        $data = array();
        $errors = array();
        $stepForm = null;
        $stepFormArr = array();
                  
        switch ($curStep) {
            case 1:      
                $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step1_form', 'melistemplatingplugincreator_step1_form');                               
                $stepForm = $factory->createForm($appConfigForm);                 
               
                //check if there is a session data
                if(!empty($container['melis-templatingplugincreator']['step_1'])){                 
                    $stepForm->setData($container['melis-templatingplugincreator']['step_1']);  
                }   
                break;

            case 2:  
                list($stepFormArr, $data['languages']) = $this->getLanguageForms($curStep);            
                
                //get the 2nd form
                $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step2_form2', 'melistemplatingplugincreator_step2_form2'); 
                $stepForm2 = $factory->createForm($appConfigForm);                              

                //check if there is a session data
                if (!empty($container['melis-templatingplugincreator']['step_2'])) {
                    $stepForm2->setData($container['melis-templatingplugincreator']['step_2']);
                }            
                $stepFormArr['form2'] = $stepForm2;
                
                //get the thumbnail saved in session
                if (!empty($container['melis-templatingplugincreator']['step_2']['plugin_thumbnail'])) {                    
                    $data['thumbnail'] = $container['melis-templatingplugincreator']['step_2']['plugin_thumbnail'];
                }   

                //get the current locale used from meliscore session
                $container = new Container('meliscore');
                $data['lang_locale'] = $container['melis-lang-locale'];                         
            
                $stepForm = $stepFormArr;
                break;

            case 3: 
                //get the step 3 main form
                $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step3_form1', 'melistemplatingplugincreator_step3_form1'); 
                $stepForm = $factory->createForm($appConfigForm);                
                $stepFormArr['form1'] = $stepForm; 
             
                //if main form is already set, get the dynamic field forms
                if (!empty($container['melis-templatingplugincreator']['step_3']['main_form'])) {
                    //set the main form's session data to form
                    $stepForm->setData($container['melis-templatingplugincreator']['step_3']['main_form']);    
                    $tabNum = 1;//main properties tab is set as default to 1
                    $stepFormArr['fieldForm'] = $this->getFieldForms($curStep, $tabNum, $container['melis-templatingplugincreator']['step_3']['main_form']['tpc_main_property_field_count']); 
                }
                                         
                $stepForm = $stepFormArr; 
                break;

            case 4:  
                list($stepForm, $data['languages']) = $this->getMainPropertiesTranslationForm($curStep); 
                break;

            case 5:
                //get all the saved values from step 1 to 4
                $container = new Container('templatingplugincreator');//session container
                $data['sessionData'] = $container['melis-templatingplugincreator'];
                
                // get all languages available in the plaftform
                $coreLang = $this->getServiceManager()->get('MelisCoreTableLang');
                $data['languages'] = $coreLang->fetchAll()->toArray();

                //get all steps
                $data['steps'] = $this->getStepConfig();
                break;

            case 6:
               $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step6_form', 'melistemplatingplugincreator_step6_form');                               
                $stepForm = $factory->createForm($appConfigForm); 
                break;               
        }     

        return array($stepForm, $data);  
    }
   
    /**
     * This retrieves the language forms and the list of languages available in the platform
     * @param int $step
     * @return array
    */
    private function getLanguageForms($step)
    {
        $container = new Container('templatingplugincreator');       

        //get the language form set for the given step
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');          
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements); 
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step'.$step.'_form1', 'melistemplatingplugincreator_step'.$step.'_form1'); 

        //get all languages
        $languages = $this->getOrderedLanguagesByCurrentLocale();

        // Generate form for each language
        foreach ($languages As $key => $lang) {           
            $stepFormtmp = $factory->createForm($appConfigForm);
        
            if (!empty($container['melis-templatingplugincreator']['step_'.$step][$lang['lang_locale']])){
                $stepFormtmp->setData($container['melis-templatingplugincreator']['step_'.$step][$lang['lang_locale']]);
            }                                   

            //set value of the hidden field 'tcp_lang_local'
            $stepFormtmp->get('tpc_lang_local')->setValue($lang['lang_locale']);

            // Adding language form
            $stepFormArr['languageForm'][$lang['lang_locale']] = $stepFormtmp;

            // Language label
            $languages[$key]['lang_label'] = $this->langLabel($lang['lang_locale'], $lang['lang_name']);
        }       
        
        return array($stepFormArr, $languages);
    }

    /**
     * This retrieves the list of steps of the tool from the config
     * @return array
    */
    private function getStepConfig()
    {
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
        $toolSteps = $melisCoreConfig->getItem('melistemplatingplugincreator/datas/steps'); 
        return $toolSteps;
    }

    /**
     * This method return the language name with flag image
     * if exist
     *
     * @param $locale
     * @param $langName
     * @return string
     */
    private function langLabel($locale, $langName)
    {        
        $langLabel = '<span>'. $langName .'</span>';

        $lang = explode('_', $locale);
        if (!empty($lang[0])) {

            $moduleSvc = $this->getServiceManager()->get('ModulesService');
            $imgPath = $moduleSvc->getModulePath('MelisCore').'/public/assets/images/lang/'.$lang[0].'.png';

            if (file_exists($imgPath)) 
                $langLabel .= '<span class="pull-right"><img src="/MelisCore/assets/images/lang/'.$lang[0].'.png"></span>';
            else
                $langLabel .= '<span style="border: 1px solid #fff;padding: 4px 4px;line-height: 10px;float: right;margin: 5px;">'. strtoupper($lang[0]) .'</span>';
        }

        return $langLabel;
    }

     /**
     * This validates the language forms for the given step
     * @param int $curStep
     * @param array $formData
     * @return array
    */
    private function validateMultiLanguageForm($curStep, $formData)
    {
        $container = new Container('templatingplugincreator');//to store the session data
        $translator = $this->getServiceManager()->get('translator');
        // Meliscore languages
        $coreLang = $this->getServiceManager()->get('MelisCoreTableLang');
        $languages = $coreLang->fetchAll()->toArray();
        $languageCount = count($languages);

        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');          
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);       
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step'.$curStep.'_form1', 'melistemplatingplugincreator_step'.$curStep.'_form1');  
        $validFormCount = 0;
        $isValid = 0;        
        $errors = array();

        foreach ($languages as $lang) {
            // Generating form for each language
            $stepFormtmp = $factory->createForm($appConfigForm);
            
            //loop through the language form data
            $ctr = 1;
            foreach ($formData['step-form'] As $val) {               
                if ($val['tpc_lang_local'] && $val['tpc_lang_local'] == $lang['lang_locale']) {                  
                    $stepFormtmp->setData($val);                    
                }  

                $ctr++;

                if ($ctr > $languageCount) {                    
                    break;
                }              
            }
          
            if ($stepFormtmp->isValid()) {   
                $validFormCount++;   

            } else {          
                $errors = ArrayUtils::merge($errors, $stepFormtmp->getMessages());              
            }   

            //add to session the posted values
            $container['melis-templatingplugincreator']['step_'.$curStep][$lang['lang_locale']] = $stepFormtmp->getData();

        }//end foreach

        //if at least 1 form is valid, flag as valid
        if ($validFormCount) {            
            $isValid = 1;
            $errors = array();
        } else {           
            $errors = $this->formatErrors($errors, $stepFormtmp->getElements());
        }       

        return array($isValid, $errors);
    }

    /**
     * This creates a directory if not yet existing
     * @param string $path
     * @return boolean
    */
    private function createFolder($path)
    {  
        if (file_exists($path)) {          
            chmod($path, 0777);
            $status = true;
        } else {            
            $status = mkdir($path, 0777, true);           
        }
        return $status;             
    }

    /**
     * This uploads the plugin thumbnail to the temp folder
     * @param array $uploadedFile
     * @return array
    */
    private function uploadPluginThumbnail($uploadedFile)
    {        
        $container = new Container('templatingplugincreator');
        $_FILES = array($uploadedFile);

        try {
            $tool =   $this->getServiceManager()->get('MelisCoreTool'); 
            $thumbnailTempPath = $this->getTempThumbnailDirectory();    
            //append session id to temp-thumbnail path
            $sessionID = $container->getManager()->getId(); 
            $thumbnailTempPath = $thumbnailTempPath.$sessionID.'/';
            $upload = false;
            $textMessage = '';
            $fileName = '';

            $imageValidator = new IsImage([
                'messages' => [
                    'fileIsImageFalseType' => $tool->getTranslation('tr_melistemplatingplugincreator_save_upload_image_imageFalseType'),
                    'fileIsImageNotDetected' => $tool->getTranslation('tr_melistemplatingplugincreator_save_upload_image_imageNotDetected'),
                    'fileIsImageNotReadable' => $tool->getTranslation('tr_melistemplatingplugincreator_save_upload_image_imageNotReadable'),
                ],
            ]);           

            if (!empty($uploadedFile['name'])) { 
                if ($this->createFolder($thumbnailTempPath)) {    
                    //call templating plugin creator service 
                    $tpcService = $this->getServiceManager()->get('MelisTemplatingPluginCreatorService');
                    //clean filename by removing accents and spaces
                    $fileName = $tpcService->removeAccents(str_replace(' ','_',trim($uploadedFile['name'])));

                    /** validate image  */
                    if (!empty($uploadedFile['tmp_name'])) {

                        //check if file is image
                        $sourceImg = @imagecreatefromstring(@file_get_contents($uploadedFile['tmp_name']));
                        if ($sourceImg === false) {                          
                            return array($upload, $fileName, $tool->getTranslation('tr_melistemplatingplugincreator_save_upload_image_imageFalseType'));
                        }

                        //retrieve the size limit from the configuration               
                        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
                        $pluginThumbnailConfig = $melisCoreConfig->getItem('melistemplatingplugincreator/datas/plugin_thumbnail');
                        $minSize = $pluginThumbnailConfig['min_size'];
                        $maxSize = $pluginThumbnailConfig['max_size'];
                    
                        // set size validator
                        $sizeValidator = new Size([
                            'min' => $minSize,              
                            'max' => $maxSize,
                        ]);

                        //check if size does not exceed the limit set   
                        if (!$sizeValidator->isValid($uploadedFile['tmp_name'])) {                           
                            return array($upload, $fileName, $tool->getTranslation('tr_melistemplatingplugincreator_upload_too_big', array($this->formatBytes($maxSize)))   );
                        }
                    }

                    $adapter = new Http();     
                    $validator = array($imageValidator);  
                    $adapter->setValidators($validator, $fileName);

                    if ($adapter->isValid()) {
                        $adapter->setDestination($thumbnailTempPath);
                        //adds file directory to filename      
                        $fileName = $thumbnailTempPath .'/'. $fileName;          
                                                   
                        $adapter->addFilter('Laminas\Filter\File\Rename', [
                            'target' => $fileName,
                            'overwrite' => true,
                        ]);

                        // if uploaded successfully
                        if ($adapter->receive()) {
                            $upload = true;                            
                        } else {                          
                            $textMessage = $tool->getTranslation('tr_melistemplatingplugincreator_save_upload_error_encounter');
                        }
                    } else {   
                        foreach ($adapter->getMessages() as $message) {
                            $textMessage = $message;
                        }
                    }
                } else {
                    $textMessage = $tool->getTranslation('tr_melistemplatingplugincreator_save_upload_file_path_rights_error');
                }
            } else {
                $textMessage = $tool->getTranslation('tr_melistemplatingplugincreator_save_upload_empty_file');
            }

            return array($upload, $fileName, $textMessage);         

        }catch (\Exception $ex){
            exit($ex->getMessage());
        }
    }

    /**
     * This processes the uploading of the plugin thumbnail     
     * @return Laminas\View\Model\JsonModel
    */
    public function processUploadAction()
    {   
        $container = new Container('templatingplugincreator');//session container
        $sessionID = $container->getManager()->getId();    
        $errors = array();       
        $uploadFormErrorMessages = array();
        $textMessage = '';    
        $isValid2ndForm = 0;
        $pluginThumbnail = null;

        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');   
       
        $request = $this->getRequest();       
        $uploadedFile = $request->getFiles()->toArray();

        //validate upload form
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step2_form2', 'melistemplatingplugincreator_step2_form2');
        $stepForm2 = $factory->createForm($appConfigForm);         
        $stepForm2->setData($uploadedFile['tpc_plugin_upload_thumbnail']);

        if ($stepForm2->isValid()) {  
            //process uploading of thumbnail
            list($isValid2ndForm, $pluginThumbnail, $textMessage) = $this->uploadPluginThumbnail($uploadedFile['tpc_plugin_upload_thumbnail']);

            if ($isValid2ndForm) {
                //save to session   
                $container['melis-templatingplugincreator']['step_2']['plugin_thumbnail'] = '/MelisTemplatingPluginCreator/temp-thumbnail/'.$sessionID.'/'.pathinfo($pluginThumbnail, PATHINFO_FILENAME).'.'.pathinfo($pluginThumbnail, PATHINFO_EXTENSION); 

            } else {         
                //unset previously uploaded file
                if (!empty($container['melis-templatingplugincreator']['step_2']['plugin_thumbnail'])) {
                    unset($container['melis-templatingplugincreator']['step_2']['plugin_thumbnail']); 
                }                
      
                $stepForm2->get('tpc_plugin_upload_thumbnail')->setMessages([
                    'pluginError' => $textMessage,
                    'label' => 'Plugin thumbnail'
                ]);

                $uploadFormErrorMessages = $this->formatErrors($stepForm2->getMessages(), $stepForm2->getElements());   
            }  

        } else {           
            $uploadFormErrorMessages = $this->formatErrors($stepForm2->getMessages(), $stepForm2->getElements());
        }           

        // Retrieving steps form config
        $stepsConfig = $this->getStepConfig();
        $translator = $this->getServiceManager()->get('translator');

        $results = array(
            'success' => $isValid2ndForm,
            'errors' => $uploadFormErrorMessages,
            'pluginThumbnail' => !empty($container['melis-templatingplugincreator']['step_2']['plugin_thumbnail'])?$container['melis-templatingplugincreator']['step_2']['plugin_thumbnail']:null,
            'textTitle' => $translator->translate($stepsConfig['melistemplatingplugincreator_step2']['name']),
            'textMessage' => $textMessage
        );

        return new JsonModel($results);
    }

    /**
     * This method removes the temp thumbnail directory for the current session
     * @return boolean
    */
    public function removeTempThumbnailDirAction()
    {           
        $container = new Container('templatingplugincreator');
        
        if (!empty($container['melis-templatingplugincreator'])) {       
            //call templating plugin creator service 
            $tpcService = $this->getServiceManager()->get('MelisTemplatingPluginCreatorService');

            //get the temp directory that stored the uploaded plugin thumbnails        
            $tempPath = pathinfo($tpcService->getTempThumbnail(), PATHINFO_DIRNAME);  
            if ($tempPath) {               
                //remove temp thumbnail directory 
               $tpcService->removeDir($tempPath); 
            }  
        }
        return true;
    }


    /**
     * ref: MelisComDocumentController
     * this will format the size of the uploaded file into kb
     * @param byte unit
     * @return kb unit
    */
    private function formatBytes($bytes) {
        $size = $bytes;
        $units = array( 'B', 'Ko', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return round(number_format($size / pow(1024, $power), 2, '.', ',')) .  $units[$power];
    }


    /**
     * This method will delete the uploaded plugin thumbnail from the session data
     * @return boolean
    */
    public function removePluginThumbnailAction()
    {   
        // Initializing the Templating Plugin creator session container
        $container = new Container('templatingplugincreator');
        
        if (!empty($container['melis-templatingplugincreator'])) { 
            if (!empty($container['melis-templatingplugincreator']['step_2']['plugin_thumbnail'])) {
                unset($container['melis-templatingplugincreator']['step_2']['plugin_thumbnail']);
            }          
        }
          
        $results = array(
            'success' => 1       
        );

        return new JsonModel($results);
    }


    /**
     * This will dynamically get the field forms based on the given number of fields
     * @return array
    */
    public function getFieldFormAction(){
        $container = new Container('templatingplugincreator');//to store the session data
        $translator = $this->getServiceManager()->get('translator');
      
        $request = $this->getRequest();
        $postValues = get_object_vars($request->getPost()); 
        $fieldCount = $postValues['fieldCount'];
        $curStep = $postValues['curStep'];
        $tab =  $postValues['tab'];//reserved for 'Tab Properties' tab for the next version

        $view = new ViewModel();
        $view->setTemplate('melis-templating-plugin-creator/partial/field-form');
        $view->fieldFormArr = $this->getFieldForms($curStep, $tab, $fieldCount);
        return $view;
    }

    /**
     * Retrieve the forms for each field
     * @param curstep
     * @param tab number
     * @param field count
     * @return array
    */
    private function getFieldForms($curStep, $tab, $fieldCount){
        $container = new Container('templatingplugincreator');//to store the session data     
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');          
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);       
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step'.$curStep.'_field_form', 'melistemplatingplugincreator_step'.$curStep.'_field_form');  
         $stepFormArr = array();
   
        for ($i=1; $i<=$fieldCount; $i++) {
            // Generating form for each language
            $stepFormtmp = $factory->createForm($appConfigForm);
           
            //set the default value of the first field (template_path)
            if ($i==1) {
                $templatingPluginCreatorSrv = $this->getServiceManager()->get('MelisTemplatingPluginCreatorService');
                $moduleName = $this->getDestinationModule();               
                $newPluginName = $templatingPluginCreatorSrv->convertToViewName($container['melis-templatingplugincreator']['step_1']['tpc_plugin_name']);

                $stepFormtmp->get('tpc_field_name')->setValue('template_path');
                $stepFormtmp->get('tpc_field_display_type')->setValue(self::DROPDOWN);
                $stepFormtmp->get('tpc_field_is_required')->setValue('1');
                $stepFormtmp->get('tpc_field_default_value')->setValue($moduleName.'/plugins/'.$newPluginName); 
                $stepFormtmp->get('tpc_field_default_options')->setValue($moduleName.'/plugins/'.$newPluginName); 

                //set to readonly/disabled
                $stepFormtmp->get('tpc_field_name')->setAttribute('readonly','readonly');
                $stepFormtmp->get('tpc_field_display_type')->setAttribute('disabled',true);
                $stepFormtmp->get('tpc_field_is_required')->setAttribute('disabled',true);
                $stepFormtmp->get('tpc_field_default_value')->setAttribute('readonly','readonly');
                $stepFormtmp->get('tpc_field_default_options')->setAttribute('readonly','readonly');                    
            }

            //hide default_options field
            $stepFormtmp->get('tpc_field_default_options')->setLabelAttributes(array('style' => 'display: none;','color' => 'red'));    
            $stepFormtmp->get('tpc_field_default_options')->setAttributes(array('style' => 'display: none;'));         

            //set data here for each form except for the template_path field since it's predefined
            if (!empty($container['melis-templatingplugincreator']['step_'.$curStep]['tab_'.$tab]['field_'.$i]) && $i!=1) {
                $stepFormtmp->setData($container['melis-templatingplugincreator']['step_'.$curStep]['tab_'.$tab]['field_'.$i]);

                //if the selected display type is Dropdown, add data-role=tagsinput attribute
                if ($stepFormtmp->get('tpc_field_display_type')->getValue() == self::DROPDOWN && $i!=1) {
                    $stepFormtmp->get('tpc_field_default_options')->setAttribute('data-role','tagsinput');

                    //show default_options field
                    $stepFormtmp->get('tpc_field_default_options')->setLabelAttributes(array('style' => 'display: block;'));    
                    $stepFormtmp->get('tpc_field_default_options')->setAttributes(array('style' => 'display: block;'));                    
                }


                //test
                if ($stepFormtmp->get('tpc_field_display_type')->getValue() == 'MelisCoreTinyMCE') {         
                    //dump('display type is tiny mce, value is: '. $container['melis-templatingplugincreator']['step_'.$curStep]['tab_'.$tab]['field_'.$i]['tpc_field_default_value']);           
                    $stepFormtmp->get('tpc_field_default_value')->setValue(htmlentities($container['melis-templatingplugincreator']['step_'.$curStep]['tab_'.$tab]['field_'.$i]['tpc_field_default_value']));                 
                }

            }           

            $stepFormArr[] = $stepFormtmp;
        } 

        return $stepFormArr;
    }


    /**
    * This retrieves the translation forms for the plugin's properties and the list of languages available in the platform
    * @return array
    */
    private function getMainPropertiesTranslationForm(){
        $container = new Container('templatingplugincreator');
   
        //get the language form set for the given step
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');          
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements); 
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step4', 'melistemplatingplugincreator_step4'); 
        
        //get all available languages 
        $languages = $this->getOrderedLanguagesByCurrentLocale();

        // Generate form for each language
        foreach ($languages As $key => $lang) {      
            $fieldCount = $container['melis-templatingplugincreator']['step_3']['main_form']['tpc_main_property_field_count'];
            $tabNumber = $container['melis-templatingplugincreator']['step_3']['main_form']['tpc_property_tab_number'];
            $tabCount = 1;//default to 1 for now

            for ($t=1; $t<=$tabCount; $t++) {
                for ($i=1; $i<=$fieldCount; $i++) {
                    $stepFormtmp = $factory->createForm($appConfigForm);        
             
                    //set value of the hidden fields
                    $stepFormtmp->get('tpc_lang_local')->setValue($lang['lang_locale']);
                    $stepFormtmp->get('tpc_tab_num')->setValue($t);
                    $stepFormtmp->get('tpc_field_num')->setValue($i);
                    $stepFormtmp->get('tpc_field_name')->setValue($container['melis-templatingplugincreator']['step_3']['tab_'.$t]['field_'.$i]['tpc_field_name']);    
           
                    //set default label for template_path field
                    if ($i==1) {
                        $stepFormtmp->get('tpc_field_label')->setValue('Template');
                    }                        
            
                    $fieldDisplayType = $container['melis-templatingplugincreator']['step_3']['tab_'.$t]['field_'.$i]['tpc_field_display_type'];
                    //$fieldDefaultValues = $container['melis-templatingplugincreator']['step_3']['tab_'.$t]['field_'.$i]['tpc_field_default_value'];
                    $fieldDefaultOptions = $container['melis-templatingplugincreator']['step_3']['tab_'.$t]['field_'.$i]['tpc_field_default_options'];
                   
                    //if field dislay type is dropdown, and default values are given in step 3, add translation fields for each dropdown values                
                    if ($fieldDisplayType == self::DROPDOWN && !empty($fieldDefaultOptions)) {
                       $stepFormtmp = $this->setDropdownValueTranslation($i, $stepFormtmp, $fieldDefaultOptions);                 
                    }

                    //set session data to form if there are any
                    if (!empty($container['melis-templatingplugincreator']['step_4'][$lang['lang_locale']]['tab_'.$t]['field_'.$i])) {
                        $stepFormtmp->setData($container['melis-templatingplugincreator']['step_4'][$lang['lang_locale']]['tab_'.$t]['field_'.$i]);
                    }  

                    // Adding translation form for each field
                    $stepFormArr['mainPropertyTranslationForm'][$lang['lang_locale']]['tab_'.$t]['field_'.$i] = $stepFormtmp;
                } 
            }
                           

            // Language label
            $languages[$key]['lang_label'] = $this->langLabel($lang['lang_locale'], $lang['lang_name']);
        }       
        
        return array($stepFormArr, $languages);
    }

    /**
    * This retrieves the list of languages available in the platform where the current locale used is the first in the list
    * @return array
    */
    private function getOrderedLanguagesByCurrentLocale(){
        //get all languages available in the plaftform
        $coreLang = $this->getServiceManager()->get('MelisCoreTableLang');
        $languages = $coreLang->fetchAll()->toArray();

        //get the current locale used from meliscore session
        $melisCoreContainer = new Container('meliscore');
        $locale = $melisCoreContainer['melis-lang-locale']; 

        //set the current locale as the first value in the array
        foreach ($languages as $key => $langData) {
            if (trim($langData["lang_locale"]) == trim($locale)) {
                unset($languages[$key]);
                array_unshift($languages,$langData);
            }
        }

        return $languages;
    }

    /**
    * This will add translation fields to each dropdown values
    * @param $fieldNum
    * @param $stepFormtmp
    * @param $fieldDefaultOptions
    * @return Form
    */
    private function setDropdownValueTranslation($fieldNum, $stepFormtmp, $fieldDefaultOptions){
        $container = new Container('templatingplugincreator');//to store the session data
        $translator = $this->getServiceManager()->get('translator');
        $inputFilter = $stepFormtmp->getInputFilter();
        $templatingPluginCreatorSrv = $this->getServiceManager()->get('MelisTemplatingPluginCreatorService');
      
        //explode fieldDefaultOptions by comma
        $explode = explode(',',$fieldDefaultOptions);

        foreach ($explode as $value) {
            $element = new \Laminas\Form\Element\Text($templatingPluginCreatorSrv->removeNonAlphaNumeric($value)."_label");
            $element->setLabel(sprintf($translator->translate('tr_melistemplatingplugincreator_tpc_dropdown_value_label'), $value));            
            $element->setAttributes([
                'class' => 'form-control',
                'required' => 'required']
            );
            $element->setOptions([
                'tooltip' => $translator->translate('tr_melistemplatingplugincreator_tpc_dropdown_value_label tooltip'),                            
            ]);

            //set default value of the label for the field number 1 [template/[path]]
            if ($fieldNum == 1) {                
                $moduleName = $this->getDestinationModule();
                $newPluginName = $templatingPluginCreatorSrv->convertToViewName($container['melis-templatingplugincreator']['step_1']['tpc_plugin_name']);

                $element->setValue($moduleName.'/plugins/'.$newPluginName);
                $element->setAttribute('readonly','readonly');
            }          
                   
            $stepFormtmp->add($element);
                        
            //add validator    
            $inputFilter->add([
                'name' => $templatingPluginCreatorSrv->removeNonAlphaNumeric($value)."_label",               
                'validators' => [
                    [
                      'name' => 'NotEmpty',
                        'options' => [
                            'messages' => [
                                \Laminas\Validator\NotEmpty::IS_EMPTY => $translator->translate('tr_melistemplatingplugincreator_err_empty'),
                            ],
                        ],

                    ]
                ],
            ]);
        }

        $stepFormtmp->setInputFilter($inputFilter); 
        return $stepFormtmp;
    }


    /**
     * This validates the language forms for the given step
     * @param int $curStep
     * @param array $formData
     * @return array
    */
    private function validateFieldTranslations($curStep, $formData){
        $container = new Container('templatingplugincreator');//to store the session data
        $translator = $this->getServiceManager()->get('translator');
        // Meliscore languages
        $coreLang = $this->getServiceManager()->get('MelisCoreTableLang');
        $languages = $coreLang->fetchAll()->toArray();
        $languageCount = count($languages);

        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');          
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);       
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step4', 'melistemplatingplugincreator_step4');  
        $invalidTranslationFormCount = 0;
        $isValid = 0;    
        $translationFormErrors = array();
        $validTranslationFormCount = array();

        // Generate form for each language and field
        foreach ($languages As $key => $lang) {    
            foreach ($formData['step-form'] as $fieldKey => $val) { 
                $stepFormtmp = $factory->createForm($appConfigForm);
                $tabNumber = $val['tpc_tab_num'];
                $fieldNumber = $val['tpc_field_num'];                
                $fieldDisplayType = $container['melis-templatingplugincreator']['step_3']['tab_'.$tabNumber]['field_'.$fieldNumber]['tpc_field_display_type'];
                // $fieldDefaultValues = $container['melis-templatingplugincreator']['step_3']['tab_'.$tabNumber]['field_'.$fieldNumber]['tpc_field_default_value'];
                $fieldDefaultValueOptions = $container['melis-templatingplugincreator']['step_3']['tab_'.$tabNumber]['field_'.$fieldNumber]['tpc_field_default_options'];
                
                //if field dislay type is dropdown, and default values are given in step 3, add translation field for each dropdown value
                if ($fieldDisplayType == self::DROPDOWN && !empty($fieldDefaultValueOptions)) {                  
                    $stepFormtmp = $this->setDropdownValueTranslation($fieldNumber, $stepFormtmp, $fieldDefaultValueOptions);
                }

                if ($val['tpc_lang_local'] == $lang['lang_locale']) {  

                    $validTranslationFormCount['tab_'.$tabNumber]['field_'.$fieldNumber] = isset($validTranslationFormCount['tab_'.$tabNumber]['field_'.$fieldNumber])?$validTranslationFormCount['tab_'.$tabNumber]['field_'.$fieldNumber]:0;

                    $stepFormtmp->setData($val); 

                    if ($stepFormtmp->isValid()) {                        
                        //add to session the posted values
                        $container['melis-templatingplugincreator']['step_'.$curStep][$lang['lang_locale']]['tab_'.$tabNumber]['field_'.$fieldNumber] = $stepFormtmp->getData();

                        $validTranslationFormCount['tab_'.$tabNumber]['field_'.$fieldNumber] = $validTranslationFormCount['tab_'.$tabNumber]['field_'.$fieldNumber] + 1;

                        //if same field of another language is set, consider the field as valid 
                        if (!empty($translationFormErrors['tab_'.$tabNumber]['field_'.$fieldNumber])) {
                            unset($translationFormErrors['tab_'.$tabNumber]['field_'.$fieldNumber]);
                            $invalidTranslationFormCount--;
                        }

                    } else {   

                        //if not valid, unset from session if previously saved
                        if(!empty($container['melis-templatingplugincreator']['step_'.$curStep][$lang['lang_locale']]['tab_'.$tabNumber]['field_'.$fieldNumber])) {
                            unset($container['melis-templatingplugincreator']['step_'.$curStep][$lang['lang_locale']]['tab_'.$tabNumber]['field_'.$fieldNumber]);
                        }   
                       
                        if ($validTranslationFormCount['tab_'.$tabNumber]['field_'.$fieldNumber] == 0 && empty($translationFormErrors['tab_'.$tabNumber]['field_'.$fieldNumber])) {
                            $invalidTranslationFormCount++;
                       
                            $translationFormErrors['tab_'.$tabNumber]['field_'.$fieldNumber] = $this->formatErrors($stepFormtmp->getMessages(), $stepFormtmp->getElements());
                        }                                                
                    } 
                }
            }   
        }

        if ($invalidTranslationFormCount == 0) {           
            $isValid = 1;            
            $translationFormErrors = array();
        }       

        return array($isValid, $translationFormErrors);
    }


    /**
     * This validates the field forms for the given step
     * @param int $curStep
     * @param array $formData
     * @return array
    */
    private function validateFieldForm($curStep, $formData){   
        $container = new Container('templatingplugincreator');//to store the session data
        $translator = $this->getServiceManager()->get('translator');

        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');          
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);       
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('melistemplatingplugincreator/forms/melistemplatingplugincreator_step'.$curStep.'_field_form', 'melistemplatingplugincreator_step'.$curStep.'_field_form'); 
        $invalidFieldFormCount = 0;
        $isValid = 1;      
        $fieldFormErrors = array();   
        $fieldCount = $formData['step-form']['tpc_main_property_field_count'];
        $tabNumber = $formData['step-form']['tpc_property_tab_number'];

        for ($i=1; $i<=$fieldCount; $i++) {
            // Generating form for each field
            $stepFormtmp = $factory->createForm($appConfigForm);
            $fieldFormName = $stepFormtmp->getAttribute('name');

            //no validation needed if the field set is template_path only since the values are predefined
            if ($fieldCount == 1) {
                $val['tpc_field_name'] = $formData['step-form']['tpc_field_name'];
                $val['tpc_field_display_type'] = self::DROPDOWN;
                $val['tpc_field_is_required'] = 1;                   
                $val['tpc_field_default_options'] = $formData['step-form']['tpc_field_default_value']; 
                $val['tpc_field_default_value'] = $formData['step-form']['tpc_field_default_value']; 
                               
                //add to session the posted values
                $container['melis-templatingplugincreator']['step_'.$curStep]['tab_'.$tabNumber]['field_'.$i] = $val;

            } else {

                foreach ($formData['step-form'] as $key => $val) {                   
                    if ($key == $i) {                    
                        //handling for 'template_path' field  
                        if ($val['tpc_field_name'] == 'template_path') {
                            $val['tpc_field_display_type'] = !empty($val['tpc_field_display_type'])?$val['tpc_field_display_type']:self::DROPDOWN;
                            $val['tpc_field_is_required'] = !empty($val['tpc_field_is_required'])?$val['tpc_field_is_required']:'1'; 
                        }       

                        //add digit validator if selected display type is NumericInput
                        if (($val['tpc_field_display_type'] == self::NUMERIC_INPUT || $val['tpc_field_display_type'] == self::PAGE_INPUT) && !empty($val['tpc_field_default_value'])) {

                            $inputFilter = $stepFormtmp->getInputFilter();
                            $translator = $this->getServiceManager()->get('translator');
                            //add validator    
                            $inputFilter->add([
                                'name' => 'tpc_field_default_value',               
                                'validators' => [
                                 [
                                        'name' => 'Digits',
                                        'options' => [
                                            'messages' => [
                                                \Laminas\Validator\Digits::NOT_DIGITS => $translator->translate('tr_melistemplatingplugincreator_digits_only'),
                                            ],
                                        ],
                                    ],
                                ],
                            ]);

                            $stepFormtmp->setInputFilter($inputFilter); 
                        }        
                                                
                        $stepFormtmp->setData($val); 

                        if ($stepFormtmp->isValid()) {                                
                            //add to session the posted values
                            $container['melis-templatingplugincreator']['step_'.$curStep]['tab_'.$tabNumber]['field_'.$i] = $stepFormtmp->getData();
                        } else {        
                            $invalidFieldFormCount++;
                            $fieldFormErrors[$fieldFormName.'-'.$i] = $stepFormtmp->getMessages(); 
                        } 

                        break; 

                    } else {
                        continue;
                    } 
                }  
            } 
        }    

        //tag current step as invalid if at least 1 field form has error
        if ($invalidFieldFormCount > 0) {            
            $isValid = 0;
            $fieldFormErrors = $this->formatFieldFormErrors($fieldFormErrors, $stepFormtmp->getElements());            
        }    

        return array($isValid, $fieldFormErrors);
    }


    /**
     * This sets the label for the error messages specific to field forms
     * @param array $errors
     * @param array $formElements
     * @return array
    */
    private function formatFieldFormErrors($errors, $formElements){ 
        $fieldFormErrors = array();

        foreach ($errors as $formKey => $fieldFormError) { 
            foreach ($fieldFormError as $keyError  => $valueError) {
                foreach ($formElements as $keyForm => $valueForm) {
                    $elementName = $valueForm->getAttribute('name');
                    //override form label with the custom one if given
                    $elementLabel = !empty($valueError['label'])?$valueError['label']:$valueForm->getLabel();              

                    if ($elementName == $keyError &&
                        !empty($elementLabel))
                        $fieldFormErrors[$formKey][$keyError]['label'] = $elementLabel;
                        $fieldFormErrors[$formKey][$keyError]['message'] = $valueError;
                }
            }         
        }

        return $fieldFormErrors;
    }


    /**
     * This sets the label for the error messages
     * @param array $errors
     * @param array $formElements
     * @return array
    */
    private function formatErrors($errors, $formElements){  
        foreach ($errors as $keyError => $valueError) {       
            foreach ($formElements as $keyForm => $valueForm) {
                $elementName = $valueForm->getAttribute('name');
                //override form label with the custom one if given
                $elementLabel = !empty($valueError['label'])?$valueError['label']:$valueForm->getLabel();              

                if ($elementName == $keyError &&
                    !empty($elementLabel))
                    $errors[$keyError]['label'] = $elementLabel;
            }
        }
        return $errors;
    }

    /**
     * Retrieves the name of destination module
     * @return string
    */
    private function getDestinationModule(){
        $container = new Container('templatingplugincreator');//to store the session data
        $templatingPluginCreatorSrv = $this->getServiceManager()->get('MelisTemplatingPluginCreatorService');
        $moduleName = ''; 

        if ($container['melis-templatingplugincreator']['step_1']['tpc_plugin_destination'] == self::NEW_MODULE) {
            $moduleName = $templatingPluginCreatorSrv->generateModuleNameCase($container['melis-templatingplugincreator']['step_1']['tpc_new_module_name']);
            
        } else {
            $moduleName = $templatingPluginCreatorSrv->generateModuleNameCase($container['melis-templatingplugincreator']['step_1']['tpc_existing_module_name']);
        }

        return $moduleName;
    }

    /**
     * Retrieves the temp thumbnail directory
     * @return string
    */
    private function getTempThumbnailDirectory(){
        // Set the user
        $melisModule = $this->getServiceManager()->get('MelisAssetManagerModulesService');                        
        $names = explode("\\", __NAMESPACE__);                       
        $moduleName = $names[0];        
        $tempThumbnailDirectory = $melisModule->getModulePath($moduleName,true).'/public/temp-thumbnail/';

        return $tempThumbnailDirectory;
    }
}