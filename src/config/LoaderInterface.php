<?php

namespace PhalconConfig;

interface LoaderInterface
{
    /**
     * @return \Phalcon\Config
     */
    public function load();
}