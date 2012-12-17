<?php
/*
 * This file is part of the AssetManager package.
 *
 * (c) Andrew Weir <andru.weir@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ViewHelpers;

/**
 * undocumented class
 *
 * @package default
 * @author 
 **/
class AssetHelper {

  private $config;
  private $js_assets = [];
  private $css_assets = [];

  private $js_template = '<script src="__PATH__" type="text/javascript"></script>';
  private $css_template = '<link href="__PATH__" media="all" rel="stylesheet" type="text/css" />';

  public function __construct (Array $config = []) {
    $this->config = $config;
  }

  public function js_include ($includes) {
    $this->__include($includes, 'js');
  }

  public function css_include ($includes) {
    $this->__include($includes, 'css'); 
  }

  private function __include ($includes, $type) {
    $asset_type = "{$type}_assets";
    if (is_array($includes)) {
      foreach ($includes as $include) {
        $this->{$asset_type}[] = $include;
      }
    } else {
      $this->{$asset_type}[] = $includes;
    }
  }

  public function js_tag () {
    return $this->__tag('js');
  }

  public function css_tag () {
    return $this->__tag('css');
  }

  private function __tag ($type) {
    $asset_type = "{$type}_assets";
    $template = "{$type}_template";
    $request = new \AssetManager\Request($this->config);
    
    // Throw exception if no assets have been added
    if (empty($this->{$asset_type})) {
      throw new \Exception("No css assets have been added.", 1);
    }

    // Check how many files in array
    if (count($this->{$asset_type}) == 1) {
      $file_name = $request->route($this->{$asset_type}[0]);
    } else {
      $file_name = $request->route($this->{$asset_type});
    }

    $path = $this->config['cdn'][$this->config['environment']] . $this->config['web'][$type] . DS . $file_name;

    return str_replace('__PATH__', $path, $this->{$template});
  }
}










