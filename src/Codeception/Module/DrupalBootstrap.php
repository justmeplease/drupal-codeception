<?php

namespace Codeception\Module;

use Codeception\Configuration;
use Codeception\Lib\ModuleContainer;
use Codeception\Module;
use Codeception\TestDrupalKernel;
use Symfony\Component\HttpFoundation\Request;
use DrupalFinder\DrupalFinder;


/**
 * Class DrupalBootstrap.
 *
 * ### Example
 * #### Example (DrupalBootstrap)
 *     modules:
 *        - DrupalBootstrap:
 *          root: './web'
 *          site_path: 'sites/default'
 *          http_host: 'mysite.local'
 *
 * @package Codeception\Module
 */
class DrupalBootstrap extends Module {

  /**
   * Default module configuration.
   *
   * @var array
   */
  protected array $config = [
    'site_path' => 'sites/default',
  ];

  /**
   * @return void
   */
  public function _initialize() : void {

    if (!isset($this->config['root'])) {

      $drupalFinder = new DrupalFinder();

      $drupalFinder->locateRoot(getcwd());
      $drupalRoot = $drupalFinder->getDrupalRoot();

      // Autodetect Drupal root.
      if ($drupalRoot) {
        $this->_setConfig(['root' => $drupalRoot]);
      }
      else {
        $this->_setConfig(['root' => Configuration::projectDir() . 'web']);
      }
    }
    chdir($this->_getConfig('root'));
    if (isset($this->config['http_host'])) {
      $_SERVER['HTTP_HOST'] = $this->config['http_host'];
    }
    $request = Request::createFromGlobals();
    $autoloader = require $this->_getConfig('root') . '/autoload.php';
    $kernel = new TestDrupalKernel('prod', $autoloader, $this->_getConfig('root'));
    $kernel->bootTestEnvironment($this->_getConfig('site_path'), $request);
  }

}
