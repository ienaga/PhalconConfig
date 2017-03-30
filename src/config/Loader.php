<?php

namespace PhalconConfig;

class Loader implements LoaderInterface
{

    /**
     * @var string
     */
    const ALL = "all";

    /**
     * @var array
     */
    private $ignore = array();

    /**
     * @var string
     */
    private $environment = "dev";

    /**
     * @var string
     */
    private $base_path = "/";

    /**
     * @return array
     */
    public function getIgnore()
    {
        return $this->ignore;
    }

    /**
     * @param  array $ignore
     * @return $this
     */
    public function setIgnore($ignore = array())
    {
        $this->ignore = $ignore;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param  string $environment
     * @return $this
     */
    public function setEnvironment($environment = "dev")
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->base_path;
    }

    /**
     * @param  string $base_path
     * @return $this
     */
    public function setBasePath($base_path = "/")
    {
        $this->base_path = $base_path;
        return $this;
    }

    /**
     * @return \Phalcon\Config
     */
    public function load()
    {
        // config
        $config = new \Phalcon\Config();

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

                $yml = new \Phalcon\Config\Adapter\Yaml($configPath . $file, array(
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
     * @param  \Phalcon\Config $config
     * @param  string          $addDirPath
     * @return \Phalcon\Config
     */
    public function add(\Phalcon\Config $config, $addDirPath)
    {
        // child config
        $childConfig = new \Phalcon\Config();

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
                if ($ext[1] !== "yml" || in_array($ext[0], $this->getIgnore())) {
                    continue;
                }

                $yml = new \Phalcon\Config\Adapter\Yaml($addDirPath . $file, array(
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
     * @param \Phalcon\Config $parent
     * @param \Phalcon\Config $child
     */
    protected function _unset(\Phalcon\Config $parent, \Phalcon\Config $child)
    {
        foreach ($child as $key => $value) {
            if (!$parent->get($key)) {
                continue;
            }

            $config = $parent->get($key);
            if ($value  instanceof \Phalcon\Config &&
                $config instanceof \Phalcon\Config
            ) {
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