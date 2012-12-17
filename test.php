<?php 

putenv('LAST_DEPLOYMENT_TIMESTAMP=2012_12_12-12:03:32');

define('DS', DIRECTORY_SEPARATOR);
// define('ENVIRONMENT', 'DEVELOPMENT');
define('ENVIRONMENT', 'PRODUCTION');
define('THEME', 'PetFlow');

require_once 'vendor/autoload.php';
require_once 'lib/ViewHelpers/AssetHelper.php';
require_once 'lib/AssetManager/AssetManager.php';
require_once 'lib/AssetManager/Request.php';
require_once 'lib/AssetManager/Processor/Processor.php';
require_once 'lib/AssetManager/Processor/CssProcessor.php';
require_once 'lib/AssetManager/Processor/JsProcessor.php';

$assets_dir = __DIR__ . DS . 'Application.gloo' . DS . 'InterfaceBundles' . DS . THEME;

// Yaml file maybe?
$asset_config = [
  'environment' => ENVIRONMENT,
  'cdn' => [
    'DEVELOPMENT' => '/',
    'STAGING' => '/',
    'PRODUCTION' => '/',
  ],
  'order_of_importance' => [
    'css' => ['css', 'less', 'scss', 'sass'],
    'js'  => ['js', 'coffee']
  ],
  'assets' => [
    'css' => $assets_dir . DS . 'CSS',
    'js'  => $assets_dir . DS . 'Javascript'
  ],
  'public' => [
    'css' => __DIR__ . DS . 'public' . DS . 'stylesheets',
    'js'  => __DIR__ . DS . 'public' . DS . 'javascripts'
  ],
  'web' => [
    'css' => 'stylesheets',
    'js'  => 'javascripts'
  ]
];

// Remove generated files in public
// AssetManager\AssetManager::init($asset_config);
// AssetManager\AssetManager::clearCache();

// die;

$asset_helper = new ViewHelpers\AssetHelper($asset_config);

$asset_helper->js_include('application.js');
// $asset_helper->js_include('products/index.js');
$asset_helper->css_include('products/new.css');
// $asset_helper->css_include(['products/show.css', 'products/new.css']);

echo $asset_helper->css_tag();
echo $asset_helper->js_tag();


// die;

// $request = new AssetManager\Request($asset_config);

// These would be called via a script that catches js/css in the web server

// Handle multiple assets
// $request->route(['application.css', 'products/new.css', 'products/index.css', 'products/show.css']);
// $request->route(['application.js', 'products/index.js']);

// {{ assets.js_include(['application.js', 'products/index.js']) }}
// {{ assets.js_tag() }}
// <script src="/javascripts/8923674623467832674678234_08787893.js"></script>

// // JavaScript
// $request->route('products/index.js');

// // CoffeeScript -> JavaScript
// $request->route('application.js');

// // Less -> CSS
// $request->route('application.css');

// // CSS
// $request->route('products/index.css');

// // Sass -> CSS
// $request->route('products/new.css');

// // SCSS -> CSS
// $request->route('products/show.css');





