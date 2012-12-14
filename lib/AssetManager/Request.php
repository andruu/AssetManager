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

class Request {

  protected $config;

  public function __construct (Array $config = []) {
    $this->config = $config;
  }

  public function route ($file_name) {
    if (preg_match("/\.js|\.css$/", $file_name, $matches)) {
      $type = substr($matches[0], 1);
      $processorClassName = "AssetManager\\Processor\\" . ucfirst($type) . 'Processor';
      $processor = new $processorClassName($file_name, $type, $this->config);
      $processor->process();
      $processor->output();
    }
  }

}