<?php


namespace Drupal\dorion_bem\Config;


use function array_merge;

abstract class ConfigBemItem {

  protected $bem;

  protected $extraClasses = [];

  public function __construct($config) {

    $this->bem = $config['bem'];

    if (!isset($this->bem['mod'])) {
      $this->bem['mod'] = [];
    }
    if (isset($config['classes'])) {
      $this->extraClasses = $config['classes'];
    }

  }

  public function getBlock() {
    return isset($this->bem['block']) ? $this->bem['block'] : NULL;
  }


  public function getMod() {
    return isset($this->bem['mod']) ? $this->bem['mod'] : NULL;
  }

  public function getExtraClasses() {
    return $this->extraClasses;
  }

  abstract protected function getBaseClass();

  protected function getModifierClasses() {
    $classes = [];
    $base_class = $this->getBaseClass();
    foreach ($this->bem['mod'] as $modifier) {
      $classes[] = "{$base_class}--{$modifier}";
    }
    return $classes;
  }

  public function getClasses(): array {

    $classes = [$this->getBaseClass()];
    $modifier_classes = $this->getModifierClasses();
    $extra_classes = $this->getExtraClasses();
    $result = array_merge($classes, $modifier_classes, $extra_classes);
    return $result;
  }

}
