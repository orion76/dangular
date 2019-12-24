<?php


namespace Drupal\dorion_element\Plugin\DorionElementType;

use Drupal\dorion_element\Plugin\DorionElementTypeBase;

/**
 * Class PreprocessFormElement
 *
 * @DorionElementType(
 *   id = "form_element",
 *   label = @Translation("Form element"),
 *   elements = {
 *      "title" = @Translation("title"),
 *      "input" = @Translation("Input"),
 *      "description" = @Translation("Description")
 *   }
 * )
 * @package Drupal\dorion_element\Plugin\DorionElementType
 */
class FormElement extends DorionElementTypeBase {

  public function getChildrenIds() {
    return [];
  }

  function getElementsPath($name, $element, $children = []) {
    $elements = [];
    //    $elements['item'] = $this->findElementsByTheme( 'field_multiple_value_form', $element);
    switch ($name) {
      case 'root':
        $search = [
          '#theme_wrappers' => 'form_element',
          //          '#theme' => 'field_multiple_value_form',
          //          '#input' => FALSE,
        ];
        $debug = $this->findByKeyValueRecursiveDebug($search, $element);
        $elements = $this->findByKeyValueRecursive($search, $element);
        //        $elements = array_map(function ($item) {
        //          $item[] = 0;
        //          return $item;
        //        }, $elements);
        break;
    }

    return $elements;
  }

  public function getAttributesParents() {

    $parents['root'] = ['attributes'];
    $parents['title'] = ['label', 'attributes'];
    $parents['input'] = ['content_attributes'];
    $parents['description'] = ['description', 'attributes'];

    return $parents;
  }


  //  public function getPrepareElementsPath($elements){
  //    $this->findByKeyValueRecursive($this->selectorsForPrepare(), $elements);
  //  }

  //  public function selectorsForPrepare(){
  //    return [
  //      '#theme' => 'field_multiple_value_form',
  //      '#input' => TRUE,
  //    ];
  //  }

  public function preprocessEnable($hook, &$variables, $info) {
    return $hook === 'form_element';
  }
  public function getSelectors() {
    return [
      'root' => [
        '#theme_wrappers' => ['form_element'],
      ],
    ];
  }
  public function getPrepareSelectors() {
    return [
      'root' => [
        'type' => 'theme_wrappers',
        'theme_wrapper' => 'form_element',
      ],
    ];
  }


  public function getPrepareElements($elements) {
    $selectors = $this->getPrepareSelectors();
    return [
      'root' => [
        'plugin_id' => $this->getPluginId(),
        'element' => 'root',
        'selector' => $selectors['root'],
      ],
    ];
  }


  protected function getElementsParents() {
    return [
      'root' => [
        'attributes' => ['attributes'],
      ],
    ];
  }
}
