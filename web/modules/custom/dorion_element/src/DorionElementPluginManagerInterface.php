<?php


namespace Drupal\dorion_element;


use Drupal\Component\Plugin\PluginManagerInterface;

interface DorionElementPluginManagerInterface extends PluginManagerInterface{

  public function getPlugin($plugin_id);
  public function getPlugins();
}
