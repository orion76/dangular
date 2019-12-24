<?php

namespace Drupal\dorion_attributes\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dorion_attributes\Entity\DorionAttributes;
use Drupal\dorion_attributes\Entity\DorionAttributesInterface;

/**
 * Defines an interface for Dorion config plugin plugins.
 */
interface DorionAttributesPluginInterface extends PluginInspectionInterface {
  public function getLabel();

  public function form(DorionAttributesInterface $entity,$form, FormStateInterface $form_state);

  public function view();

  public function updateAttributes($element_name, $attributes);

  public function getDefaultConfig();

  public function getConfiguration();
}
