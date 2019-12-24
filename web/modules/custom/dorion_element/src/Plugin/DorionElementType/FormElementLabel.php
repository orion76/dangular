<?php


namespace Drupal\dorion_element\Plugin\DorionElementType;

use Drupal\dorion_element\Plugin\DorionElementTypeBase;

/**
 * Class PreprocessFormElement
 *
 * @DorionElementType(
 *   id = "form_element_label",
 *   label = @Translation("Form element label")
 * )
 * @package Drupal\dorion_element\Plugin\DorionElementType
 */
class FormElementLabel extends DorionElementTypeBase {

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

  public function getSelectors() {
    return [
      'root' => [
        '#theme_wrappers' => ['form_element'],
      ],
    ];
  }

  public function getPrepareElements($elements) {
    $selectors = $this->getSelectors();
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

