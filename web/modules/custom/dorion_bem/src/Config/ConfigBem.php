<?php


namespace Drupal\dorion_bem\Config;


use Drupal\dorion_bem\PathArray;

class ConfigBem extends PathArray implements ConfigBemInterface {

  protected $config;

  public function __construct() {
    parent::__construct([]);
  }


  public function addItem($path, $conf): ConfigBemInterface {
    $type = $conf['type'];
    $item = NULL;
    switch ($type) {
      case 'block':
        $item = new ConfigBemBlock($conf);
        break;
      case 'element':
        $item = new ConfigBemElement($conf);
        break;
    }
    $this[$path][] = $item;
    return $this;
  }



}
