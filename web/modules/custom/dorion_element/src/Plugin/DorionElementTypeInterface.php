<?php

namespace Drupal\dorion_element\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Theme preprocess plugin plugins.
 */
interface DorionElementTypeInterface extends PluginInspectionInterface {

  public function isDisabled($element);

  // Add get/set methods for your plugin type here.
  public function preprocess(&$variables, $info);

  public function getAttributesParents();

  public function getLabel();

  public function getChildren();

  function getElementsPath($name, $element);

  public function getSelectors();

  public function getChildrenIds();

  public function getPrepareElements($elements);

  public function preprocessEnable($hook, &$variables, $info);

}
