<?php

namespace Drupal\gpb_layouts;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Template\Attribute;

class GpbLayoutDefault extends LayoutDefault implements PluginFormInterface {


  /**
   * {@inheritdoc}
   */
  public function build(array $regions) {
    // Ensure $build only contains defined regions and in the order defined.
    $build = parent::build($regions);
    $configuration = $this->getConfiguration();
    $build['#attributes']['class'][] = 'layout';
    $build['#attributes']['class'][] = $configuration['layout_class'];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
        'layout_class' => '',
      ];
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
    $form['layout_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Layout class'),
      '#default_value' => $configuration['layout_class'],
    ];
    return $form;
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
    // TODO: Implement validateConfigurationForm() method.
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
    $this->configuration['layout_class'] = $form_state->getValue('layout_class');
  }
}
