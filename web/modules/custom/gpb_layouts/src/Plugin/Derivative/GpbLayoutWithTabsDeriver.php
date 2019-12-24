<?php

namespace Drupal\gpb_layouts\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GpbLayoutWithTabsDeriver extends DeriverBase implements ContainerDeriverInterface {

  protected $basePluginId;

  public function __construct($base_plugin_id) {
    $this->basePluginId = $base_plugin_id;
  }


  /**
   * Creates a new class instance.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the fetcher.
   * @param string $base_plugin_id
   *   The base plugin ID for the plugin ID.
   *
   * @return static
   *   Returns an instance of this fetcher.
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id

    );

  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    return $this->derivatives;
  }
}
