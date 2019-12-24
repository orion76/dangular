<?php

namespace Drupal\dorion_element\Form;

;

use Drupal;
use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Component\Utility\Html;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dorion_attributes\DorionAttributesPluginManagerInterface;
use Drupal\dorion_attributes\Entity\DorionAttributesInterface;
use Drupal\dorion_attributes\Entity\Handlers\DorionAttributesStorageInterface;
use Drupal\dorion_attributes\Plugin\DorionAttributesPluginInterface;
use Drupal\dorion_element\DorionElementPluginManagerInterface;
use Drupal\dorion_element\Entity\DorionElementInterface;
use Drupal\dorion_element\Plugin\DorionElementTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use function array_column;
use function array_diff_key;
use function array_slice;
use function get_class;
use function implode;

/**
 * Class DorionElementForm.
 */
class DorionElementForm extends EntityForm {

  /** @var DorionElementPluginManagerInterface */
  protected $elementPluginManager;


  /** @var DorionAttributesPluginManagerInterface */
  protected $attributesPluginManager;

  /** @var ContainerAwareEventDispatcher */
  protected $eventDispatcher;

  /** @var DorionAttributesStorageInterface */
  protected $attributesStorage;

  /** @var UuidInterface */
  protected $uuidService;

  public function __construct(DorionElementPluginManagerInterface $elementPluginManager,
                              DorionAttributesPluginManagerInterface $attributesPluginManager,
                              ContainerAwareEventDispatcher $eventDispatcher,
                              UuidInterface $uuid_service) {

    $this->elementPluginManager = $elementPluginManager;
    $this->attributesPluginManager = $attributesPluginManager;
    $this->eventDispatcher = $eventDispatcher;
    $this->uuidService = $uuid_service;

  }

  protected function getAttributesStorage() {
    if (is_null($this->attributesStorage)) {
      try {
        $this->attributesStorage = $this->entityTypeManager->getStorage('dorion_attributes');
      } catch (InvalidPluginDefinitionException $e) {
      } catch (PluginNotFoundException $e) {
      }
    }
    return $this->attributesStorage;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.dorion_element'),
      $container->get('plugin.manager.dorion_attributes'),
      $container->get('event_dispatcher'),
      $container->get('uuid')
    );
  }

  protected function getElements() {
    $elements = ['' => '-- Select --'];
    $elements += array_column($this->elementPluginManager->getDefinitions(), 'label', 'id');
    return $elements;
  }

  protected function getAttributesTypes() {
    $elements = ['' => '-- Select --'];
    $elements += array_column($this->attributesPluginManager->getDefinitions(), 'label', 'id');
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  public function buildFormBase(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    /** @var $entity DorionElementInterface */
    $entity = $this->entity;
    $wrapper_id = Html::getId('dorion-config-form-ajax-wrapper');
    $form['#prefix'] = '<div id="' . $wrapper_id . '">';
    $form['#suffix'] = '</div>';
    $elements = [];
    $elements['label'] = [
      '#type' => 'value',
      '#default_value' => isset($values['label']) ? $values['label'] : '',
    ];

    $elements['id'] = [
      '#type' => 'value',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dorion_element\Entity\DorionElement::load',
      ],

    ];

    $element_type = $entity->getElementType();
    $element_plugins = $this->getElements();

    $elements['element_type'] = [
      '#type' => 'select',
      '#title' => 'Plugin ID',
      '#options' => $element_plugins,
      '#default_value' => $element_type,
      '#required' => TRUE,
    ];

    if (empty($element_type)) {
      return $elements;
    }

    $elements['elements'] = [
      '#type' => 'table',
      '#caption' => 'Elements',
      '#header' => ['name' => 'Name', 'label' => 'Label'],
    ];

    /** @var $element_plugin DorionElementTypeInterface */
    try {
      $element_plugin = $this->elementPluginManager->createInstance($element_type);
    } catch (PluginException $e) {
    }

    foreach ($element_plugin->getChildren() as $name => $label) {
      $elements['elements'][] = [
        'name' => ['#markup' => $name],
        'label' => ['#markup' => $label],
      ];
    }
    return $elements;
  }


  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $element_type = $this->entity->getElementType();

    $form += $this->buildFormBase($form, $form_state);

    if (empty($element_type)) {
      return $form;
    }

    //    $form_state->set('attributes_storage', $this->getAttributesStorage());
    //    $form_state->set('element_plugin', $this->entity->getPlugin());

    $form['context'] = ['#type' => 'fieldset', '#title' => 'Context'];

    $form['attributes_tabs'] = [
      '#type' => 'vertical_tabs',
      '#title' => 'Attributes',
      '#attached' => ['library' => ['node/drupal.content_types']],
    ];

    $form['attributes'] = ['#type' => 'container', '#tree' => TRUE];
    $form['attributes'] += $this->addAttributesPluginForms($form_state);

    return $form;
  }

  protected function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->prepareValues($values);

    if ($this->entity instanceof EntityWithPluginCollectionInterface) {
      // Do not manually update values represented by plugin collections.
      $values = array_diff_key($values, $this->entity->getPluginCollections());
    }

    // @todo: This relies on a method that only exists for config and content
    //   entities, in a different way. Consider moving this logic to a config
    //   entity specific implementation.
    foreach ($values as $key => $value) {
      $entity->set($key, $value);
    }
  }

  public static function getAttributesConfig() {

  }


  public static function pluginEditArguments(FormStateInterface $form_state) {

    $arguments = [];

    $button = $form_state->getTriggeringElement();
    $parents = array_slice($button['#array_parents'], 0, 2);
    $parents[] = 'config_id';
    $arguments['config_id'] = $form_state->getValue($parents);
    $arguments['plugin_type'] = $parents[1];

    return $arguments;
  }

  public function ajaxCallbackPluginEdit($form, FormStateInterface $form_state) {
    $arguments = $this->pluginEditArguments($form_state);
    /** @var $element DorionElementTypeInterface */
    $element = $this->entity->getPlugin();
    $children = [];
    foreach ($element->getChildren() as $name => $label) {
      $children[$name] = ['name' => $name];
    }
    /** @var $attributes_config DorionAttributesInterface */
    if (empty($config_id)) {
      $values = [
        'label' => $arguments['plugin_type'],
        'plugin_type' => $arguments['plugin_type'],
        'root' => [],
        'children' => $children,
      ];
      $attributes_config = $this->getAttributesStorage()->create($values);
      $operation = 'edit';
    }
    else {
      $attributes_config = $this->getAttributesStorage()->load($arguments['config_id']);
      $operation = 'add';
    }

    $attributes_config->set('input_selector', $this->getInputSelector($arguments['plugin_type'], $form_state));

    $response = new AjaxResponse();

    /** @var $form_builder EntityFormBuilderInterface */
    $form_builder = Drupal::service('entity.form_builder');
    $form = $form_builder->getForm($attributes_config, $operation);
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $response->addCommand(new OpenModalDialogCommand("Edit plugin", $form, ['width' => 900, 'minHeight' => 300]));
    //    }
    return $response;

  }

  /**
   * Handles switching the available regions based on the selected theme.
   */
  public static function ajaxCallback($form, FormStateInterface $form_state) {

    return $form;

  }

  protected function setInputSelector($plugin_type, &$input, FormStateInterface $form_state) {
    $input_selector = $this->uuidService->generate();
    $form_state->setTemporaryValue(['attributes', $plugin_type, 'input_selector'], $input_selector);
    $input['#attributes']['data-dorion-input-selector'] = $input_selector;
  }

  protected function getInputSelector($plugin_type, FormStateInterface $form_state) {
    return $form_state->getTemporaryValue(['attributes', $plugin_type, 'input_selector']);
  }

  protected function addAttributesPluginForms(FormStateInterface $form_state) {
    $elements = [];
    $elements['#attached']['library'][] = 'dorion_element/dorion_element';
    $element_attributes = $this->entity->getAttributes();


    foreach ($this->attributesPluginManager->getDefinitions() as $plugin_definition) {
      $plugin_type = $plugin_definition['id'];
      $value = isset($element_attributes[$plugin_type]) ? $element_attributes[$plugin_type] : [] + ['config_id' => NULL];

      /** @var $attributes DorionAttributesInterface */

      if (!is_null($value['config_id'])) {
        $attributes = $this->getAttributesStorage()->load($value['config_id']);
      }
      else {
        $attributes_value = [
          'label' => $this->entity->label(),
          'element_type' => $this->entity->getElementType(),
          'plugin_type' => $plugin_type,
        ];
        $attributes = $this->getAttributesStorage()->create($attributes_value);
      }

      /** @var $plugin DorionAttributesPluginInterface */
      $plugin = $attributes->getPlugin();


      $elements[$plugin_type] = [
        '#type' => 'details',
        '#title' => $plugin->getLabel(),
        '#group' => 'attributes_tabs',
        '#tree' => TRUE,
        '#weight' => 100,
        'plugin_type' => ['#type' => 'value', '#value' => $plugin_type],
        'config_id' => [
          '#type' => 'hidden',
          '#value' => $attributes->id(),
        ],
        'view' => $attributes->view(),
      ];

      $this->setInputSelector($plugin_type, $elements[$plugin_type]['config_id'], $form_state);

      $name = [$plugin_type];
      if ($attributes->isNew()) {
        $name[] = 'add';
        $elements[$plugin_type]['add'] = [
          '#type' => 'button',
          '#value' => 'Add',
          '#name' => 'attributes[' . implode('][', $name) . ']',
          '#ajax' => [
            'callback' => [$this, 'ajaxCallbackPluginEdit'],
          ],
        ];
      }
      else {
        $name[] = 'edit';
        $elements[$plugin_type]['edit'] = [
          '#type' => 'button',
          '#value' => 'Edit',
          '#name' => 'attributes[' . implode('][', $name) . ']',
          '#ajax' => [
            'callback' => [get_class($this), 'ajaxCallbackPluginEdit'],
          ],
        ];
      }
    }
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dorion_element = $this->entity;
    $status = $dorion_element->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Dorion config.', [
          '%label' => $dorion_element->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Dorion config.', [
          '%label' => $dorion_element->label(),
        ]));
    }
    if (!$this->entity->isNew()) {
      try {
        $form_state->setRedirectUrl($this->entity->toUrl('edit-form'));
      } catch (Drupal\Core\Entity\EntityMalformedException $e) {
      }
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Remove button and internal Form API values from submitted values.
    parent::submitForm($form, $form_state);
    $form_state->setRedirectUrl($this->entity->toUrl('canonical'));
  }

  protected function prepareValues(&$values) {
    if (isset($values['element_type']) && !empty($values['element_type'])) {
      $values['id'] = $values['element_type'];
      $values['label'] = $values['element_type'];
    }
  }
}
