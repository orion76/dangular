<?php


namespace Drupal\dorion_attributes\Plugin\DorionAttributesPlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\dorion_attributes\Entity\DorionAttributesInterface;
use Drupal\dorion_attributes\Plugin\DorionAttributesPluginBase;


/**
 * Class Form
 *
 * @DorionAttributesPlugin(
 *   id = "attributes_available",
 *   label = @Translation("Attributes available")
 * )
 */
class AttributesAvailable extends DorionAttributesPluginBase {

  public function form(DorionAttributesInterface $entity, $form, FormStateInterface $form_state) {
    return ['#markup' => 'AttributesAvailable'];
  }

  public function getDefaultConfig() {
    return [
      'id' => NULL,
      'block' => NULL,
      'modifiers' => [],
      'elements' => [],
    ];
  }


  public function updateAttributes($element_name, $attributes) {
    return $attributes;
  }

}
