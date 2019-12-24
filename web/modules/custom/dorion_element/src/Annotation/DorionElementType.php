<?php

namespace Drupal\dorion_element\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Theme preprocess plugin item annotation object.
 *
 * @see \Drupal\dorion_element\Plugin\DorionElementPluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class DorionElementType extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;


  protected $elements=[];

  /**
   * @return array
   */
  public function getElements(): array {
    return $this->elements;
  }

}
