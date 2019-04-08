<?php

namespace Phalcon\Config\Adapter\Yaml;

use Phalcon\Config;
use Phalcon\Config\Adapter\Yaml;

class Loader implements LoaderInterface
{

    /**
     * @var string
     */
    const ALL = "all";

    /**
     * @var array
     */
    private $_ignore = array();

    /**
     * @var string
     */
    private $_environment = "dev";

    /**
     * @var string
     */
    private $_basePath = "/";

    /**
     * @return array
     */
    public function getIgnore(): array
    {
        return $this->_ignore;
    }

    /**
     * @param  array $ignore
     * @return Loader
     */
    public function setIgnore(array $ignore = array()): Loader
    {
        $this->_ignore = $ignore;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->_environment;
    }

    /**
     * @param  string $environment
     * @return Loader
     */
    public function setEnvironment(string $environment = "dev"): Loader
    {
        $this->_environment = $environment;
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->_basePath;
    }

    /**
     * @param  string $base_path
     * @return Loader
     */
    public function setBasePath(string $base_path = "/"): Loader
    {
        $this->_basePath = $base_path;
        return $this;
    }

    /**
     * @return Config
     * @throws Config\Exception
     */
    public function load()
    {
        // config
        $config = new Config();

        // path
        $basePath   = $this->getBasePath();
        $appPath    = $basePath . "/app";
        $configPath = $appPath  . "/config/";

        // dir valid
        if (!file_exists($appPath) || !file_exists($configPath)) {
            return $config;
        }

        // environment
        $env = $this->getEnvironment();

        // load
        if ($dir = opendir($configPath)) {

            while (($file = readdir($dir)) !== false) {

                $ext = explode(".", $file);
                if ($ext[1] !== "yml" || in_array($ext[0], $this->getIgnore())) {
                    continue;
                }

                $yml = new Yaml($configPath . $file, array(
                    "!app_path"  => function($value) use ($appPath)  {
                        return $appPath . $value;
                    },
                    "!base_path" => function($value) use ($basePath) {
                        return $basePath . $value;
                    }
                ));

                // merge environment
                if ($env && $yml->get($env)) {
                    $config->merge($yml->get($env));
                }

                // merge all
                if ($yml->get(self::ALL)) {
                    $config->merge($yml->get(self::ALL));
                }
            }
            closedir($dir);
        }

        return $config;
    }

    /**
     * @param  Config $config
     * @param  $addDirPath
     * @return Config
     * @throws Config\Exception
     */
    public function add(Config $config, $addDirPath)
    {
        // child config
        $childConfig = new Config();

        // path
        $basePath   = $this->getBasePath();
        $appPath    = $basePath . "/app";
        $configPath = $appPath  . "/config/";

        // dir valid
        if (!file_exists($appPath) || !file_exists($configPath)) {
            return $config;
        }

        // environment
        $env = $this->getEnvironment();

        // add end slash
        if (mb_substr($addDirPath, -1) !== "/") {
            $addDirPath .= "/";
        }

        // load
        if ($dir = opendir($addDirPath)) {

            while (($file = readdir($dir)) !== false) {

                $ext = explode(".", $file);
                if (!isset($ext[1]) ||
                    $ext[1] !== "yml" ||
                    in_array($ext[0], $this->getIgnore())
                ) {
                    continue;
                }

                $yml = new Yaml($addDirPath . $file, array(
                    "!app_path"  => function($value) use ($appPath)  {
                        return $appPath . $value;
                    },
                    "!base_path" => function($value) use ($basePath) {
                        return $basePath . $value;
                    }
                ));

                // merge environment
                if ($env && $yml->get($env)) {
                    $childConfig->merge($yml->get($env));
                }

                // merge all
                if ($yml->get(self::ALL)) {
                    $childConfig->merge($yml->get(self::ALL));
                }
            }
            closedir($dir);
        }

        $this->_unset($config, $childConfig);
        return $config->merge($childConfig);
    }

    /**
     * @param Config $parent
     * @param Config $child
     */
    protected function _unset(Config $parent, Config $child)
    {
        foreach ($child as $key => $value) {

            if (!$parent->get($key)) {
                continue;
            }

            $config = $parent->get($key);
            if ($value  instanceof Config && $config instanceof Config) {
                $this->_unset($config, $value);
                continue;
            }

            if (!is_numeric($key)) {
                continue;
            }

            foreach ($parent as $idx => $val) {
                unset($parent->{$idx});
            }

        }
    }
}