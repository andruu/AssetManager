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
 * CssProcessor class to manage the compilation of CSS assets
 *
 * @package eGloo/AssetManager
 * @author Andrew Weir <andru.weir@gmail.com>
 **/
class CssProcessor extends Processor {

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
      case 'less':
        $less = new \lessc;
        $output = $less->compile($this->fileContents());
        break;
      case 'sass':
        $sass = new \SassParser;
        $output = $sass->toCss($this->file['file_path']);
        break;
      case 'scss':
        $sass = new \SassParser;
        $output = $sass->toCss($this->file['file_path']);
        break;
      case 'css':
        $output = $this->fileContents();
        break;
    }
    $this->compiled_file_contents = $output;
  }
}