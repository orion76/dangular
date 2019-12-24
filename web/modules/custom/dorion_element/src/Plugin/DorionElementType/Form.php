<?php


namespace Drupal\dorion_element\Plugin\DorionElementType;

use Drupal\Core\Annotation\Translation;
use Drupal\dorion_element\Plugin\DorionElementTypeBase;
use Drupal\dorion_element\Plugin\DorionElementTypeInterface;

/**
 * Class PreprocessFormElement
 *
 * @DorionElementType(
 *   id = "form",
 *   label = @Translation("Form"),
 *   elements = {
 *      "item" = @Translation("Item")
 *   }
 * )
 * @package Drupal\dorion_element\Plugin\DorionElementType
 */
class Form extends DorionElementTypeBase {


  public function isDisabled($element) {
    return TRUE;
  }

  public function getAttributesParents() {
    return [
      'root' => ['attributes'],
      'item' => ['attributes'],
    ];
  }

  public function getChildrenIds() {
    return ['item' => 'form_element'];
  }

  public function getSelectors() {
    return [
      'root' => [
        '#theme_wrappers' => ['form_element'],
      ],
    ];
  }

  function getElementsPath($name, $element) {
    if ($name === 'root') {
      $n = 0;
    }

    /** @var $item_plugin DorionElementTypeInterface */
    $item_plugin = $this->children['item'];
    $elements = $item_plugin->getElementsPath('root', $element);
    return $elements;
  }

  //  public function selectorsForPrepare(){
  //    return [
  //      '#theme' => 'field_multiple_value_form',
  //      '#input' => TRUE,
  //    ];
  //  }
  public function getPrepareSelectors() {
    return [
      'root' => [
        'type' => 'parents',
        'parents' => [],
      ],
      'item' => [
        'type' => 'theme_wrapper',
        'value' => 'form_element',
      ],
    ];
  }


  public function preprocessEnable($hook, &$variables, $info) {
    return $hook === 'form_element' | $hook === 'form';
    //    return FALSE;
  }

  public function getPrepareElements($elements) {
    $selectors = $this->getPrepareSelectors();
    return [
      'root' => [
        'plugin_id' => $this->getPluginId(),
        'element' => 'root',
        'selector' => $selectors['root'],
      ],
      'item' => [
        'plugin_id' => $this->getPluginId(),
        'element' => 'item',
        'selector' => $selectors['item'],
      ],
    ];
  }

  protected function getElementsParents() {
    return [
      'root' => [
        'attributes' => ['attributes'],
      ],
      'item' => [
        'attributes' => ['attributes'],
      ],
    ];
  }

}
