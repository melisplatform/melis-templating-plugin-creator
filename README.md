# Melis Templating Plugin Creator

Generates a ready-to-use templating plugin, complete with source code and necessary configuration. This will aid the developers, especially the new developers of the platform, to swiftly create a plugin without delving deeply into plugin's technicalities.

## Getting started

These instructions will get you a copy of the project up and running on your machine.

### Prerequisites

The following modules need to be installed to run the Melis Link Checker module:

- Melis Core
- Melis Tool Creator

### Installing

Run the composer command:

```
composer require melisplatform/melis-templating-plugin-creator
```

### Database

No database is needed for this tool


## Tools and elements provided

- Templating Plugin Creator Tool
- Templating Plugin Creator Service

### Templating Plugin Creator Tool

 - to follow

### Templating Plugin Creator Service

```
File: 
      - /melis-templating-plugin-creator/src/Service/MelisTemplatingPluginCreatorService.php
    
```

- MelisTemplatingPluginCreatorService
    - This service's main function is to generate a templating plugin using the parameters saved in the current session. 
      
    ```     
     $templatingPluginService = $this->getServiceManager()->get('MelisTemplatingPluginCreatorService');
     $result = $templatingPluginService->generateTemplatingPlugin();
    ```    

## Authors

- **Melis Technology** - [www.melistechnology.com](https://www.melistechnology.com/)

See also the list of [contributors](https://github.com/melisplatform/melis-newsletter/contributors) who participated in this project.

## License

This project is licensed under the Melis Technology premium versions end user license agreement (EULA) - see the [LICENSE.md](LICENSE.md) file for details
