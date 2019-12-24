<?php


namespace Drupal\dorion_bem\Config;


interface ConfigBemBlockInterface extends ConfigBemItemInterface {

  public function __construct($config);

  public function addElement($elem, $mod = []);

  public function hasElement($elem);

  public function getElement($elem);
}
