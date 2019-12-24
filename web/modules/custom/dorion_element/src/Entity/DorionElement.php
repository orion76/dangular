<?php

namespace Drupal\dorion_element\Entity;

use Drupal;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\dorion_element\Exception\DorionElementException;
use Drupal\dorion_element\Plugin\DorionElementTypeInterface;
use function is_null;
use function reset;
use function sprintf;


/**
 * Defines the Dorion config entity.
 *
 * @ConfigEntityType(
 *   id = "dorion_element",
 *   label = @Translation("Dorion config"),
 *   handlers = {
 *     "storage" = "Drupal\dorion_element\Entity\Handlers\DorionElementStorage",
 *     "view_builder" = "Drupal\dorion_element\Entity\Handlers\DorionElementViewBuilder",
 *     "list_builder" = "Drupal\dorion_element\Entity\Handlers\DorionElementListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dorion_element\Form\DorionElementForm",
 *       "edit" = "Drupal\dorion_element\Form\DorionElementForm",
 *       "delete" = "Drupal\dorion_element\Form\DorionElementDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dorion_element\Entity\Handlers\DorionElementHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "entity",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *
 *     "canonical" = "/admin/appearance/dorion_element/{dorion_element}",
 *     "add-form" = "/admin/appearance/dorion_element/add",
 *     "edit-form" = "/admin/appearance/dorion_element/{dorion_element}/edit",
 *     "delete-form" = "/admin/appearance/dorion_element/{dorion_element}/delete",
 *     "collection" = "/admin/appearance/dorion_element"
 *   },
 *   lookup_keys = {
 *     "element_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "element_type",
 *     "children",
 *     "context",
 *     "attributes",
 *   },
 * )
 */
class DorionElement extends ConfigEntityBase implements DorionElementInterface {


  /**
   * The Dorion config Base ID.
   *
   * @var string
   */
  protected $element_type;

  /** @var DorionElementTypeInterface */
  protected $plugin;


  /**
   * The Dorion config label.
   *
   * @var string
   */
  protected $label;

  protected $context;
  protected $children;

  protected $attributes;

  protected $attributesPlugins;

  public function id() {
    return $this->uuid();
  }

  public function getPlugin() {
    if (is_null($this->plugin)) {
      try {
        $plugins = $this->getPlugins('dorion_element', [$this->getElementType()]);
        if (count($plugins) === 1) {
          $this->plugin = reset($plugins);
        }
      } catch (DorionElementException $e) {
      }

    }
    return $this->plugin;
  }

  /**
   * @param $type
   * @param $ids
   *
   * @return array
   * @throws \Drupal\dorion_element\Exception\DorionElementException
   */
  protected function getPlugins($type, $ids) {

    $pluginManagerId = NULL;
    switch ($type) {
      case 'dorion_element':
        $pluginManagerId = 'plugin.manager.dorion_element';
        break;
      case 'dorion_attributes':
        $pluginManagerId = 'plugin.manager.dorion_attributes';
        break;
    }
    if (is_null($pluginManagerId)) {
      throw new DorionElementException(sprintf('Missing plugin manager for plugin type: %s', $type));
    }

    /** @var \Drupal\Component\Plugin\PluginManagerInterface $pluginManager */
    $pluginManager = Drupal::service($pluginManagerId);

    $plugins = [];

    try {
      foreach ($ids as $plugin_id) {
        $plugins[$plugin_id] = $pluginManager->createInstance($plugin_id);
      }
    } catch (Drupal\Component\Plugin\Exception\PluginException $e) {
    }

    return $plugins;
  }

  /**
   * @return array
   */
  public function getContext(): array {
    return $this->context;
  }

  public function getAttributes() {
    return $this->attributes;
  }

  public function getAttributesPlugins() {
    if (is_null($this->attributesPlugins)) {
      $this->attributesPlugins = $this->getPlugins('dorion_attributes', $this->getAttributes());
    }
    return $this->attributesPlugins;
  }


  public function getElementType() {
    return $this->element_type;
  }
}
