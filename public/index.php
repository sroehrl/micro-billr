<?php

use Neoan\Helper\Setup;
use Neoan\NeoanApp;

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$projectPath = dirname(__DIR__);
require_once $projectPath . '/vendor/autoload.php';

$pathToSourceFiles = $projectPath . '/src';
$publicPath = __DIR__;

$setup = new Setup();
$setup->setLibraryPath($pathToSourceFiles)
    ->setDefault404(dirname(__DIR__) .'/config/404.html')
    ->setPublicPath($publicPath);


$app = new NeoanApp($setup, dirname(__DIR__));

new \Config\Config($app);
// enable attribute routing
$namespaceToExploreRecursively = 'App';

$app->invoke(new Neoan\Routing\AttributeRouting($namespaceToExploreRecursively));



// run application
$app->run();
