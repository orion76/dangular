<?php

namespace Drupal\dorion_attributes;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\dorion_attributes\Plugin\DorionAttributesPluginInterface;
use Traversable;

/**
 * Provides the Dorion config plugin plugin manager.
 */
class DorionAttributesPluginManager extends DefaultPluginManager implements DorionAttributesPluginManagerInterface {


  protected $plugins;

  /**
   * DorionAttributesPluginManager constructor.
   *
   * @param \Traversable $namespaces
   * @param CacheBackendInterface $cache_backend
   * @param ModuleHandlerInterface $module_handler
   */
  public function __construct(Traversable $namespaces,
                              CacheBackendInterface $cache_backend,
                              ModuleHandlerInterface $module_handler) {

    parent::__construct('Plugin/DorionAttributesPlugin', $namespaces, $module_handler,
      'Drupal\dorion_attributes\Plugin\DorionAttributesPluginInterface',
      'Drupal\dorion_attributes\Annotation\DorionAttributesPlugin');

    $this->alterInfo('dorion_attributes_plugin_info');
    $this->setCacheBackend($cache_backend, 'dorion_attributes_plugins');
  }


  /**
   * @param $plugin_id
   * @param $configuration
   *
   * @return DorionAttributesPluginInterface
   */
  public function getPlugin($plugin_id) {
    try {
      /** @var $plugin DorionAttributesPluginInterface */
      $plugin = $this->createInstance($plugin_id);
    } catch (PluginException $e) {
    }
    return $plugin;
  }

  public function getPlugins() {
    $plugins = [];
    try {
      foreach ($this->getDefinitions() as $definition) {
        $plugins[] = $this->getPlugin($definition['id']);
      }
    } catch (PluginException $e) {
    }
    return $plugins;
  }

}
