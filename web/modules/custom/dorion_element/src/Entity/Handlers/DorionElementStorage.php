<?php

namespace Drupal\dorion_element\Entity\Handlers;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Cache\MemoryCache\MemoryCacheInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller class for user roles.
 */
class DorionElementStorage extends ConfigEntityStorage implements DorionElementStorageInterface {

  protected $pluginManager;

  public function __construct(EntityTypeInterface $entity_type,
                              ConfigFactoryInterface $config_factory,
                              UuidInterface $uuid_service,
                              LanguageManagerInterface $language_manager,
                              MemoryCacheInterface $memory_cache = NULL) {
    parent::__construct($entity_type, $config_factory, $uuid_service, $language_manager, $memory_cache = NULL);


  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('config.factory'),
      $container->get('uuid'),
      $container->get('language_manager'),
      $container->get('entity.memory_cache')
    );
  }


}
