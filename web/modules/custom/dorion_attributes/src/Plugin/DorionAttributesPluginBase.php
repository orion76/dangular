<?php

namespace Drupal\dorion_attributes\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Serialization\Yaml;
use Drupal\dorion_attributes\Entity\DorionAttributesInterface;


/**
 * Base class for Dorion config plugin plugins.
 */
abstract class DorionAttributesPluginBase extends PluginBase implements DorionAttributesPluginInterface {

  protected $id;

  protected $label;

  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * @return DorionAttributesInterface
   */
  public function getConfig() {
    return $this->configuration['config'];
  }

  public function getConfiguration() {
    return $this->configuration;
  }

  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  protected function buildView(&$view) {

  }



  public function view() {
    $view = [
      '#type' => 'html_tag',
      '#tag'=>'pre',


      'content' => htmlspecialchars(Yaml::encode($this->getPluginDefinition()))];

    return $view;
  }
}
