<?php


namespace Drupal\dorion_element\Plugin\DorionElementType;

/**
 * Class PreprocessFormElement
 *
 * @DorionElementType(
 *   id = "input__radio",
 *   label = @Translation("Form input Radio")
 * )
 * @package Drupal\dorion_element\Plugin\DorionElementType
 */
class InputRadio extends Input {

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

  protected function getElementsParents() {
    return [
      'root' => [
        'attributes' => ['attributes'],
      ],
    ];
  }
}
