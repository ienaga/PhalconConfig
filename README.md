# Phalcon Config Loarder for Yaml


[![Build Status](https://travis-ci.org/ienaga/PhalconConfig.svg?branch=master)](https://travis-ci.org/ienaga/PhalconConfig)


[![Latest Stable Version](https://poser.pugx.org/ienaga/phalcon-config-loader-for-yaml/v/stable)](https://packagist.org/packages/ienaga/phalcon-config-loader-for-yaml) [![Total Downloads](https://poser.pugx.org/ienaga/phalcon-config-loader-for-yaml/downloads)](https://packagist.org/packages/ienaga/phalcon-config-loader-for-yaml) [![Latest Unstable Version](https://poser.pugx.org/ienaga/phalcon-config-loader-for-yaml/v/unstable)](https://packagist.org/packages/ienaga/phalcon-config-loader-for-yaml) [![License](https://poser.pugx.org/ienaga/phalcon-config-loader-for-yaml/license)](https://packagist.org/packages/ienaga/phalcon-config-loader-for-yaml)


Loads all the `yml` in the directory of the `app/config`.


## Composer

```json
{
    "require": {
       "ienaga/phalcon-config-loader-for-yaml": "*"
    }
}
```


## BASE_PATH and APP_PATH

```yaml
all:
  application:
    appDir:         !app_path  /
    controllersDir: !app_path  /controllers/
    modelsDir:      !app_path  /models/
    migrationsDir:  !app_path  /migrations/
    viewsDir:       !app_path  /views/
    pluginsDir:     !app_path  /plugins/
    libraryDir:     !app_path  /library/
    cacheDir:       !base_path /cache/
    baseUri:        /project_name/

dev:
  image: "https://dev.example.com"
  
stg:
  image: "https://stgexample.com"
  
prd:
  image: "https://example.com"
  
test:
  image: "https://test.example.com"
  
local:
  image: "https://local.example.com"
```


## app/config/config.php

```php
$loader = new Phalcon\Config\Adapter\Yaml\Loader();
return $loader
    ->setIgnore(["routing"]) // ignore yml names
    ->setEnvironment("stg") // default dev
    ->setBasePath(realpath(dirname(__FILE__) . "/../.."))
    ->load();
```

