<?php


namespace Drupal\dorion_element\Plugin\DorionElementType;

use Drupal\dorion_element\Plugin\DorionElementTypeBase;

/**
 * Class PreprocessFormElement
 *
 * @DorionElementType(
 *   id = "input",
 *   label = @Translation("Form input")
 * )
 * @package Drupal\dorion_element\Plugin\DorionElementType
 */
class Input extends DorionElementTypeBase {

  public function getAttributesParents() {
    return [];
  }


  public function getChildrenIds() {
    return [];
  }


  function getElementsPath($name, $element, $children = []) {
    return [];
  }

  public function preprocessEnable($hook, &$variables, $info) {
    return TRUE;
  }
  public function getPrepareSelectors() {
    return [
      'root' => [
        'type' => 'theme_wrapper',
        'theme_wrapper' => 'form_element',
      ],
    ];
  }

  public function getPrepareElements($elements) {
    $selectors = $this->getPrepareSelectors();
    return [];
  }

  public function getSelectors() {
    return [];
  }

  protected function getElementsParents() {
    return [
      'root' => [
        'attributes' => ['attributes'],
      ],
    ];
  }
}
