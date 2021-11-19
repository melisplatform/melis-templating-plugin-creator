# Melis Templating Plugin Creator

Generates a ready-to-use templating plugin, complete with source code and necessary configuration. This will aid the developers, especially the new developers of the platform, to swiftly create a plugin without delving deeply into plugin's technicalities.

## Getting started

These instructions will get you a copy of the project up and running on your machine.

### Prerequisites

The following modules need to be installed to run the Melis Templating Plugin Creator module:

- Melis Core
- Melis Tool Creator

### Installing

Run the composer command:

```
composer require melisplatform/melis-templating-plugin-creator
```

Go to /melis-templating-plugin-creator/public/ and change the file owner of the 'temp-thumbnail' directory to 'www-data'.  This is where the plugin thumbnails are temporarily saved.

```
chown www-data temp-thumbnail
```

### Database

No database is needed for this tool


## Tools and elements provided

- Templating Plugin Creator Tool
- Templating Plugin Creator Service

### Templating Plugin Creator Tool

  - user must specify the destination of the generated plugin: new module or existing site module
  - the properties of the plugin need to be set(field name, display type, default options, default value and required attribute) as well as their translations which will then be used in the plugin modal form
  - the 'Template' property is already set by default 
  - after generation, the source code can be found inside the destination module ready to be updated based on the project's requirements
  - the generated plugin by default, will be shown under the 'Others' section in the Plugins menu inside the Page Edition

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
