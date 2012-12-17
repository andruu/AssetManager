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
        if (!in_array($include, $this->{$asset_type})) {
          $this->{$asset_type}[] = $include;
        }
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

    // Throw exception if no assets have been added
    if (empty($this->{$asset_type})) {
      throw new \Exception("No {$type} assets have been added.", 1);
    }

    // If in production return just the path
    if ($this->config['environment'] == "PRODUCTION") {
      
      // Check how many files in array
      if (count($this->{$asset_type}) == 1) {
        $file_name = $this->{$asset_type}[0];
        $file_name = substr($file_name, 0, -((int) strlen($type) + 1)) . '_' . getenv('LAST_DEPLOYMENT_TIMESTAMP') . '.' . $type;
      } else {
        $file_name = md5(serialize($this->{$asset_type})) . '_' . getenv('LAST_DEPLOYMENT_TIMESTAMP') . '.' . $type;
      }

    } else {

      // Compile new asset files
      $request = new \AssetManager\Request($this->config);

      // Check how many files in array
      if (count($this->{$asset_type}) == 1) {
        $file_name = $request->route($this->{$asset_type}[0]);
      } else {
        $file_name = $request->route($this->{$asset_type});
      }
    }

    $this->__write($type);

    $path = $this->config['cdn'][$this->config['environment']] . $this->config['web'][$type] . DS . $file_name;      
    return str_replace('__PATH__', $path, $this->{$template});
  }

  private function __write ($type) {

    $asset_type = "{$type}_assets";

    $compile_file = $this->config['compile_file'];
    
    // Create file if it doesn't exist
    if (!file_exists($compile_file)) {
      file_put_contents($compile_file, null);
    }

    // Open compile file and turn into PHP array
    $assets = json_decode(file_get_contents($compile_file), true);
    $assets[$type][] = $this->{$asset_type};

    // Clean duplicates
    $assets[$type] = array_map("unserialize", array_unique(array_map("serialize", $assets[$type])));

    // Write back to file
    file_put_contents($compile_file, json_format(json_encode($assets)));
  }
}

// Pretty print some JSON 
// @source http://www.php.net/manual/en/function.json-encode.php#80339
function json_format ($json) { 
    $tab = "  "; 
    $new_json = ""; 
    $indent_level = 0; 
    $in_string = false; 

    $json_obj = json_decode($json); 

    if($json_obj === false) 
        return false; 

    $json = json_encode($json_obj); 
    $len = strlen($json); 

    for($c = 0; $c < $len; $c++) 
    { 
        $char = $json[$c]; 
        switch($char) 
        { 
            case '{': 
            case '[': 
                if(!$in_string) 
                { 
                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1); 
                    $indent_level++; 
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case '}': 
            case ']': 
                if(!$in_string) 
                { 
                    $indent_level--; 
                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char; 
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case ',': 
                if(!$in_string) 
                { 
                    $new_json .= ",\n" . str_repeat($tab, $indent_level); 
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case ':': 
                if(!$in_string) 
                { 
                    $new_json .= ": "; 
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case '"': 
                if($c > 0 && $json[$c-1] != '\\') 
                { 
                    $in_string = !$in_string; 
                } 
            default: 
                $new_json .= $char; 
                break;                    
        } 
    } 
    return $new_json; 
} 






