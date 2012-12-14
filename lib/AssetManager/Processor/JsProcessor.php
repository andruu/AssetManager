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
 * JsProcessor class to manage the compilation of JavaScript assets
 *
 * @package eGloo/AssetManager
 * @author Andrew Weir <andru.weir@gmail.com>
 **/
class JsProcessor extends Processor {

  /**
   * Contents of compiled file
   *
   * @var string
   **/
  protected $compiled_file_contents;

  /**
   * Compiles and processes asset
   *
   * @return void
   **/
  public function process () {
    switch ($this->file['extension']) {
      case 'coffee':
        $output = \CoffeeScript\Compiler::compile($this->fileContents(), ['bare' => true, 'header' => false]);
        break;
      case 'js':
        $output = $this->fileContents();
        break;
    }
    $this->compiled_file_contents = $output;
  }
}