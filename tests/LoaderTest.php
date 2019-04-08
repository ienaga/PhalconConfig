<?php

require_once __DIR__ . "/../src/Phalcon/config/adapter/yaml/Loader.php";

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * main test load
     */
    public function testLoad()
    {
        $env     = "dev";
        $loader  = new Phalcon\Config\Adapter\Yaml\Loader();
        $results = $loader
            ->setIgnore(array("ignore", "parent", "child"))
            ->setEnvironment($env)
            ->setBasePath(realpath(dirname(__FILE__)))
            ->load();

        // environment
        $this->assertArrayHasKey($env, $results);

        // all
        $this->assertArrayHasKey("application", $results);

        // ignore
        $this->assertEmpty($results["ignore"]);
    }

    /**
     * empty dir test
     */
    public function testEmptyDirectoryCase()
    {
        $loader  = new Phalcon\Config\Adapter\Yaml\Loader();
        $results = $loader
            ->setIgnore(array("ignore", "parent", "child"))
            ->setEnvironment("dev")
            ->setBasePath("/empty/dir/")
            ->load();

        $this->assertEmpty($results);
    }

    /**
     * empty environment test
     */
    public function testEmptyEnvironmentCase()
    {
        $loader  = new Phalcon\Config\Adapter\Yaml\Loader();
        $results = $loader
            ->setIgnore(array("ignore", "parent", "child"))
            ->setEnvironment("empty")
            ->setBasePath(realpath(dirname(__FILE__)))
            ->load();

        // environment
        $this->assertEmpty($results["dev"]);

        // all
        $this->assertArrayHasKey("application", $results);

        // ignore
        $this->assertEmpty($results["ignore"]);
    }

    /**
     * test add
     */
    public function testAdd()
    {
        $loader  = new Phalcon\Config\Adapter\Yaml\Loader();
        $parent = $loader
            ->setIgnore(array("ignore", "app", "child"))
            ->setEnvironment("test")
            ->setBasePath(realpath(dirname(__FILE__)))
            ->load();

        $this->assertEquals($parent["mode"], "parent");
        $this->assertEquals($parent["mysql"]["port"], 3306);

        $pattern = array(0, 1, 2, 3);
        foreach ($parent["list"] as $key => $value) {
            $this->assertEquals($parent["list"][$key], $pattern[$key]);
        }

        $child = $loader
            ->setIgnore(array("ignore", "app", "parent"))
            ->setEnvironment(getenv("ENVIRONMENT"))
            ->setBasePath(realpath(dirname(__FILE__)))
            ->add($parent, realpath(dirname(__FILE__)). "/app/config");

        $this->assertEquals($child["mode"], "child");
        $this->assertEquals($child["mysql"]["port"], 3316);

        $pattern = array(10, 11, 12, 13);
        foreach ($child["list"] as $key => $value) {
            $this->assertEquals($child["list"][$key], $pattern[$key]);
        }
    }
}