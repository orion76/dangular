<?php

namespace Drupal\dorion_attributes\Entity\Handlers;

use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\dorion_attributes\Entity\DorionAttributesInterface;

/**
 * Controller class for user roles.
 */
class DorionAttributesStorage extends ConfigEntityStorage implements DorionAttributesStorageInterface {

//  public function view(DorionAttributesInterface $entity) {
//    $id = $this->doPreSave($entity);
//    $prefix = $this->getPrefix();
//    $config_name = $prefix . $entity->id();
//    $config = $this->configFactory->getEditable($config_name);
//    $config->setData($this->mapToStorageRecord($entity));
//    $n=0;
//  }
}
