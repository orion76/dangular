<?php


namespace Drupal\dorion_bem\Config;


interface ConfigBemItemInterface {

  public function getBlock();

  public function getMod();

  public function getExtraClasses();

  public function getClasses(): array;

}

