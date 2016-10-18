<?php


require_once __DIR__ . "../src/config/Loader.php";


class LoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * main test load
     */
    public function testLoad()
    {
        $env     = "dev";
        $loader  = new \PhalconConfig\Loader();
        $results = $loader
            ->setIgnore(array("ignore"))
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
        $loader  = new \PhalconConfig\Loader();
        $results = $loader
            ->setIgnore(array("ignore"))
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
        $loader  = new \PhalconConfig\Loader();
        $results = $loader
            ->setIgnore(array("ignore"))
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
}