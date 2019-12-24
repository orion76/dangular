<?php


namespace Drupal\dorion_attributes\Plugin\DorionAttributesPlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\dorion_attributes\Entity\DorionAttributesInterface;
use Drupal\dorion_attributes\Plugin\DorionAttributesPluginBase;
use Drupal\dorion_attributes\Plugin\DorionAttributesPluginInterface;

/**
 * Class Form
 *
 * @DorionAttributesPlugin(
 *   id = "attributes_custom",
 *   label = @Translation("Attributes custom")
 * )
 */
class AttributesCustom extends DorionAttributesPluginBase implements DorionAttributesPluginInterface {

  public function form(DorionAttributesInterface $entity,$form, FormStateInterface $form_state) {
    //    $build = new DorionAttributesFormBuilderBem($elementPlugin, $form, $form_state);
    //    return $build->form($config + $this->getDefaultConfig());
    return ['#markup' => 'AttributesCustom'];
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
