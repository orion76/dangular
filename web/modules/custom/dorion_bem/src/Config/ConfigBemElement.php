<?php


namespace Drupal\dorion_bem\Config;


class ConfigBemElement extends ConfigBemItem implements ConfigBemElementInterface {

  public function __construct($config) {
    parent::__construct($config);
  }

  protected function getBaseClass() {
    return "{$this->bem['block']}__{$this->bem['elem']}";
  }

  public function getElem() {
    return $this->bem['elem'];
  }

}
