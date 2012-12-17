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
 * General methods to assist in asset management
 *
 * @package eGloo/AssetManager
 * @author Andrew Weir <andru.weir@gmail.com>
 **/
class AssetManager {

  /**
   * Configuration
   *
   * @var array
   **/
  protected static $config;

  /**
   * Initialize static class
   *
   * @param array $config Configeration
   * @return void
   **/
  public static function init (Array $config = []) {
    self::$config = $config;
  }

  /**
   * Remove generated files in public assets directories
   *
   * @return void
   **/
  public static function clearCache () {

    foreach (self::$config['public'] as $assets_dir) {
      self::removeFiles($assets_dir);
    }

  }

  /**
   * Remove files from directories
   *
   * @param string $dir Directory
   * @return void
   **/
  public static function removeFiles ($dir, $dirs = []) {
      // Temp store for directories 
      $dirs[] = $dir;

      // Remove all files
      $files = Processor\Processor::readDir($dir);
      foreach ($files as $file) {
        $file = $dir . DS . $file;
        if (is_dir($file)) {
          self::removeFiles($file);
        } else {
          unlink($file);
        }
      }

      // Remove all directories
      foreach ($dirs as $_dir) {
        if (is_dir($_dir)) {
          rmdir($_dir);
        }
      }
  }

}