<?php

namespace Drupal\gpb_layouts\Plugin\Layout;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\SortArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Link;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Zend\Stdlib\ArrayUtils;
use function array_filter;
use function array_key_exists;
use function array_map;
use function uasort;

/**
 * A very advanced custom layout.
 * deriver = "Drupal\gpb_layouts\Plugin\Derivative\GpbLayoutWithTabsDeriver"
 * @Layout(
 *   id = "gpb_layout_with_tabs",
 *   label = @Translation("With tabs"),
 *   category = @Translation("gpb"),
 *   template = "layouts/with_tabs/with-tabs",
 *   library = "gpb_layouts/with_tabs",
 *   icon = "images/1col.png"
 * )
 */
class GpbLayoutWithTabs extends LayoutDefault implements PluginFormInterface {

  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function process(&$variables) {
    /** @var $layout \Drupal\Core\Layout\LayoutDefinition */
    $layout = $variables['layout'];
    if ($layout->id() !== 'gpb_layout_with_tabs') {
      return;
    }

    $variables['regions'] = [];
    $variables['tabs'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => [],
      '#attributes' => ['class' => 'nav nav-tabs', 'role' => 'tablist'],
      //      '#wrapper_attributes' => ['class' => 'nav nav-tabs','role'=>'tablist'],
    ];

    $first = TRUE;
    foreach ($layout->getRegions() as $regionId => $region) {
      $content_id = Html::getUniqueId('layout-with-tabs--' . $regionId);
      $link_id = Html::getUniqueId('layout-with-tabs--' . $regionId . '-link');
      $link = Link::fromTextAndUrl($region['label'], Url::fromUserInput('#' . $content_id))->toRenderable();

      $link['#wrapper_attributes'] = ['class' => ['nav-item']];
      $link['#attributes'] = [
        'id' => $link_id,
        'class' => ['nav-link'],
        'data-toggle' => 'tab',
        'role' => 'tab',
        'aria-controls' => $content_id,
      ];


      $variables['regions'][$regionId] = $variables['content'][$regionId];
      unset($variables['content'][$regionId]);
      $content_attributes = [
        'id' => $content_id,
        'class' => ['tab-pane', 'fade'],
        'role' => 'tabpanel',
        'aria-labelledby' => $regionId . '-tab',
      ];

      if ($first) {
        $link['#attributes']['class'][] = 'active';
        $content_attributes['class'][] = 'show active';
      }
      $variables['tabs']['#items'][] = $link;
      $variables['regions'][$regionId]['attributes'] = new Attribute($content_attributes);
      $first = FALSE;
    }

  }

  /**
   * Form constructor.
   *
   * Plugin forms are embedded in other forms. In order to know where the plugin
   * form is located in the parent form, #parents and #array_parents must be
   * known, but these are not available during the initial build phase. In order
   * to have these properties available when building the plugin form's
   * elements, let this method return a form element that has a #process
   * callback and build the rest of the form in the callback. By the time the
   * callback is executed, the element's #parents and #array_parents properties
   * will have been set by the form API. For more documentation on #parents and
   * #array_parents, see \Drupal\Core\Render\Element\FormElement.
   *
   * @param array $form
   *   An associative array containing the initial structure of the plugin form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form. Calling code should pass on a subform
   *   state created through
   *   \Drupal\Core\Form\SubformState::createForSubform().
   *
   * @return array
   *   The form structure.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $configuration = $this->getConfiguration();

    $form['tabs'] = [
      '#type' => 'container',
      '#title' => $this->t('Tabs'),
    ];

    foreach ($configuration['tabs'] as $tab) {
      $name = $tab['name'];
      $parent = ['layout_settings', 'tabs', $name, 'label'];
      $form['tabs'][$name] = $this->createTabForm($parent, $tab, $tab['label']);
    }


    $new = ['label' => '', 'name' => ''];

    $form['new'] = $this->createTabForm(['layout_settings', 'new', 'label'], $new, $this->t('New section'));
    return $form;
  }

  protected function createTabForm($parent, $tab, $title) {
    $form = [
      '#type' => 'details',
      '#title' => $title,
    ];

    $form['label'] = [
      '#type' => 'textfield',
      '#default_value' => $tab['label'],

    ];
    $form['name'] = [
      '#type' => 'machine_name',
      '#default_value' => empty($tab['name']) ? NULL : $tab['name'],
      '#machine_name' => [
        'exists' => [$this, 'exists'],
        'source' => $parent,
      ],
      '#description' => '',
    ];
    $form['weight'] = [
      '#type' => 'textfield',
      '#size' => 6,
      '#default_value' => !empty($tab['weight']) ? $tab['weight'] : 0,
      '#title' => $this->t('Weight'),
    ];
    $form['delete'] = [
      '#type' => 'checkbox',
      '#default_value' => FALSE,
      '#title' => $this->t('Delete'),
    ];
    return $form;
  }

  public function exists($tab_id) {
    return isset($this->configuration['tabs'][$tab_id]);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
        'tabs' => [],
      ];
  }

  /**
   * Form validation handler.
   *
   * @param array $form
   *   An associative array containing the structure of the plugin form as built
   *   by static::buildConfigurationForm().
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form. Calling code should pass on a subform
   *   state created through
   *   \Drupal\Core\Form\SubformState::createForSubform().
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Temporarily store all form errors.
    $form_errors = $form_state->getErrors();
    if (isset($form_errors['layout_settings][new][name'])) {
      $form_state->clearErrors();

      unset($form_errors['layout_settings][new][name']);

      foreach ($form_errors as $name => $error_message) {
        $form_state->setErrorByName($name, $error_message);
      }
    }

  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the plugin form as built
   *   by static::buildConfigurationForm().
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form. Calling code should pass on a subform
   *   state created through
   *   \Drupal\Core\Form\SubformState::createForSubform().
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $tabs = $form_state->getValue('tabs', []);
    $new = $form_state->getValue('new', []);
    if (isset($new['name']) && !empty($new['name'])) {
      if (empty($new['weight'])) {
        $new['weight'] = 1000;
      }
      $tabs[$new['name']] = $new;
    }


    $tabs = array_filter($tabs, function ($item) {
      $not_delete = !isset($item['delete']) || !$item['delete'];
      $not_empty = isset($item['name']) && !empty($item['name']);
      return $not_empty && $not_delete;
    });

    uasort($tabs, 'Drupal\Component\Utility\SortArray::sortByWeightElement');

    $delta = 0;
    foreach ($tabs as &$tab) {
      $tab['weight'] = $delta;
      $delta++;
    }

    $tabs = array_map(function ($item) use ($delta) {
      if (isset($item['delete'])) {
        unset($item['delete']);
      }
      return $item;
    }, $tabs);

    $this->configuration['tabs'] = $tabs;
  }


  /**
   * {@inheritdoc}
   */
  public function getPluginDefinition() {
    $definition = $this->pluginDefinition;
    $definition->setRegions($this->getRegionsFromLayout($this->configuration['tabs']));
    return $definition;
  }

  /**
   * Loads regions from custom layout.
   *
   * @param array $tabs
   *   The current Layout settings.
   *
   * @return array
   *   Renderable regions for layout discover.
   */
  protected function getRegionsFromLayout(array $tabs) {
    $regions = [];

    foreach ($tabs as $machine_name => $item) {

      $regions[$machine_name] = [
        'label' => $item['label'],
      ];


    }

    return $regions;
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $regions) {
    // Ensure $build only contains defined regions and in the order defined.
    $build = [];
    foreach ($this->getPluginDefinition()->getRegionNames() as $region_name) {
      if (array_key_exists($region_name, $regions)) {
        $build[$region_name] = $regions[$region_name];
      }
    }
    $build['#settings'] = $this->getConfiguration();
    $build['#layout'] = $this->pluginDefinition;
    $build['#theme'] = $this->pluginDefinition->getThemeHook();
    if ($library = $this->pluginDefinition->getLibrary()) {
      $build['#attached']['library'][] = $library;
    }

    return $build;
  }

}
