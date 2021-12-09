<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisTemplatingPluginCreator\Service;

use Laminas\Session\Container;
use Laminas\Config\Factory;
use MelisCore\Service\MelisGeneralService;

/**
 * 
 * This service executes the testing of page links
 *
 */
class MelisTemplatingPluginCreatorService extends MelisGeneralService
{  
    protected $templatingPluginCreatorTplDir;   
    protected $steps;
    protected $pluginName;
    protected $moduleName;
    const EXISTING_MODE = "existing_module";    
    const DROPDOWN = "Dropdown";
    const NUMERIC_INPUT = "NumericInput";
    const PAGE_INPUT = "PageInput";
    const DATE_PICKER = 'DatePicker';
    const DATETIME_PICKER = 'DateTimePicker';
    const SWITCH = 'Switch';


    public function __construct()
    {    
        $this->templatingPluginCreatorTplDir = __DIR__ .'/../../template';

        //session container     
        $container = new Container('templatingplugincreator');     
        $this->steps = $container['melis-templatingplugincreator'];  
    }
     
    /**
     * This will generate the templating plugin based on the parameters stored in the current session   
     * @return boolean
     */
    public function generateTemplatingPlugin()
    {   
        // Sending service start event
        $this->sendEvent('melistemplating_plugin_creator_service_generate_templating_plugin_start', []);

        //set module name and directory
        if ($this->steps['step_1']['tpc_plugin_destination'] == self::EXISTING_MODE) {
            $this->moduleName = $this->steps['step_1']['tpc_existing_module_name'];           
            $moduleDir = $_SERVER['DOCUMENT_ROOT'].'/../module/MelisSites/'.$this->moduleName;
        } else {
            $this->moduleName = $this->generateModuleNameCase($this->steps['step_1']['tpc_new_module_name']);           
            $moduleDir = $_SERVER['DOCUMENT_ROOT'].'/../module/'.$this->moduleName;

            //unset the tools tree section of the newly created module
            $this->emptyConfigToolsTreeSection($moduleDir);
        }

        //set plugin name
        $this->pluginName = $this->generateModuleNameCase($this->steps['step_1']['tpc_plugin_name']);
       
        //perform the steps in generating the Templating plugin
        $isSuccessful = $this->performGeneration($moduleDir);   
                  
        if ($isSuccessful) {    
            //remove temp thumbnail directory of the current session  
            $tempPath = pathinfo($this->getTempThumbnail(), PATHINFO_DIRNAME);            
            $this->removeDir($tempPath);
        } else {            
            //this will rollback the steps performed when generating the Templating plugin
            $this->rollbackPluginGeneration($moduleDir);              
        }    

        $this->sendEvent('melistemplating_plugin_creator_service_generate_templating_plugin_end', $this->steps);
        return $isSuccessful; 
    }

    /**
     * This will perform the generation of plugin
     * @param string $moduleDir
     * @return boolean
     */
    protected function performGeneration($moduleDir)
    {
        //generate the Templating Plugin config
        $isSuccessful = $this->generateTemplatingPluginConfig($moduleDir);
        if (!$isSuccessful) {
            return false;
        }

        //update module.config.php
        $isSuccessful = $this->updateModuleConfig($moduleDir);
        if (!$isSuccessful) {
            return false;
        }     

        //set the translations
        $isSuccessful = $this->setTranslations($moduleDir);  
        if (!$isSuccessful) {
            return false;
        }       

        //generate assets
        $isSuccessful = $this->generateTemplatingPluginAssets($moduleDir);
        if (!$isSuccessful) {
            return false;
        }

        //generate the Templating Plugin Controller
        $isSuccessful = $this->generateTemplatingPluginController($moduleDir);
        if (!$isSuccessful) {
            return false;
        }

        //generate view file
        $isSuccessful = $this->generateTemplatingPluginView($moduleDir);
        if (!$isSuccessful) {
            return false;
        }

        //generate modal forms
        $isSuccessful = $this->generatePluginModalForm($moduleDir);
        if (!$isSuccessful) {
            return false;
        }

        //update Module.php to add the newly created Templating Plugin config file
        $isSuccessful = $this->updateModuleFile($moduleDir);
        if (!$isSuccessful) {
            return false;
        }
        
        return true;
    }

    /**
     * This will rollback the steps done in generating the Templating plugin
     * @param string $moduleDir
     * @return boolean
     */
    protected function rollbackPluginGeneration($moduleDir)
    {
        //remove created files if using existing mode
        if ($this->steps['step_1']['tpc_plugin_destination'] == self::EXISTING_MODE) {              
            
            /*delete Templating plugin config*/
            if (file_exists($moduleDir.'/config/plugins/'.$this->moduleName.$this->pluginName.'Plugin.config.php')) {
                unlink($moduleDir.'/config/plugins/'.$this->moduleName.$this->pluginName.'Plugin.config.php');
            }
            
            //update module.config.php to remove the Templating Plugin in template_path and controller_plugin keys 
            $this->updateModuleConfig($moduleDir, false);    

            //remove translation keys
            $this->setTranslations($moduleDir, false);
          
            /*******start remove assets(css/js/image)*****/
            $cssFile = $moduleDir.'/public/plugins/css/plugin.'.$this->pluginName.'.css';
            if (file_exists($cssFile)) {
                unlink($cssFile);
            }
            
            $jsFile = $moduleDir.'/public/plugins/js/plugin.'.$this->pluginName.'.init.js';
            if (file_exists($jsFile)) {
                unlink($jsFile); 
            }
              
            //get the filename
            $fileName = $this->moduleName.$this->pluginName.'Plugin_thumbnail.'.pathinfo($this->steps['step_2']['plugin_thumbnail'], PATHINFO_EXTENSION);
            $thumbFile = $moduleDir.'/public/plugins/images/'.$fileName;
            if (file_exists($thumbFile)) {
                unlink($thumbFile);
            }
            /*******end remove assets*****/                                        

            //delete controller
            if (file_exists($moduleDir.'/src/'.$this->moduleName.'/Controller/Plugin/'.$this->moduleName.$this->pluginName.'Plugin.php')) {
                unlink($moduleDir.'/src/'.$this->moduleName.'/Controller/Plugin/'.$this->moduleName.$this->pluginName.'Plugin.php');
            }
            
            //delete template path view file
            if (file_exists($moduleDir.'/view/plugins/'.$this->convertToViewName($this->pluginName).'.phtml')) {
                unlink($moduleDir.'/view/plugins/'.$this->convertToViewName($this->pluginName).'.phtml');
            }

            //delete form view file for each tab
            $tabCount = 1;//default to 1 for now
            $tabName = 'plugin-'.$this->convertToViewName($this->pluginName).'-tab-';
            for ($t = 1; $t <= $tabCount; $t++) {   
                if (file_exists($moduleDir.'/view/plugins/'.$tabName.$t.'-modal-form.phtml')) {
                    unlink($moduleDir.'/view/plugins/'.$tabName.$t.'-modal-form.phtml');
                }   
            } 
           
            //update module.php to remove the Templating config 
            $this->updateModuleFile($moduleDir, false);  

        } else {        
            //remove newly created module if using new module              
            $this->removeDir($moduleDir);
        }
    }


    /**
     * This method generates the Templating plugin config
     * @param string $moduleDir
     * @return boolean
     */
    protected function generateTemplatingPluginConfig($moduleDir)
    {
        $targetDir = $moduleDir.'/config/plugins';

        // get the config template
        $templatingPluginConfigContent = $this->getTemplateContent('/TemplatingPlugin.config.php');  
        //this default to 1 for now, in ver. 2, the # of tabs is given in step 1
        $tabCount = 1; 
        $fields = "";
        $tab = "";

        //set the fields with their default values in the config file after the 'template_path' key          
        $pattern = "/^.*\btemplate_path\b'.*$/m";                  
        $match = null;
        if (preg_match_all($pattern, $templatingPluginConfigContent, $matches)) {
            $match = implode("\n", $matches[0]);//return as string
        }

        if ($match) {
            for ($t = 1; $t <= $tabCount; $t++) {
                $tabFieldCount = $this->steps['step_3']['main_form']['tpc_main_property_field_count'];
                
                //starts with field #2 since the field 1[template_path] is already added in the template file
                for ($f = 2; $f <= $tabFieldCount; $f++) {
                    $fieldName = $this->steps['step_3']['tab_'.$t]['field_'.$f]['tpc_field_name'];
                    $displayType = $this->steps['step_3']['tab_'.$t]['field_'.$f]['tpc_field_display_type'];
                    $defaultValue = $this->steps['step_3']['tab_'.$t]['field_'.$f]['tpc_field_default_value'];

                    if ($f != 2) {
                        $tab = "\t\t\t\t\t\t";
                    }
                    $fields .= $tab."'".$fieldName."' => '".$defaultValue."',\r\n";
                }
            }
            $templatingPluginConfigContent = str_replace($match, $match."\t\t\t\t\t\t".$fields, $templatingPluginConfigContent);
        }

        //set the tab properties    
        for ($i = 1; $i <= $tabCount; $i++) {       
            $tabPropertiesTpl = $this->getTemplateContent('/Code/tab-properties');           
           
            //set the tab#
            $tabPropertiesTpl = str_replace('TAB#', $i, $tabPropertiesTpl);

            //set the tab icon
            $tabIcon = 'fa-cog';//default for tab 1, in ver 2, tab icon is selected for each tab
            $tabPropertiesTpl = str_replace('#tabicon', $tabIcon, $tabPropertiesTpl);            
            $fieldCount = $this->steps['step_3']['main_form']['tpc_main_property_field_count'];            
            $tabElementCollection = '';
            $tabInputFilterCollection = '';

            //set tab's elements and input filters
            for ($j = 1; $j <= $fieldCount; $j++) {
                //set template path default value
                if ($j == 1) {
                    $templatingPluginConfigContent = str_replace('#template_path',$this->steps['step_3']['tab_'.$i]['field_'.$j]['tpc_field_default_value'], $templatingPluginConfigContent);
                }

                /********************* start setting tab elements ********************/
                $tabElements = "";
                if ($j != 1) {
                    $tabElements = "\t\t\t\t\t\t\t\t\t";
                }
               
                //get the template
                $tabElements .= $this->getTemplateContent('/Code/tab-elements');
                $fieldDisplayType = $this->steps['step_3']['tab_'.$i]['field_'.$j]['tpc_field_display_type'];
                $fieldName = $this->steps['step_3']['tab_'.$i]['field_'.$j]['tpc_field_name'];
                $classAttr = "'form-control'";//default
                
                if ($fieldDisplayType == self::DROPDOWN) {   
                    $pattern = "/^.*\btooltip\b.*$/m";
                    if (preg_match_all($pattern, $tabElements, $matches)) {
                        $match = implode("\n", $matches[0]);//return as string

                        //set 'empty_option' key and put it under the 'tooltip' key
                        $emptyOptionLabel = "'empty_option' => 'tr_meliscore_common_choose',\r\n\t\t\t\t\t\t\t\t\t\t\t\t'disable_inarray_validator' => true";             
                        $tabElements = str_replace($match, $match. "\t\t\t\t\t\t\t\t\t\t\t\t".$emptyOptionLabel.",", $tabElements);

                        //set 'value_option' key of the element if default options are given and put it under the 'tooltip' key
                        $defaultOptions = $this->steps['step_3']['tab_'.$i]['field_'.$j]['tpc_field_default_options'];
                        if ($defaultOptions) {
                            $defaultOptions = explode(',',$defaultOptions);
                            $options = "";

                            foreach ($defaultOptions as $val) {
                                $translationKey = 'tr_melis_'.strtolower($this->moduleName).'_'.strtolower($this->pluginName).'_'.$fieldName.'_'.$this->removeNonAlphaNumeric($val).'_label'; 

                                $options .= "\t\t\t\t\t\t\t\t\t\t\t\t\t'".$val."'=> '".$translationKey."',\r\n";
                            }

                            $valueOptions = "'value_options' => [\r\n".$options."\t\t\t\t\t\t\t\t\t\t\t\t]";
                            $tabElements = str_replace($match, $match."\t\t\t\t\t\t\t\t\t\t\t\t".$valueOptions.",\r\n", $tabElements);
                        }  
                    }                               
                    
                } elseif ($fieldDisplayType == self::SWITCH) {
                    //set the switch options and put under the 'tooltip' key
                    $pattern = "/^.*\btooltip\b.*$/m";
                    if (preg_match_all($pattern, $tabElements, $matches)) {
                        $match = implode("\n", $matches[0]);//return as string

                        $switchOptions = "'checked_value' => 1,"."\r\n\t\t\t\t\t\t\t\t\t\t\t\t".
                                        "'unchecked_value' => 0,"."\r\n\t\t\t\t\t\t\t\t\t\t\t\t".
                                        "'switchOptions' => ["."\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t".
                                            "'label-on' => 'tr_meliscore_common_yes',"."\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t".
                                            "'label-off' => 'tr_meliscore_common_nope',"."\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t".
                                            "'label' => \"<i class='glyphicon glyphicon-resize-horizontal'></i>\","."\r\n\t\t\t\t\t\t\t\t\t\t\t\t".
                                        "],"."\r\n\t\t\t\t\t\t\t\t\t\t\t\t".
                                        "'disable_inarray_validator' => true";

                        $tabElements = str_replace($match, $match."\t\t\t\t\t\t\t\t\t\t\t\t".$switchOptions.",",  $tabElements);
                    }

                } elseif ($fieldDisplayType == self::PAGE_INPUT) {
                    //set field attributes here for the pageinput field and put under the 'required' key
                    $classAttr = "'melis-input-group-button'";

                    $pattern = "/^.*\brequired\b'.*$/m";
                    if (preg_match_all($pattern, $tabElements, $matches)) {
                        $match = implode("\n", $matches[0]);//return as string
                        $pageInputOptionAttr = "'data-button-icon' => 'fa fa-sitemap',"."\r\n\t\t\t\t\t\t\t\t\t\t\t\t".
                                               "'data-button-id' => 'meliscms-site-selector'"."\r\n\t\t\t\t\t\t\t\t\t\t\t\t";                               
                        $tabElements = str_replace($match, $match."\t\t\t\t\t\t\t\t\t\t\t\t".$pageInputOptionAttr.",", $tabElements);
                    }                  
                }               
                
                //set class attribute
                $tabElements = str_replace('#classAttr', $classAttr, $tabElements);

                //set field name
                $tabElements = str_replace('#field_name', $this->steps['step_3']['tab_'.$i]['field_'.$j]['tpc_field_name'], $tabElements);
                              
                //set field type
                switch ($fieldDisplayType) {
                    case 'MelisText':
                    case 'DatePicker':
                    case 'DateTimePicker':
                    case 'PageInput':
                    case 'NumericInput':
                        $fieldDisplayType = 'MelisText';
                        break;
                    case 'Dropdown':
                    case 'Switch':
                        $fieldDisplayType = 'Select';
                        break;
                    default:
                        $fieldDisplayType = $fieldDisplayType;
                        break;
                } 
                $tabElements = str_replace('#field_type', $fieldDisplayType, $tabElements);

                //set 'required' attribute
                $isRequired = $this->steps['step_3']['tab_'.$i]['field_'.$j]['tpc_field_is_required'];
                $tabElements = str_replace('#isRequired', ($isRequired==1?'required':''), $tabElements); 

                $tabElementCollection .= $tabElements;
                /************************ end setting tab elements *******************************/


                /************************ start setting input filters****************************/
                $tabInputFilters = "";
                $validators = [];
                $displayType = $this->steps['step_3']['tab_'.$i]['field_'.$j]['tpc_field_display_type'];

                if ($j != 1) {
                    $tabInputFilters = "\t\t\t\t\t\t\t\t\t";
                }

                //get the template
                $tabInputFilters .= $this->getTemplateContent('/Code/tab-input-filters');

                //set field name
                $tabInputFilters = str_replace('field_name', $this->steps['step_3']['tab_'.$i]['field_'.$j]['tpc_field_name'], $tabInputFilters);

                //set required attribute
                $isRequired = $this->steps['step_3']['tab_'.$i]['field_'.$j]['tpc_field_is_required'];
                $tabInputFilters = str_replace('#isRequired', ($isRequired==1?'true':'false'), $tabInputFilters);

                //add empty field validator if field is required 
                if ($isRequired == 1) {
                    $validators[] = $this->getTemplateContent('/Code/empty-field-validator');
                } 

                //add digit validator if the selected display type is either numeric or page input
                if ($displayType == self::NUMERIC_INPUT || $displayType == self::PAGE_INPUT) {
                    $validators[] = $this->getTemplateContent('/Code/digit-validator');
                }

                //add validators inside the 'validators' key
                if ($validators) {
                    foreach ($validators as $validator) {
                        //search for the validators keyword to append the filters
                        $pattern = 'validators\s*[\'"]\s*=>\s*\[';  
                        $pattern = '/('.$pattern.')/';

                        if (preg_match_all($pattern, $tabInputFilters, $matches)) {
                            $match = implode("\n", $matches[0]);//return as string
                            $tabInputFilters = str_replace($match, $match."\r\n".$validator, $tabInputFilters);
                        }
                    }                    
                }              

                $tabInputFilterCollection .= $tabInputFilters;
                /************************ end setting input filters*/
            }
        
            $tabPropertiesTpl = str_replace('#TABELEMENTS', $tabElementCollection, $tabPropertiesTpl); 
            $tabPropertiesTpl = str_replace('#TABINPUTFILTERS', $tabInputFilterCollection, $tabPropertiesTpl); 
        }

        $templatingPluginConfigContent = str_replace('#TABPROPERTIES  ', $tabPropertiesTpl, $templatingPluginConfigContent); 

        $res = $this->generateFile($this->moduleName.$this->pluginName.'Plugin.config.php', $targetDir, $templatingPluginConfigContent);
        return $res;
    }


    /**
     * This method generates the Templating plugin controller
     * @param string $moduleDir
     * @return boolean
     */
    protected function generateTemplatingPluginController($moduleDir)
    {
        $targetDir = $moduleDir.'/src/'.$this->moduleName.'/Controller/Plugin';
        $tabIconArr = array();
        $tabIcons = '';

        //get the controller template
        $templatingPluginControllerContent = $this->getTemplateContent('/TemplatingPluginController.php');   

        //update loadDbXmlToPluginConfig and savePluginConfigToXml functions
        $tabCount = 1; //this default to 1 for now          
        $configValues = "";
        $xmlValueFormatted = "";
        $tabConfig = "";
        $tabXML = "";

        //retrieve all fields from all tabs
        for ($t = 1; $t <= $tabCount; $t++) {
            $tabFieldCount = $this->steps['step_3']['main_form']['tpc_main_property_field_count'];            
            
            for ($f = 1; $f <= $tabFieldCount; $f++) {
                $fieldName = $this->steps['step_3']['tab_'.$t]['field_'.$f]['tpc_field_name'];
                $fieldDisplayType = $this->steps['step_3']['tab_'.$t]['field_'.$f]['tpc_field_display_type'];

                $cond = '!empty';
                //use isset if display type is switch option
                if ($fieldDisplayType == self::SWITCH) {
                    $cond = 'isset';
                }

                if ($f != 1) {
                    $tabConfig = "\t\t\t";
                    $tabXML = "\t\t";
                }

                $configValues .= $tabConfig."if (".$cond."(\$xml->".$fieldName."))\r\n\t\t\t\t";
                $configValues .= "\$configValues['".$fieldName."'] = (string)\$xml->".$fieldName.";\r\n";   
                $xmlValueFormatted .= $tabXML."if (".$cond."(\$parameters['".$fieldName."']))\r\n\t\t\t";
                $xmlValueFormatted .=  " \$xmlValueFormatted .= \"\\t\\t\". '<".$fieldName."><![CDATA[' . \$parameters['".$fieldName."'] . ']]></".$fieldName.">';\r\n";               
            }
        }

        $templatingPluginControllerContent = str_replace("#LOADDBXMLTOPLUGINCONFIGVALUES", $configValues, $templatingPluginControllerContent);
        $templatingPluginControllerContent = str_replace("#XMLVALUEFORMATTED", $xmlValueFormatted, $templatingPluginControllerContent);    
        
        $res = $this->generateFile($this->moduleName.$this->pluginName.'Plugin.php', $targetDir, $templatingPluginControllerContent);
        return $res;
    }

    /**
     * This method generates the Templating plugin view file
     * @param string $moduleDir
     * @return boolean
     */
    protected function generateTemplatingPluginView($moduleDir)
    {
        $targetDir = $moduleDir.'/view/plugins';
                
        //get the plugin view template for multiple tabs
        $templatingPluginViewContent = $this->getTemplateContent('/plugin-name.phtml');

        //set module name and directory
        if ($this->steps['step_1']['tpc_plugin_destination'] == self::EXISTING_MODE) {                     
            $moduleDir = '/module/MelisSites';
        } else {                     
            $moduleDir = '/module';
        }

        $templatingPluginViewContent = str_replace('ModuleDir', $moduleDir, $templatingPluginViewContent);

        //generate view file
        $res = $this->generateFile($this->convertToViewName($this->steps['step_1']['tpc_plugin_name']).'.phtml', $targetDir, $templatingPluginViewContent);

        return $res;
    }

    /**
     * This method generates the plugin modal form for each tab
     * @param string $moduleDir
     * @return boolean
     */
    protected function generatePluginModalForm($moduleDir)
    {
        $targetDir = $moduleDir.'/view/plugins';
        $tabName = 'plugin-'.$this->convertToViewName($this->steps['step_1']['tpc_plugin_name']).'-tab-';
        $tabCount = 1;//default to 1 for now
       
        for ($t = 1; $t <= $tabCount; $t++) {
            $datePickerFields = "";
            $dateTimePickerFields = "";
            $datePickerScript = "";
            $dateTimePickerScript = "";
            $modalScripts = "";

            //set js script if one of the fields' display type is either datepicker or datetimepicker
            $fieldCount = $this->steps['step_3']['main_form']['tpc_main_property_field_count']; 
            for ($f = 1; $f <= $fieldCount; $f++) {
                $fieldName = $this->steps['step_3']['tab_'.$t]['field_'.$f]['tpc_field_name'];
                $displayType = $this->steps['step_3']['tab_'.$t]['field_'.$f]['tpc_field_display_type'];
              
                if ($displayType == self::DATE_PICKER) {
                    $datePickerFields .= !empty($datePickerFields) ? ",#".$fieldName : "#".$fieldName;
                } elseif ($displayType == self::DATETIME_PICKER) {
                    $dateTimePickerFields .= !empty($dateTimePickerFields) ? ",#".$fieldName : "#".$fieldName;
                }
            }

            if (!empty($datePickerFields)) {
                $datePickerScript = "$('".$datePickerFields."').datetimepicker({ \r\n\t\t\t".                                   
                                    "format: 'YYYY-MM-DD',\r\n\t\t".
                                    "});";
            }

            if (!empty($dateTimePickerFields)) {
                $dateTimePickerScript = "$('".$dateTimePickerFields."').datetimepicker({\r\n\t\t\t".                                       
                                        "format: 'YYYY-MM-DD HH:mm:ss',\r\n\t\t".
                                    "});";
            }
            
            if ($datePickerScript || $dateTimePickerScript) {
                //get modal form scripts template
                $modalScripts = $this->getTemplateContent('/Code/modal-form-scripts');
                $modalScripts = str_replace('#DATEPICKER', $datePickerScript, $modalScripts);
                $modalScripts = str_replace('#DATETIMEPICKER', $dateTimePickerScript, $modalScripts);                                     
            }

            //get modal form template
            $templatingPluginModalFormContent = $this->getTemplateContent('/modal-form.phtml');
            $templatingPluginModalFormContent = str_replace('#JSSCRIPTS', $modalScripts, $templatingPluginModalFormContent);  
                       
            //generate view file
            $res = $this->generateFile($tabName.$t.'-modal-form.phtml', $targetDir, $templatingPluginModalFormContent);
        }       
      
        return $res;
    }



    /**
     * This method generates the css and js files and copy the uploaded plugin thumbnail to the module's asset directory
     * @param string $moduleDir
     * @return boolean
     */
    protected function generateTemplatingPluginAssets($moduleDir)
    {       
        $targetDir = $moduleDir.'/public/plugins';
        $assetArr = array('css' => $targetDir.'/css', 'js' => $targetDir.'/js','images' => $targetDir.'/images');
        $res = false;

        foreach ($assetArr as $asset => $dir) {            
            if ($asset == 'css') {
                $res = $this->generateFile('plugin.'.$this->pluginName.'.'.$asset, $dir, '');
            } elseif ($asset == 'js') {                    
                $res = $this->generateFile('plugin.'.$this->pluginName.'.init.'.$asset, $dir, $this->getTemplateContent('/blank_plugin.js'));
            } elseif ($asset == 'images') {                
                //check if target directory exists
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }                        

                //copy saved thumbnail to the plugins/image directory of the module          
                $tempThumbnail = $this->getTempThumbnail();

                //set the filename
                $fileName = $this->moduleName.$this->pluginName.'Plugin_thumbnail.'.pathinfo($this->steps['step_2']['plugin_thumbnail'], PATHINFO_EXTENSION); 
                if (copy($tempThumbnail, $dir.'/'.$fileName)) {  
                    $res = true;
                }                 
            }             
        }

        return $res;
    }

    /**
     * This method retrieves the temp thumbnail path and filename     
     * @return string
     */
    public function getTempThumbnail()
    {
        $this->sendEvent('melistemplating_plugin_creator_service_get_temp_thumbnail_plugins_start', []);

        //session container     
        $container = new Container('templatingplugincreator');     
        $sessionID = $container['melis-templatingplugincreator']['sessionID'];

        //get the templating plugin creator's module directory 
        $melisModule = $this->getServiceManager()->get('MelisAssetManagerModulesService');  
        $names = explode("\\", __NAMESPACE__);                       
        $moduleToolName = $names[0];
        $thumbnailTempPath = $melisModule->getModulePath($moduleToolName,true).'/public/temp-thumbnail/';        
        
        //append the current session ID to the thumbnail path
        $thumbnailTempPath = $thumbnailTempPath.$sessionID.'/';
        $baseName = pathinfo($this->steps['step_2']['plugin_thumbnail'], PATHINFO_BASENAME);      
        $pluginThumbnail = $thumbnailTempPath.$baseName;

        $this->sendEvent('melistemplating_plugin_creator_service_get_temp_thumbnail_plugins_end', []);       
        return $pluginThumbnail;
    }

    /**
     * This method sets or unsets the translations of the plugin's properties for each language available in the platform
     * @param string $moduleDir
     * @param boolean $appencConfig    
     * @return string
     */
    protected function setTranslations($moduleDir, $appendConfig = true)
    {
        $languageDir = $moduleDir.'/language/';
        
        //retrieve all available languages
        $coreLang = $this->getServiceManager()->get('MelisCoreTableLang');
        $languages = $coreLang->fetchAll()->toArray();
        $errorCount = 0;
        $res = false;

        foreach ($languages as $lang) {
            $langFile = $languageDir.$lang['lang_locale'].'.interface.php';
            $langFile = file_exists($langFile)?$langFile:$languageDir.$lang['lang_locale'].'.php';

            //create file if not yet exists
            if (!file_exists($langFile)) {
                $languageTpl = $this->getTemplateContent('/language-tpl.php');
                $this->generateFile($lang['lang_locale'].'.php', $languageDir, $languageTpl);               
            }

            if (file_exists($langFile)) {               
                //get the existing translation of the language
                $translationArr = include $langFile;
                
                //set here the name and description of the plugin
                if ($appendConfig) {
                    //set the plugin title
                    $translationArr['tr_'.$this->moduleName.$this->pluginName.'Plugin_Name'] = !empty($this->steps['step_2'][$lang['lang_locale']]['tpc_plugin_title'])
                                                        ?$this->removeExtraSpace($this->steps['step_2'][$lang['lang_locale']]['tpc_plugin_title']):"";
                    
                    //set the plugin description
                    $translationArr['tr_'.$this->moduleName.$this->pluginName.'Plugin_Description'] = !empty($this->steps['step_2'][$lang['lang_locale']]['tpc_plugin_desc'])                                ?$this->removeExtraSpace($this->steps['step_2'][$lang['lang_locale']]['tpc_plugin_desc']):"";
                } else {
                    //unset name and desc translation if doing rollback process
                    unset($translationArr['tr_'.$this->moduleName.$this->pluginName.'Plugin_Name']);
                    unset($translationArr['tr_'.$this->moduleName.$this->pluginName.'Plugin_Description']);
                }
                   
                //set here the tab title and field translations of each tab
                $tabCount = 1; //this default to 1 for now    
                for ($t = 1; $t <= $tabCount; $t++) {
                    $tabFieldCount = $this->steps['step_3']['main_form']['tpc_main_property_field_count'];
                    $tr_keyword = 'tr_'.strtolower($this->moduleName).'_'.strtolower($this->pluginName).'_plugin_tab';
                      
                    //set the title for Properties Tab, always present in templating plugin, in version 2, tab title is set as required field
                    if ($t == 1) {
                        $tr_keyword = $tr_keyword.'_properties';                    

                        //set the default translation for the Properties tab name
                        if ($appendConfig) {
                            $tr_value = "";        
            
                            if ($lang['lang_locale'] == 'en_EN') {
                                $tr_value = "Properties";
                            } elseif ($lang['lang_locale'] == 'fr_FR') {
                                $tr_value = "Propriétés";
                            }

                            $translationArr[$tr_keyword] = $tr_value;
                        } else {
                            //unset translation if rollback
                            unset($translationArr[$tr_keyword]);
                        }                                                         
                    }
                    
                    //set translation for label and tooltip of each field
                    for ($f = 1; $f <= $tabFieldCount; $f++) {
                        $fieldName = $this->steps['step_3']['tab_'.$t]['field_'.$f]['tpc_field_name'];
                        $trKeywordLabel = 'tr_melis_'.strtolower($this->moduleName).'_'.strtolower($this->pluginName).'_'.$fieldName.'_label'; 
                        $trKeywordTooltip = 'tr_melis_'.strtolower($this->moduleName).'_'.strtolower($this->pluginName).'_'.$fieldName.' tooltip';
                        
                        //set translations for field label and tooltip                     
                        if ($appendConfig) {
                            $translationArr[$trKeywordLabel] = !empty($this->steps['step_4'][$lang['lang_locale']]['tab_'.$t]['field_'.$f]['tpc_field_label']) ? $this->removeExtraSpace($this->steps['step_4'][$lang['lang_locale']]['tab_'.$t]['field_'.$f]['tpc_field_label']) : '';;
                            $translationArr[$trKeywordTooltip] = !empty($this->steps['step_4'][$lang['lang_locale']]['tab_'.$t]['field_'.$f]['tpc_field_tooltip']) ? $this->removeExtraSpace($this->steps['step_4'][$lang['lang_locale']]['tab_'.$t]['field_'.$f]['tpc_field_tooltip']) : '';

                        } else {
                            //unset if performing rollback  
                            unset($translationArr[$trKeywordLabel]);
                            unset($translationArr[$trKeywordTooltip]);
                        }          
                        
                        /*check if there are translated dropdown values*/
                        $displayType = $this->steps['step_3']['tab_'.$t]['field_'.$f]['tpc_field_display_type'];
                        // $dropdownValues = $this->steps['step_3']['tab_'.$t]['field_'.$f]['tpc_field_default_value'];
                        $dropdownValues = $this->steps['step_3']['tab_'.$t]['field_'.$f]['tpc_field_default_options'];

                        if ($displayType == 'Dropdown' && !empty($dropdownValues)) {
                            $explode = explode(',', $dropdownValues);
                            foreach ($explode as $val) {
                                $val = $this->removeNonAlphaNumeric($val);
                                $dropdownKeyword = 'tr_melis_'.strtolower($this->moduleName).'_'.strtolower($this->pluginName).'_'.$fieldName.'_'.$this->removeNonAlphaNumeric($val).'_label';                         
                                
                                //set translations for dropdown values' labels
                                if ($appendConfig) {
                                    $translationArr[$dropdownKeyword] = !empty($this->steps['step_4'][$lang['lang_locale']]['tab_'.$t]['field_'.$f][$val.'_label']) ? $this->steps['step_4'][$lang['lang_locale']]['tab_'.$t]['field_'.$f][$val.'_label'] : '';
                                } else {
                                    //unset if performing rollback
                                    unset($translationArr[$dropdownKeyword]);
                                }
                            }
                        }
                    }
                }   

                //write back to file the updated translation array
                $configFactory = new Factory();
                $write = $configFactory->toFile($langFile, $translationArr);

                if (!$write) {
                    $errorCount++;
                }              
            }                
        }

        if ($errorCount) {
            return $res;
        } else {
            return true;
        }
    }


    /**
     * This method updates module.config.php of the Module by adding or removing the controller_plugin and template_map entry
     * @param string $moduleDir
     * @param boolean $appendConfig
     * @return boolean
     */
    protected function updateModuleConfig($moduleDir, $appendConfig = true)
    {
        $res = false;

        //get the existing module.config.php
        $moduleConfigFile = $moduleDir.'/config/module.config.php';       
        $pluginToViewName = $this->convertToViewName($this->steps['step_1']['tpc_plugin_name']);
        
        //get the content of module.config.php 
        $moduleFileContent = file_get_contents($moduleConfigFile);

        if ($appendConfig) {         

            //add the template map entries
            $moduleFileContent = $this->setTemplateMapEntry($moduleDir, $moduleFileContent, $pluginToViewName);

            //add the controller plugin entry
            $moduleFileContent = $this->setControllerPluginEntry($moduleFileContent);
    
        } else {       
            //find the controller_plugin and template_map keys of the created Templating plugin, then remove           
            $keyArr = array();
   
             //add to array the controller_plugin key
            $keyArr[] = $this->moduleName.$this->pluginName.'Plugin';
            
            //add to array the main template_path key
            $keyArr[] = $this->moduleName."/plugins/".$pluginToViewName;

            //add to array the template_path keys of the modal forms for each plugin tab
            $tabCount = 1;//default 1 for now  
            for ($t = 1; $t <= $tabCount; $t++) {            
                $key = $this->moduleName."/plugins/plugin-".$pluginToViewName."-tab-".$t."-modal-form";  
                $keyArr[] = $key;                       
            }  

            if ($keyArr) {
                foreach ($keyArr as $key) {
                    // escape special characters in the query
                    $pattern = preg_quote($key, '/');
                    // finalize the regular expression, matching the whole line
                    $pattern = "/^.*$pattern.*\$/m";

                    // search, and store all matching occurences in $matches
                    if (preg_match_all($pattern, $moduleFileContent, $matches)) { 
                        $match = implode("\n", $matches[0]);
                        //remove the key entry
                        $moduleFileContent = str_replace($match, '', $moduleFileContent);
                    }  
                }
            }   
        }      

        // Write the contents back to the file
        $res = file_put_contents($moduleConfigFile, $moduleFileContent);

        return $res;
    }

     /**
     * This method updates module.config.php of the destination module by adding a template_map key entry of the Templating plugin
     * @param string $moduleDir
     * @param string $moduleFileContent    
     * @param string pluginToViewName
     * @return string
     */
    protected function setTemplateMapEntry($moduleDir, $moduleFileContent, $pluginToViewName)
    {   
        //set template map entry for the plugin's main template     
        $templateMapKey = $this->moduleName."/plugins/".$pluginToViewName;        
        $templateMapValue = ".'/../view/plugins/".$pluginToViewName.".phtml'"; 
        $templateMap = "'".$templateMapKey."' => __DIR__".$templateMapValue.",\r\n\t\t\t";

        //set template map entry for plugin's modal form for each tab    
        $tabCount = 1;//default 1 for now    
        for ($t = 1; $t <= $tabCount; $t++) {            
            $key = $this->moduleName."/plugins/plugin-".$pluginToViewName."-tab-".$t."-modal-form";            
            $val = ".'/../view/plugins/plugin-".$pluginToViewName."-tab-".$t."-modal-form".".phtml'"; 
            $templateMap .= "'".$key."' => __DIR__".$val.",\r\n";
        }       
                  
        $match = null;
        //search for the template_map key
        $pattern = 'template_map\s*[\'"]\s*=>\s*(array\s*\(|\[)\s*';
        $pattern = '/('.$pattern.')/';        

        if (preg_match_all($pattern, $moduleFileContent, $matches)) {
            $match = implode("\n", $matches[0]);//return as string

            //check the existence of the template_map entries for the formatting purposes          
            $moduleConfig = include $moduleDir.'/config/module.config.php';
            $existingTemplateMapEntry = $moduleConfig['view_manager']['template_map'];

            if (count($existingTemplateMapEntry) > 0) {
                $templateMapEntry = $templateMap."\t\t\t";
            } else {
                $templateMapEntry = "\t".$templateMap."\t\t";
            }

            $moduleFileContent = str_replace($match, $match.$templateMapEntry, $moduleFileContent);
        } else {
            //search for the view_manager key to add the template_map key
            $pattern = 'view_manager\s*[\'"]\s*=>\s*(array\s*\(|\[)\s*';
            $pattern = '/('.$pattern.')/';        

            if (preg_match_all($pattern, $moduleFileContent, $matches)) {
                $match = implode("\n", $matches[0]);//return as string

                $templateMapArr ="'template_map' => ["."\t\t".                    
                    "\r\n\t\t\t'". $templateMap.                
                "]";             
                $moduleFileContent = str_replace($match, $match.$templateMapArr.",\r\n\t\t", $moduleFileContent);
            }          
        }
       
        return  $moduleFileContent;    
    }

    /**
     * This method updates module.config.php of the destination module by adding a controller_plugin key entry of the Templating plugin   
     * @param string $moduleFileContent 
     * @return string
     */
    protected function setControllerPluginEntry($moduleFileContent)
    {       
        $controllerPluginKey = $this->moduleName.$this->pluginName.'Plugin';
        $controllerPluginValue =  "\\".$this->moduleName."\Controller\Plugin\\".$controllerPluginKey."::class";
                
        //search for the controller_plugin invokables key and add the plugin key inside it
        $pattern = 'controller_plugins\s*[\'"]\s*=>\s*(array\s*\(|\[)\s*[\'"]invokables\s*[\'"]\s*=>\s*(array\s*\(|\[)';
        $pattern = '/('.$pattern.')/';
        $match = null;

        if (preg_match_all($pattern, $moduleFileContent, $matches)) {
            $match = implode("\n", $matches[0]);//return as string

            $controllerPluginEntry = "'".$controllerPluginKey."' => ".$controllerPluginValue;   
            $moduleFileContent = str_replace($match, $match."\r\n\t\t\t".$controllerPluginEntry.",", $moduleFileContent);
        } else {
            //search for the 'controllers' key, then add the controller_plugin key before the 'controllers' key 
            $pattern = '[\'"]\s*controllers\s*[\'"]\s*=>\s*(array\s*\(|\[)\s*';
            $pattern = '/('.$pattern.')/';        

            if (preg_match_all($pattern, $moduleFileContent, $matches)) {
                $match = implode("\n", $matches[0]);//return as string

                //set the template
                $template ="'controller_plugins' => ["."\r\n\t\t".
                    "'invokables' => [".
                    "\r\n\t\t\t'". $controllerPluginKey."' => ".$controllerPluginValue.",\r\n\t\t"
                    ."],\r\n\t".
                "],";

                //prepend it to 'controllers' key
                $moduleFileContent = str_replace($match, $template."\r\n\t".$match, $moduleFileContent);                
            }      
        }      

        return  $moduleFileContent;    
    }


    /**
     * This method updates Module.php of the Module by adding/removing the Templating plugin config path
     * @param $moduleDir
     * @param $appendConfig
     * @return boolean
     */
    protected function updateModuleFile($moduleDir, $appendConfig = true)
    {     
        //get the Module.php file
        $moduleFileContent = file_get_contents($moduleDir.'/Module.php');
                  
        //set the Templating config file 
        $templatingConfigFile = PHP_EOL . "\t\t\t". "include __DIR__ . '/config/plugins/".$this->moduleName.$this->pluginName."Plugin.config.php',";
        
        if ($appendConfig) {
            //search for the module.config.php keyword to append the Templating plugin config file 
            $pattern = 'module.config.php\s*[\'"]\s*,';
            $pattern = '/('.$pattern.')/';

            if (preg_match_all($pattern, $moduleFileContent, $matches)) {
                $match = implode("\n", $matches[0]);//return as string
                $moduleFileContent = str_replace($match, $match.$templatingConfigFile, $moduleFileContent);
            }
          
        } else {
            //remove the Templating plugin config  
            $moduleFileContent = str_replace($templatingConfigFile, '', $moduleFileContent);
        }
        
        // Write the contents back to the file
        $res = file_put_contents($moduleDir.'/Module.php', $moduleFileContent);
        return $res;
    }


    /**
     * This method generate files to the directory
     *
     * @param string $fileName - file name
     * @param string $targetDir - the target directory where the file will created
     * @param string $fileContent - will be the content of the file created
     */
    public function generateFile($fileName, $targetDir, $fileContent = null)
    {       
        try{
            //set the plugin name
            $fileContent = str_replace('PluginName', $this->pluginName, $fileContent);
            $fileContent = str_replace('pluginName', lcfirst($this->pluginName), $fileContent);
            $fileContent = str_replace('pluginname', strtolower($this->pluginName), $fileContent);
            $fileContent = str_replace('plugin-name', $this->convertToViewName($this->steps['step_1']['tpc_plugin_name']), $fileContent);
           
            //set the module name
            $fileContent = str_replace('ModuleTpl', $this->moduleName, $fileContent);
            $fileContent = str_replace('moduleTpl', lcfirst($this->moduleName), $fileContent);
            $fileContent = str_replace('moduletpl', strtolower($this->moduleName), $fileContent);
            $fileContent = str_replace('module-tpl', $this->convertToViewName($this->moduleName), $fileContent);

            //create directory if not yet exists
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            //add file if not yet exists
            $targetFile = $targetDir.'/'.$fileName;
            if (!file_exists($targetFile)) {          
                $targetFile = fopen($targetFile, 'x+');
                fwrite($targetFile, $fileContent);
                fclose($targetFile);
            }

            return true;
        } catch(Exception $e) {
            return false;
        }       
    }


    /**
     * This method retrieves the content of the template files 
     * @param string $path 
     * @return string 
     */
    protected function getTemplateContent($path)
    {
        return file_get_contents($this->templatingPluginCreatorTplDir.$path);
    }

    
    /**
     * This method converts a Module name to a valid view name directory
     * @param $string
     * @return string
     */
    public function convertToViewName($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $string));
    }
       

    /**
     * This method unsets the meliscustom_toolstree_section of the app.toolstree.php file after generating a new module
     * @param string $moduleDir
     * @return boolean
     */
    protected function emptyConfigToolsTreeSection($moduleDir)
    {
        $toolsTreeConfigFile = $moduleDir.'/config/app.toolstree.php';        
        $toolsTreeConfig = include $toolsTreeConfigFile;
        
        //unset the meliscustom_toolstree_section of the newly created module
        if (isset($toolsTreeConfig['plugins']['meliscore']['interface']['meliscore_leftmenu']['interface']['meliscustom_toolstree_section']['interface'][strtolower($this->moduleName).'_conf'])) {
            unset($toolsTreeConfig['plugins']['meliscore']['interface']['meliscore_leftmenu']['interface']['meliscustom_toolstree_section']['interface'][strtolower($this->moduleName).'_conf']);
        }
        
        $configFactory = new Factory();
        $write = $configFactory->toFile($toolsTreeConfigFile, $toolsTreeConfig);

        if (!$write) {
           return false;
        }

        return true;
    } 

    /**
     * This method removes the directory and its files recursively
     * ref: https://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
     * @param string $dirPath    
     */
    public function removeDir($dirPath) 
    {
        if (! is_dir($dirPath)) {
            return false;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);

        //remove files/directory inside the dir
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->removeDir($file);
            } else {
                unlink($file);
            }
        }

        //remove dir
        rmdir($dirPath);
    }


    /**
     * This will modified a string to valid zf2 module name, ref: MelisToolCreatorService
     * @param string $str
     * @return string
     */
    public function generateModuleNameCase($str) 
    {
        $str = preg_replace('/([a-z])([A-Z])/', "$1$2", $str);
        $str = str_replace(['-', '_'], '', ucwords(strtolower($str)));
        $str = ucfirst($str);
        $str = $this->cleanString($str);
        return $str;
    }


    /**
     * Clean strings from special characters, ref: MelisToolCreatorService
     * @param string $str
     * @return string
     */
    public function cleanString($str)
    {
        $str = preg_replace("/[áàâãªä]/u", "a", $str);
        $str = preg_replace("/[ÁÀÂÃÄ]/u", "A", $str);
        $str = preg_replace("/[ÍÌÎÏ]/u", "I", $str);
        $str = preg_replace("/[íìîï]/u", "i", $str);
        $str = preg_replace("/[éèêë]/u", "e", $str);
        $str = preg_replace("/[ÉÈÊË]/u", "E", $str);
        $str = preg_replace("/[óòôõºö]/u", "o", $str);
        $str = preg_replace("/[ÓÒÔÕÖ]/u", "O", $str);
        $str = preg_replace("/[úùûü]/u", "u", $str);
        $str = preg_replace("/[ÚÙÛÜ]/u", "U", $str);
        $str = preg_replace("/[’‘‹›‚]/u", "'", $str);
        $str = preg_replace("/[“”«»„]/u", '"', $str);
        $str = str_replace("–", "-", $str);
        $str = str_replace(" ", " ", $str);
        $str = str_replace("ç", "c", $str);
        $str = str_replace("Ç", "C", $str);
        $str = str_replace("ñ", "n", $str);
        $str = str_replace("Ñ", "N", $str);

        return ($str);
    }

    /**
     * Remove accents in the string
     * @param string $str
     * @return string
     */
    public function removeAccents($str)
    {
        $transliterator = \Transliterator::createFromRules(':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', \Transliterator::FORWARD);
        $normalized = $transliterator->transliterate($str);
        return $normalized;
    }        

    /**
     * This will remove extra spaces in the given string
     * @param string $str
     * @return string
     */
    public function removeExtraSpace($string)
    {
        $cleanStr = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $string)));
        return $cleanStr;
    }

    /**
     * This will remove non alphanumeric and extra spaces in the given string
     * @param string $str
     * @return string
     */
    public function removeNonAlphaNumeric($string)
    {
        $cleanStr = str_replace(' ','_',preg_replace("/(\W)+/", "", $string));
        return $cleanStr;
    }


    /**
     * This will retrieve the list of templating plugin names of the given site module
     * ref: MelisCorePluginService/getTemplatingPlugins
     * @param string $siteModule
     * @return array
     */
    public function getSiteTemplatingPluginNames($siteModule)
    {
        // Event parameters prepare
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        // Sending service start event
        $arrayParameters = $this->sendEvent('melistemplating_plugin_creator_service_get_site_templating_plugin_names_start', $arrayParameters);

        //set the directory of the new or existing module
        $siteService = $this->getServiceManager()->get('MelisCmsSiteService');
        $moduleDir = $siteService->getModulePath($arrayParameters['siteModule']);    
        $moduleDir = $moduleDir.'/config';  
        $templatingPlugins = [];

        $dir = new \RecursiveDirectoryIterator($moduleDir);
        foreach (new \RecursiveIteratorIterator($dir) as $filename => $file) { 
            if (!is_dir($filename) && $filename != '.' && $filename != '..' ) {                         
                $conf = include $filename;
               
                if (isset($conf['plugins'])) {
                    // get plugin config
                    $plugins = $conf['plugins'];                  
                    // put plugins into a variable
                    if (! empty($plugins)) {                       
                        foreach ($plugins as $moduleName => $val) {                           
                            // get templating plugins
                            if (isset($val['plugins']) && ! empty($val['plugins'])) {                              
                                // templating plugins
                                foreach ($val['plugins'] as $pluginName => $pluginConfig) {
                                    if ($pluginName != "MelisFrontDragDropZonePlugin") {
                                        $templatingPlugins[] = $pluginName;                                                    
                                    }
                                }
                            }
                        }                            
                    }
                }
            }   
        }

        $arrayParameters['templatingPlugins'] = $templatingPlugins;
        $arrayParameters = $this->sendEvent('melistemplating_plugin_creator_service_get_site_templating_plugin_names_end', $arrayParameters);
        return $arrayParameters['templatingPlugins']; 
    }

}
