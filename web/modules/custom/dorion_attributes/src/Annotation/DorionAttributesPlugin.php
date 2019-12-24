<?php

namespace Drupal\dorion_attributes\Annotation;

use Drupal\Component\Annotation\Plugin;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines a Dorion config plugin item annotation object.
 *
 * @see plugin_api
 *
 * @Annotation
 */
class DorionAttributesPlugin extends Plugin {

  use StringTranslationTrait;

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

  /** @var $elementType 'type'|'theme' */
  public $elementType;

  /** @var $elementName 'element type'|'theme hook' */
  public $elementName;

  /**
   * DorionAttributesPlugin constructor.
   *
   * @param $values
   *
   */
  public function __construct($values) {

    parent::__construct($values);
  }

}
