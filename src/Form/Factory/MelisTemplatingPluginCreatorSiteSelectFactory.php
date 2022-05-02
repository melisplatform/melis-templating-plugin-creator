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
class MelisTemplatingPluginCreatorSiteSelectFactory extends MelisSelectFactory
{
    protected function loadValueOptions(ServiceManager $serviceManager)
    {
        $siteService = $serviceManager->get('MelisCmsSiteService');
        $melisSites = $siteService->getAllSites();

        $valueoptions = [];
        if ($melisSites) {
            foreach ($melisSites as $val) {   
                //exclude MelisDemoCms and MelisDemoCmsTwig
                if ($val['site_name'] != 'MelisDemoCms' && $val['site_name'] != 'MelisDemoCmsTwig') {
                    $valueoptions[$val['site_name']] = $val['site_label'];
                }                         
            }
        }
        
        return $valueoptions;
    }
}