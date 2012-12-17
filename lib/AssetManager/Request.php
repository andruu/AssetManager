<?php 
/*
 * This file is part of the AssetManager package.
 *
 * (c) Andrew Weir <andru.weir@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AssetManager;

/**
 * Request class to manage the routing of assets based on type
 *
 * @package eGloo/AssetManager
 * @author Andrew Weir <andru.weir@gmail.com>
 **/
class Request {

  /**
   * Configuration
   *
   * @var array
   **/
  protected $config;

  /**
   * Constructor sets up instance variables
   *
   * @param array $config Configuration
   * @return void
   **/
  public function __construct (Array $config = []) {
    $this->config = $config;
  }

  /**
   * Determine whether or not 1 or more assets are being routed
   *
   * @param string $file_name Name of file
   * @return void
   **/
  public function route ($file_name) {
    if (is_array($file_name)) {
      
      $concat_file_name = md5(serialize($file_name));
      
      $assets = [];
      foreach($file_name as $file) {
        $assets[] = $this->handleRoute($file, true, $concat_file_name);
      }

      Processor\Processor::outputContat($assets, $concat_file_name, $this->config['environment']);

    } else {
      $processor = $this->handleRoute($file_name);
      $processor->process();
      $processor->output();
    }
  }

  /**
   * Route assets based on file type
   *
   * @param string $file_name Name of file
   * @return object Processor Object containing asset properties
   **/
  public function handleRoute ($file_name) {
    if (preg_match("/\.js|\.css$/", $file_name, $matches)) {
      $type = substr($matches[0], 1);
      $processorClassName = "AssetManager\\Processor\\" . ucfirst($type) . 'Processor';
      return $processor = new $processorClassName($file_name, $type, $this->config);
    } else {
      throw new Exception("Error Processing Request", 1);
    }
  }

}