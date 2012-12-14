<?php 
/*
 * This file is part of the AssetManager package.
 *
 * (c) Andrew Weir <andru.weir@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AssetManager\Processor;

/**
 * Processor class to manage reading/write assets and their directories
 *
 * @package eGloo/AssetManager
 * @author Andrew Weir <andru.weir@gmail.com>
 **/
class Processor {

  /**
   * Contains file, file_path and extension
   *
   * @var array
   **/
  protected $file;

  /**
   * Name of the file including extension
   *
   * @var string
   **/
  protected $file_name;

  /**
   * Name of file excluding extension
   *
   * @var string
   **/  
  protected $file_name_no_ext;

  /**
   * Configuration
   *
   * @var array
   **/
  protected $config;
  
  /**
   * Type of asset (css/js)
   *
   * @var string
   **/
  protected $type;

  /**
   * Path to assets in application
   *
   * @var string
   **/  
  protected $assets_path;

  /**
   * Sub directory(ies) of file
   *
   * @var string
   **/  
  protected $sub_directory;

  /**
   * Constructor sets up instance variables
   *
   * @param string $file_name Name of file
   * @param string $type Type of file
   * @param array $config Configuration
   * @return void
   **/
  public function __construct ($file_name, $type, Array $config = []) {

    $this->original_file_name = $file_name;

    // Check if file_name contains directory separator
    if (preg_match('/\//', $file_name)) {
      $pieces = explode('/', $file_name);
      $file_name = $pieces[count($pieces) - 1];
      foreach (range(0,count($pieces) - 2) as $c) {
        $this->sub_directory .= DS . $pieces[$c];
        $config['assets'][$type] .= DS . $pieces[$c];
      }
    }

    $this->extensions       = $config['order_of_importance'][$type];
    $this->file_name        = $file_name;
    $this->file_name_no_ext = substr($this->file_name, 0, -((int) strlen($type) + 1));
    $this->type             = $type;
    $this->config           = $config;
    $this->assets_path      = $this->config['assets'][$this->type];

    $this->findFile();

  }

  /**
   * Finds file in asset path
   *
   * @return void
   **/
  public function findFile () {
    $possible_files = array_map(function ($ext) {
      return $this->file_name_no_ext . '.' . $ext;
    }, $this->extensions);

    foreach ($this->readDir() as $file) {
      if (in_array($file, $possible_files)) {
        $extension = explode('.', $file)[count(explode('.', $file)) - 1];
        $this->file = ['file' => $file, 'file_path' => $this->assets_path . DS . $file, 'extension' => $extension];
        break;
      }
    }
  }

  /**
   * Reads contents of file
   *
   * @return string Contents of file before compilation
   **/
  public function fileContents () {
    return file_get_contents($this->file['file_path']);
  }

  /**
   * Writes file to public path
   *
   * @return void
   **/
  public function output () {
    
    // Figure out public directory
    if ($this->sub_directory) {
      $out_dir = $this->config['public'][$this->type] . $this->sub_directory;
    } else {
      $out_dir = $this->config['public'][$this->type];
    }
    
    // Create public directory if it doesn't exist
    self::makeDir($out_dir);
    
    // Write file to directory
    file_put_contents($out_dir . DS . $this->file_name, $this->compiled_file_contents);
  }

  /**
   * Writes concatinated file to public path
   *
   * @param array $assets Array of assets
   * @param string $concat_file_name MD5 has of assets array
   * @return void
   **/
  public static function outputContat (Array $assets = [], $concat_file_name) {
    $file_contents = '';
    $type = $assets[0]->type;

    foreach ($assets as $asset) {
      $asset->process();
      $file_name = str_replace($type, $asset->file['extension'], $asset->original_file_name);
      $file_contents .= "/* {$file_name} */\n";
      $file_contents .= $asset->compiled_file_contents . "\n";
    }

    // Create public directory if it doesn't exist
    $out_dir = $assets[0]->config['public'][$type];
    self::makeDir($out_dir);

    file_put_contents($out_dir . DS . $concat_file_name . '.' . $type, $file_contents);
  }

  /**
   * Recursively make directory
   *
   * @param string $dir Directory to make
   * @return void
   * @author 
   **/
  public static function makeDir ($dir) {
    if (!is_dir($dir)) {
      mkdir($dir, 0777, true);
    }
  }

  /**
   * Finds files in asset path excluding '.' and '..'
   *
   * @return array Files in asset path
   **/
  public function readDir () {
    $handle = opendir($this->assets_path);
    $files_in_dir = [];
    while (false !== ($file = readdir($handle))) {
      if($file !== '.' && $file !== '..' && !in_array($file, $files_in_dir)) { 
        array_push($files_in_dir, $file);
      }
    }
    return $files_in_dir;
  }
}