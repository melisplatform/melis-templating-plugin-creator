<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisTemplatingPluginCreator\Form\Factory;

use Laminas\ServiceManager\ServiceManager;
use MelisCore\Form\Factory\MelisSelectFactory;

/**
 * This class sets the value options for the Existing Module dropdown
 * Retrieves all modules inside MelisSites
 */
class MelisTemplatingPluginCreatorModuleSelectFactory extends MelisSelectFactory
{
    protected function loadValueOptions(ServiceManager $serviceManager)
    {
        $moduleService = $serviceManager->get('ModulesService');
        $melisSiteModules = $moduleService->getSitesModules();

        $valueoptions = [];
        if($melisSiteModules){
            foreach($melisSiteModules as $key => $val) {               
                $valueoptions[$val] = $val;                           
            }
        }
        
        return $valueoptions;
    }
}