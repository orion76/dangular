<?php

namespace Drupal\dorion_element;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\dorion_element\Plugin\DorionElementTypeInterface;

/**
 * Provides the Theme preprocess plugin plugin manager.
 */
class DorionElementPluginManager extends DefaultPluginManager implements DorionElementPluginManagerInterface {


  /**
   * Constructs a new DorionElementPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/DorionElementType', $namespaces, $module_handler, 'Drupal\dorion_element\Plugin\DorionElementTypeInterface', 'Drupal\dorion_element\Annotation\DorionElementType');

    $this->alterInfo('dorion_element_type_info');
    $this->setCacheBackend($cache_backend, 'dorion_element_type');
  }

  /**
   * @param $plugin_id
   *
   * @return DorionElementTypeInterface
   */
  public function getPlugin($plugin_id) {
    try {
      /** @var $plugin DorionElementTypeInterface */
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
