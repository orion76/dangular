<?php


namespace Drupal\dorion_attributes;


use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\dorion_attributes\Plugin\DorionAttributesInterface;

interface DorionAttributesPluginManagerInterface extends PluginManagerInterface{

  /**
   * @param $plugin_id
   *
   * @param $config
   *
   * @return DorionAttributesInterface
   */
//  public function getPlugin($plugin_id, $config);

  /**
   * @param array $configuration
   *
   * @return DorionAttributesInterface[]
   */
  public function getPlugins();
}
