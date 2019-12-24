<?php

namespace Drupal\dorion_attributes\Entity;

use Drupal;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use function array_unshift;
use function is_scalar;


/**
 * Defines the Dorion attributes plugin config entity.
 *
 * @ConfigEntityType(
 *   id = "dorion_attributes",
 *   label = @Translation("Dorion attributes plugin config"),
 *   handlers = {
 *     "storage" = "Drupal\dorion_attributes\Entity\Handlers\DorionAttributesStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dorion_attributes\Entity\Handlers\DorionAttributesListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dorion_attributes\Form\DorionAttributesForm",
 *       "edit" = "Drupal\dorion_attributes\Form\DorionAttributesForm",
 *       "delete" = "Drupal\dorion_attributes\Form\DorionAttributesDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dorion_attributes\Entity\Handlers\DorionAttributesHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "dorion_attributes",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   lookup_keys = {
 *      "element_type",
 *      "plugin_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "element_type",
 *     "plugin_type",
 *     "config",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/dorion_attributes/{dorion_attributes}",
 *     "add-form" = "/admin/structure/dorion_attributes/add",
 *     "edit-form" = "/admin/structure/dorion_attributes/{dorion_attributes}/edit",
 *     "delete-form" = "/admin/structure/dorion_attributes/{dorion_attributes}/delete",
 *     "collection" = "/admin/structure/dorion_attributes"
 *   }
 * )
 */
class DorionAttributes extends ConfigEntityBase implements DorionAttributesInterface {

  /**
   * The Dorion attributes plugin config ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Dorion attributes plugin config label.
   *
   * @var string
   */
  protected $label;

  protected $plugin_type;

  protected $element_type;

  /** @var DorionAttributesInterface */
  protected $plugin;


  protected $config;
  public function id() {
    return $this->uuid();
  }

  public function getPluginType() {
    return $this->plugin_type;
  }

  public function getPlugin() {
    if (is_null($this->plugin)) {
      $this->plugin = Drupal::service('plugin.manager.dorion_attributes')->getPlugin($this->plugin_type);
    }
    return $this->plugin;
  }

  public function view() {
    return [
      '#type' => 'html_tag',
      '#tag' => 'pre',
      'content' => ['#markup' => Drupal\Core\Serialization\Yaml::encode($this->toArray())],
    ];
  }

  public function get($parents) {

    if (!is_array($parents)) {
      $parents = [$parents];
    }
    $field_name = array_shift($parents);
    $value = parent::get($field_name);
    if (count($parents) === 0) {
      return $value;
    }
    else {
      return NestedArray::getValue($value, $parents);
    }
  }

  /**
   * @return mixed
   */
  public function getConfig() {
    return $this->config;
  }
}
