<?php


namespace Drupal\dorion_bem\Config;


use Drupal\dorion_bem\BemException;
use function array_keys;
use function implode;
use function t;

class ConfigBemBlock extends ConfigBemItem implements ConfigBemBlockInterface {


  protected $elements = [];


  public function __construct($config) {
    parent::__construct($config);
  }

  public function addElement($elem, $mod = []) {
    $config = [
      'block' => $this->block,
      'elem' => $elem,
      'mod' => $mod,
    ];
    $this->elements[$elem] = new ConfigBemElement($config);
  }

  public function hasElement($elem) {
    return isset($this->elements[$elem]);
  }

  /**
   * @param $elem
   *
   * @return mixed
   * @throws \Drupal\dorion_bem\BemException
   */
  public function getElement($elem) {

    if (!isset($this->elements[$elem])) {
      throw new BemException(t('Element [@elem] in [@block] not found, exists elements: @elements', [
        '@elem' => $elem,
        '@block' => $this->block,
        '@elements' => implode(', ', array_keys($this->elements)),
      ]));
    }

    return $this->elements[$elem];
  }

  protected function getBaseClass() {
    return $this->bem['block'];
  }


}
