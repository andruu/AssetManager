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

class Processor {

  protected $file;
  protected $file_name;
  protected $file_name_no_ext;
  protected $config;
  protected $type;
  protected $assets_path;
  protected $subDirectory;

  public function __construct ($file_name, $type, Array $config = []) {

    // Check if file_name contains directory separator
    if (preg_match('/\//', $file_name)) {
      $pieces = explode('/', $file_name);
      $file_name = $pieces[count($pieces) - 1];
      foreach (range(0,count($pieces) - 2) as $c) {
        $this->subDirectory .= DS . $pieces[$c];
        $config['assets'][$type] .= DS . $pieces[$c];
      }
    }

    $this->extensions       = $config['orderOfImportance'][$type];
    $this->file_name        = $file_name;
    $this->file_name_no_ext = substr($this->file_name, 0, -((int) strlen($type) + 1));
    $this->type             = $type;
    $this->config           = $config;
    $this->assets_path      = $this->config['assets'][$this->type];

    $this->findFile();

  }

  public function findFile () {
    $possible_files = array_map(function ($ext) {
      return $this->file_name_no_ext . '.' . $ext;
    }, $this->extensions);

    foreach ($this->read_dir() as $file) {
      if (in_array($file, $possible_files)) {
        $extension = explode('.', $file)[count(explode('.', $file)) - 1];
        $this->file = ['file' => $file, 'file_path' => $this->assets_path . DS . $file, 'extension' => $extension];
        break;
      }
    }
  }

  public function fileContents () {
    return file_get_contents($this->file['file_path']);
  }

  public function output () {
    
    // Figure out public directory
    if ($this->subDirectory) {
      $out_dir = $this->config['public'][$this->type] . $this->subDirectory;
    } else {
      $out_dir = $this->config['public'][$this->type];
    }
    
    // Create public directory if it doesn't exist
    if (!is_dir($out_dir)) {
      mkdir($out_dir, 0777, true);
    }
    
    // Write file to directory
    file_put_contents($out_dir . DS . $this->file_name, $this->compiledFileContents);
  }

  public function read_dir () {
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