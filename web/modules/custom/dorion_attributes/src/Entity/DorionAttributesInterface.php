<?php

namespace Drupal\dorion_attributes\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Dorion config entities.
 */
interface DorionAttributesInterface extends ConfigEntityInterface {

  public function getPluginType();
  public function view();
  public function getPlugin() ;
  public function getConfig();
}
