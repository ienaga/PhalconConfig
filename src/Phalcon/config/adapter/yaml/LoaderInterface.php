<?php

namespace Phalcon\Config\Adapter\Yaml;

interface LoaderInterface
{
    /**
     * @return \Phalcon\Config
     */
    public function load();
}