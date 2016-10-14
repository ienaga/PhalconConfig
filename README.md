# Phalcon Config Loarder for Yaml



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
```

## app/config/config.php

```php
$configLoader = \PhalconConfig\Loader($ignore);
return $configLoader
    ->setIgnore(["routing"]) // ignore yml names
    ->setEnvironment("stg")
    ->setBasePath(BATH_PATH)
    ->load();
```


