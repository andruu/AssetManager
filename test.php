<?php 
define('DS', DIRECTORY_SEPARATOR);
define('ENVIRONMENT', 'DEVELOPMENT');
define('THEME', 'PetFlow');

require_once 'vendor/autoload.php';
require_once 'lib/AssetManager/Request.php';
require_once 'lib/AssetManager/Processor/Processor.php';
require_once 'lib/AssetManager/Processor/CssProcessor.php';
require_once 'lib/AssetManager/Processor/JsProcessor.php';

// Yaml file maybe?
$assetManagerConfig = [
  'environment' => ENVIRONMENT,
  'orderOfImportance' => [
    'css' => ['css', 'less', 'scss', 'sass'],
    'js'  => ['js', 'coffee']
  ],
  'assets' => [
    'css' => __DIR__ . DS . 'Application.gloo' . DS . 'InterfaceBundles' . DS . THEME . DS . 'CSS',
    'js'  => __DIR__ . DS . 'Application.gloo' . DS . 'InterfaceBundles' . DS . THEME . DS . 'Javascript'
  ],
  'public' => [
    'css' => __DIR__ . DS . 'public' . DS . 'stylesheets',
    'js'  => __DIR__ . DS . 'public' . DS . 'javascripts'
  ]
];

$request = new AssetManager\Request($assetManagerConfig);

// These would be called via a script that catches js/css in the web server

// JavaScript
$request->route('products/index.js');

// CoffeeScript -> JavaScript
$request->route('application.js');

// Less -> CSS
$request->route('application.css');

// CSS
$request->route('products/index.css');

// Sass -> CSS
$request->route('products/new.css');

// SCSS -> CSS
$request->route('products/show.css');





