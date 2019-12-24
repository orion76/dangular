<?php


namespace Drupal\dorion_helper;


use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Render\Element;
use Drupal\Core\Template\Attribute;
use Drupal\dorion_helper\Event\DorionHelperEvent;
use function array_merge;
use function array_unique;
use function in_array;
use function is_array;

class DorionService implements DorionServiceInterface {


  const DORION_SETTINGS = '#dorion_settings';

  const DORION_ID = '#dorion_id';

  const DORION_PARENT = '#dorion_parent';

  protected $preparedElements = [];

  //  protected $elementPluginsMap;

  //  protected $elementPlugins;
  protected $eventDispatcher;

  public function __construct(ContainerAwareEventDispatcher $eventDispatcher) {
    $this->eventDispatcher = $eventDispatcher;
    //    $this->attributesPluginManager = $configPluginManager;
    //    $this->configRepository = $configRepository;
  }

  protected function getAttributes($variables, $path) {
    $element = NestedArray::getValue($variables, $path);

    $attributes = NULL;
    if ($element['attributes'] instanceof Attribute) {
      $attributes = $element['attributes'];
    }
    else {
      $attributes = !empty($element['attributes']) ? $element['attributes'] : [];
    }
    return $attributes;
  }

  protected function setAttributes(&$variables, $path, $attributes) {
    $path[] = 'attributes';
    NestedArray::setValue($variables, $path, $attributes);

  }

  protected function ___prepareAttributes(Attribute $attr, $classes) {
    //    $attr->addClass('test');
    $classes_attr = $attr->getClass();
    if (!$classes_attr) {
      $attr->offsetSet('class', []);
      $classes_attr = $attr->getClass();
    }
    $classes_attr->exchangeArray(array_merge($classes, $classes_attr->value()));

  }

  protected function ___prepareAttributesArray(array &$attr, $classes) {
    //    $attr->addClass('test');
    $classes_attr = isset($attr['class']) ? $attr['class'] : [];
    $attr['class'] = array_merge($classes, $classes_attr);

  }

  static function __preRender($element) {
    //    return bem_service()->addBem($element);
    return $element;
  }


  protected function getPreprocessElementConfig($hook, $variables, $info) {
    if (isset($info['render element'])) {
      $elements = $variables[$info['render element']];
    }
    else {
      return FALSE;
    }

    return isset($elements[DorionService::DORION_SETTINGS]) ? $elements[DorionService::DORION_SETTINGS] : FALSE;
  }


  public function ____preprocessElement(&$variables, /*DorionElementInterface*/ $elementPlugin, /*DorionAttributesPluginInterface*/ $plugin) {
    //    $attributes_parents = array_filter(
    //      $elementPlugin->getAttributesParents(),
    //      function ($item_parents) use ($variables) {
    //        return NestedArray::keyExists($variables, $item_parents);
    //      }
    //    );
    //
    //    foreach ($attributes_parents as $element_name => $parents) {
    //      $attributes = NestedArray::getValue($variables, $parents);
    //
    //      if ($attributes instanceof Attribute) {
    //        $attributes = $attributes->toArray();
    //      }
    //
    //      $updated_attributes = $plugin->updateAttributes($attributes, $element_name);
    //
    //      if (isset($updated_attributes['class'])) {
    //        $updated_attributes['class'] = array_unique($updated_attributes['class']);
    //      }
    //
    //      NestedArray::setValue($variables, $parents, $updated_attributes);
    //    }

  }

  protected function updateAttributes(&$element, $data) {
    foreach ($data as $attributes_data) {
      $parents = $attributes_data['parents'];
      $attributes = $attributes_data['attributes'];
      if (isset($attributes['class'])) {
        $attributes['class'] = array_unique($attributes['class']);
      }
      NestedArray::setValue($element, $parents, $attributes);
    }
  }

  public function preprocessElement($hook, &$variables, $info) {
    $preprocess_data = $this->getPreprocessElementConfig($hook, $variables, $info);
    if ($preprocess_data === FALSE) {
      return;
    }

    foreach ($preprocess_data as $preprocess_config) {

      $event = new DorionHelperEvent($preprocess_config);
      $this->eventDispatcher->dispatch(DorionHelperEvent::ELEMENT_PREPROCESS, $event);
      $result = $event->getResult();
      foreach ($result as $data) {
        $parents = $data['parents'];
        $element = &NestedArray::getValue($parents, $variables);
        if (isset($data['attributes'])) {
          $this->updateAttributes($element, $data['attributes']);
        }
      }
      //      $elementPlugin = $this->getElementPlugin($preprocess_config['plugin_id']);

      //      if (FALSE === $elementPlugin->preprocessEnable($hook, $variables, $info)) {
      //        continue;
      //      }

      //
      //      foreach ($this->attributesPluginManager->getPlugins($elementConfig->getPlugins()) as $plugin_name => $plugin) {
      //        $this->_preprocessElement($variables, $elementPlugin, $plugin);
      //      }

    }
    $n = 0;
  }


  protected function ___addElementPlugin(/*DorionElementInterface*/ $plugin) {
    //    if (!is_array($this->elementPluginsMap)) {
    //      $this->elementPluginsMap = [];
    //    }
    //    foreach ($plugin->getSelectors() as $element_name => $selectors) {
    //      foreach ($selectors as $field_name => $field_values) {
    //        foreach ($field_values as $field_value) {
    //          $this->elementPluginsMap += [$field_name => []];
    //          $this->elementPluginsMap[$field_name] += [$field_value => []];
    //
    //          $plugin_id = $plugin->getPluginId();
    //          $this->elementPluginsMap[$field_name][$field_value][] = [
    //            'element' => $element_name,
    //            'plugin_id' => $plugin_id,
    //          ];
    //          $this->elementPlugins[$plugin_id] = $plugin;
    //        }
    //      }
    //    }
  }


  protected function ___initElementPlugins() {
    //    if (is_null($this->elementPlugins)) {
    //      foreach ($this->elementPluginManager->getPlugins() as $plugin) {
    //        /** @var DorionElementInterface $plugin */
    //        $this->addElementPlugin($plugin);
    //      }
    //    }
  }

  protected function ___getElementPlugins() {
    //    $this->initElementPlugins();
    //    return $this->elementPlugins;
  }

  protected function ___getElementPluginsMap() {
    //    $this->initElementPlugins();
    //    return $this->elementPluginsMap;
  }

  protected function ___getElementPlugin($plugin_id) {

    //    return $this->getElementPlugins()[$plugin_id];
  }

  public function prepareElement(&$elements) {

    $event = new DorionHelperEvent($elements);
    $this->eventDispatcher->dispatch(DorionHelperEvent::PREPARE_ELEMENT, $event);
    $result = $event->getResult();

    if(empty($result)){
      return;
    }

    foreach ($result as $data) {
      $parents = $data['parents'];
      $element = NestedArray::getValue($parents, $elements);
      if (!isset($element[static::DORION_SETTINGS])) {
        $element[static::DORION_SETTINGS] = [];
      }
      $element[static::DORION_SETTINGS] += $data;
      NestedArray::setValue($elements, $parents, $element);
    }
    //    $plugins_map = $this->getElementPluginsMap();
    //    foreach (['#type', '#theme', '#theme_wrappers'] as $key) {
    //      if (!isset($elements[$key])) {
    //        continue;
    //      }
    //      if (!isset($plugins_map[$key])) {
    //        continue;
    //      }
    //      $values = is_array($elements[$key]) ? $elements[$key] : [$elements[$key]];
    //
    //      foreach ($values as $value) {
    //        if (!isset($plugins_map[$key][$value])) {
    //          continue;
    //        }
    //        //        foreach ($plugins_map[$key][$value] as $plugin_data) {
    //          $elements += [DorionService::DORION_SETTINGS => []];
    //          $elements[DorionService::DORION_SETTINGS][] = $plugin_data;
    //        }
    //      }


    //    }
    //    if ($plugin->isDisabled($elements)) {
    //      return;
    //    }

    //
    //    foreach ($plugin->getPrepareElements($elements) as $dorion_element) {
    //
    //      foreach ($this->getElementParents($dorion_element['selector'], $elements) as $parents) {
    //        $item = NestedArray::getValue($elements, $parents);
    //        $item += [DorionService::DORION_SETTINGS => []];
    //        $item[DorionService::DORION_SETTINGS][$dorion_element['plugin_id']] = $dorion_element;
    //        NestedArray::setValue($elements, $parents, $item);
    //        $this->addPreparedElement($parents, $dorion_element, $item);
    //      }
    //    }


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

  protected function ___getElementParents($selector, $elements) {
    $parents = NULL;
    switch ($selector['type']) {
      case 'parents':
        $parents = $selector['parents'];
        break;
      case 'theme':
        $parents = $this->findByKeyValueRecursive(['#theme' => $selector['theme']], $elements);
        break;
      case 'theme_wrappers':
        $parents = $this->findByKeyValueRecursive(['#theme_wrappers' => $selector['theme_wrapper']], $elements);
        break;
    }
    return $parents;
  }

}
