<?php

include_once 'vendor/autoload.php';

// Include our test models.
$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->addPsr4("Test\\Models\\", 'test/Models', true);
$classLoader->register();
