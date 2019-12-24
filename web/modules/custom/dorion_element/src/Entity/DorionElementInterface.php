<?php

namespace Drupal\dorion_element\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\dorion_element\Plugin\DorionElementTypeInterface;

/**
 * Provides an interface for defining Dorion config entities.
 */
interface DorionElementInterface extends ConfigEntityInterface {

  public function getContext();

  public function getAttributes();

  public function getElementType();
  function getPlugin();

}
