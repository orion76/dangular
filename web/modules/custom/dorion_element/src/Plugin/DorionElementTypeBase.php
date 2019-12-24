<?php

namespace Drupal\dorion_element\Plugin;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Template\Attribute;
use Drupal\dorion_element\DorionElementPluginManagerInterface;
use Drupal\dorion_element\Event\DorionElementEvent;
use Drupal\dorion_helper\DorionService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use function array_filter;
use function array_map;
use function array_merge;
use function in_array;
use function is_array;

/**
 * Base class for Theme preprocess plugin plugins.
 */
abstract class DorionElementTypeBase extends PluginBase implements DorionElementTypeInterface, ContainerFactoryPluginInterface {

  protected $elements = [];

  protected $elementsParents;

  /** @var DorionService */
  protected $service;

  /** @var DorionElementPluginManagerInterface */
  protected $pluginManager;


  protected $children;


  /** @var ContainerAwareEventDispatcher */
  protected $eventDispatcher;

  public function __construct(array $configuration, $plugin_id, $plugin_definition,
                              DorionElementPluginManagerInterface $pluginManager,
                              ContainerAwareEventDispatcher $eventDispatcher) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->pluginManager = $pluginManager;
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.dorion_element'),
      $container->get('event_dispatcher')
    );
  }

  abstract protected function getElementsParents();

  protected function getElementParents($name, $type) {
    if (empty($this->elementsParents)) {
      $this->elementsParents = $this->getElementsParents();
    }
    return $this->elementsParents[$name][$type];
  }

  protected function getElementData($variables, $name, $type) {
    $parents = $this->getElementParents($name, $type);
    return NestedArray::getValue($variables, $parents);
  }


  protected function setElementData(&$variables, $name, $type, $data) {
    $parents = $this->getElementParents($name, $type);
    NestedArray::setValue($variables, $parents, $data);
  }


  public function preprocess(&$variables, $info) {
    /** DorionConfigInterface */
    $config = $this->getConfig();

    if (empty($config)) {
      return;
    }

    foreach ($this->getElementsNames() as $element_name) {
      $attributes = $this->getElementData($variables, $element_name, 'attributes');

      $event = new DorionElementEvent($element_name, $attributes, $config->getPlugins());
      $this->eventDispatcher->dispatch(DorionElementEvent::PREPROCESS_ATTRIBUTES, $event);
      $attributes = $event->getResult();

      $this->setElementData($variables, $element_name, 'attributes', $attributes);
    }
  }

  public function _preprocessElement(&$variables, DorionElementTypeInterface $elementPlugin) {
    $attributes_parents = array_filter(
      $elementPlugin->getAttributesParents(),
      function ($item_parents) use ($variables) {
        return NestedArray::keyExists($variables, $item_parents);
      }
    );

    foreach ($attributes_parents as $element_name => $parents) {
      $attributes = NestedArray::getValue($variables, $parents);

      if ($attributes instanceof Attribute) {
        $attributes = $attributes->toArray();
      }

//      $event = new DorionElementEvent($this->getPluginId(),$attributes);
//      $this->eventDispatcher->dispatch(DorionElementEvent::PREPROCESS_ATTRIBUTES, $event);

//      $updated_attributes = $event->getResult();

      //      $updated_attributes = $plugin->updateAttributes($attributes, $element_name);
      //
//      if (isset($updated_attributes['class'])) {
//        $updated_attributes['class'] = array_unique($updated_attributes['class']);
//      }
//
//      NestedArray::setValue($variables, $parents, $updated_attributes);
    }

  }

  protected function getConfig() {
//    if (empty($this->config)) {
//      $this->config = $this->configRepository->loadByElementId($this->getPluginId());
//    }
//    return $this->config;
    return NULL;
  }

  protected function getChildren___() {
    if (is_null($this->children)) {
      $this->children = array_map(function ($children_id) {
        return $this->pluginManager->getPlugin($children_id);
      }, $this->getChildrenIds());
    }
    return $this->children;

  }


  /**
   * @param $element
   *
   * @return bool
   */
  public function isDisabled($element) {
    return FALSE;
  }

  protected function findElementsByThemeWrappers($theme, $elements) {
    return $this->findByKeyValueRecursive(['#theme_wrappers' => $theme], $elements);
  }

  protected function findByKeyValueRecursive($search, array $elements, $parents = []) {
    $result = [];

    $found = 0;
    foreach ($search as $key => $value) {
      if (!isset($elements[$key])) {
        break;
      }

      if (is_array($elements[$key]) && in_array($value, $elements[$key])) {
        $found++;
      }
      elseif ($value === $elements[$key]) {
        $found++;
      }

    }

    if ($found === count($search)) {
      $result[] = $parents;
    }

    foreach (Element::children($elements) as $field) {
      $next_parents = $parents;
      $next_parents[] = $field;
      $result = array_merge($result, $this->findByKeyValueRecursive($search, $elements[$field], $next_parents));
    }

    return $result;
  }


  protected function findByKeyValueRecursiveDebug($search, array $elements, $parents = []) {
    $result = [];

    $found = 0;
    foreach ($search as $key => $value) {
      if (!isset($elements[$key])) {
        break;
      }

      if (is_array($elements[$key]) && in_array($value, $elements[$key])) {
        $found++;
      }
      elseif ($value === $elements[$key]) {
        $found++;
      }

    }

    if ($found === count($search)) {
      $result[] = [
        'parents' => $parents,
        'element' => $elements,
      ];
    }

    foreach (Element::children($elements) as $field) {
      $next_parents = $parents;
      $next_parents[] = $field;
      $result = array_merge($result, $this->findByKeyValueRecursiveDebug($search, $elements[$field], $next_parents));
    }

    return $result;
  }

  public function getLabel() {
    return $this->pluginDefinition['label'];
  }


  protected function getElement($variables, $info) {
    $name = $info['render element'];
    return $variables[$name];
  }

  public function getElementsNames() {
    return array_keys($this->pluginDefinition['elements']);
  }

  public function getChildren() {
    return isset($this->pluginDefinition['elements']) ? $this->pluginDefinition['elements'] : [];
  }

  public function getAttributesParents() {
    return ['root' => 'attributes'];
  }

  protected function getAttribute($element_name, $variables) {
    $attributes = [];
    foreach ($this->getAttributesParents() as $name => $parents) {
      $attributes[$name] = NestedArray::getValue($variables, $parents);
    }
    return $attributes;
  }

}
