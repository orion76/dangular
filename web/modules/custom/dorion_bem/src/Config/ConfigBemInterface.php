<?php


namespace Drupal\dorion_bem\Config;


interface ConfigBemInterface {

  public function addItem($path, $conf): ConfigBemInterface;

}
