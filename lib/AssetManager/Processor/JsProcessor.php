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

class JsProcessor extends Processor {

  protected $compiledFileContents;

  public function process () {
    switch ($this->file['extension']) {
      case 'coffee':
        $output = \CoffeeScript\Compiler::compile($this->fileContents(), ['bare' => true]);
        break;
      case 'js':
        $output = $this->fileContents();
        break;
    }
    $this->compiledFileContents = $output;
  }
}