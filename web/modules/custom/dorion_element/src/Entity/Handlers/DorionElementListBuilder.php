<?php

namespace Drupal\dorion_element\Entity\Handlers;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use function uasort;

/**
 * Provides a listing of Dorion config entities.
 */
class DorionElementListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Dorion config');
    $header['id'] = $this->t('Machine name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);
    return $operations;
  }
}
