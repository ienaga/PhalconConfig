<?php


namespace PhalconConfig;


class Loader
{

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
    public function setEnvironment($environment)
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
        $config = new \Phalcon\Config();

        // path
        $basePath = $this->getBasePath();
        $appPath  = $basePath . "/app";

        // environment
        $env = $this->getEnvironment();

        // load
        if ($dir = opendir($appPath ."/config")) {

            while (($file = readdir($dir)) !== false) {

                $ext = explode(".", $file);
                if ($ext[1] !== "yml" || in_array($ext[0], $this->ignore)) {
                    continue;
                }

                $yml =  new \Phalcon\Config\Adapter\Yaml($appPath ."/config/". $file, [
                    '!app_path' => function($value) use ($appPath) {
                        return $appPath . $value;
                    },
                    '!base_path' => function($value) use ($basePath) {
                        return $basePath . $value;
                    }
                ]);

                if ($env && $yml->get($env)) {
                    $config->merge($yml->get($env));
                }

                if ($yml->get("all")) {
                    $config->merge($yml->get("all"));
                }
            }

            closedir($dir);
        }

        return $config;
    }
}