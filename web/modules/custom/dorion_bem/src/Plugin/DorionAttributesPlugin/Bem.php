<?php


namespace Drupal\dorion_bem\Plugin\DorionAttributesPlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\dorion_attributes\Annotation\DorionAttributesPlugin;

use Drupal\dorion_attributes\Entity\DorionAttributesInterface;
use Drupal\dorion_attributes\Plugin\DorionAttributesPluginBase;

/**
 * Class Form
 *
 * @DorionAttributesPlugin(
 *   id = "bem",
 *   label = @Translation("BEM")
 * )
 * @package Drupal\dorion_helper\Plugin\ThemePreprocess
 */
class Bem extends DorionAttributesPluginBase {

  public function form(DorionAttributesInterface $entity, $form, FormStateInterface $form_state) {
    $elements = [];
    $elements['root'] = [
      '#type' => 'fieldset',
      '#title' => 'Root',
    ];

    $root_name = 'root';

    $elements['root'] = $this->formElement('root', $entity->get('root'));
    $elements['children'] = [
      '#type' => 'fieldset',
      '#title' => 'Children',

    ];

    $children = $entity->get('children');
    foreach ($entity->get('children') as $child) {
      $name = $child['name'];

      $elements['children'][$name] = $this->formElement($name, $child);
    }
    return $elements;
  }

  protected function formElement($name, $data) {
    $elements = [
      '#type' => 'fieldset',
      '#title' => $name,
    ];
    $elements['name'] = [
      '#type' => 'value',
      '#value' => $name,
    ];
    $elements['class'] = [
      '#type' => 'textfield',
      '#title' => 'Class',
      '#default_value' => isset($data['class']) ? $data['class'] : '',
    ];
    return $elements;
  }

  public function getDefaultConfig() {
    return [
      'id' => NULL,
      'root' => ['class' => NULL, 'modifiers' => []],
      'block' => NULL,
      'elements' => [],
    ];
  }

  protected function createElementClass($name) {
    return "{$this->getBlockClass()}__{$this->getElementClass($name)}";
  }

  protected function getBlockClass() {
    return $this->configuration['root']['class'];
  }

  protected function getElementClass($name) {
    return $this->configuration['elements'][$name]['class'];
  }

  protected function createClass($element_name) {
    return $element_name === 'root' ? $this->getBlockClass() : $this->createElementClass($element_name);
  }

  public function updateAttributes($element_name, $attributes) {
    $attributes += ['class' => []];
    if (count($attributes['class']) > 2) {
      $n = 0;
    }
    $attributes['class'][] = $this->createClass($element_name);
    return $attributes;
  }

}
