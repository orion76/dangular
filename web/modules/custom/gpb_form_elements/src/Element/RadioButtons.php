<?php

namespace Drupal\gpb_form_elements\Element;

use Drupal\Component\Utility\Html as HtmlUtility;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Annotation\FormElement;
use Drupal\Core\Render\Element\Radios;

/**
 * Provides a form element for a set of radio buttons.
 *
 * Properties:
 * - #options: An associative array, where the keys are the returned values for
 *   each radio button, and the values are the labels next to each radio button.
 *
 * Usage example:
 * @code
 * $form['settings']['active'] = array(
 *   '#type' => 'radios',
 *   '#title' => $this->t('Poll status'),
 *   '#default_value' => 1,
 *   '#options' => array(0 => $this->t('Closed'), 1 => $this->t('Active')),
 * );
 * @endcode
 *
 * @see \Drupal\Core\Render\Element\Checkboxes
 * @see \Drupal\Core\Render\Element\Radio
 * @see \Drupal\Core\Render\Element\Select
 *
 * @FormElement("radio_buttons")
 */
class RadioButtons extends Radios {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#process' => [
        [$class, 'processRadioButtons'],
      ],
      '#theme_wrappers' => ['radio_buttons'],
      '#pre_render' => [
        [$class, 'preRenderCompositeFormElement'],
      ],
    ];
  }

  /**
   * Expands a radios element into individual radio elements.
   */
  public static function processRadioButtons(&$element, FormStateInterface $form_state, &$complete_form) {
    if (count($element['#options']) > 0) {
      $weight = 0;
      foreach ($element['#options'] as $key => $choice) {
        // Maintain order of options as defined in #options, in case the element
        // defines custom option sub-elements, but does not define all option
        // sub-elements.
        $weight += 0.001;

        $element += [$key => []];
        // Generate the parents as the autogenerator does, so we will have a
        // unique id for each radio button.
        $parents_for_id = array_merge($element['#parents'], [$key]);
        $element[$key] += [
          '#type' => 'radio',
          '#title' => $choice,
          // The key is sanitized in Drupal\Core\Template\Attribute during output
          // from the theme function.
          '#return_value' => $key,
          // Use default or FALSE. A value of FALSE means that the radio button is
          // not 'checked'.
          '#default_value' => isset($element['#default_value']) ? $element['#default_value'] : FALSE,
          '#attributes' => $element['#attributes'],
          '#parents' => $element['#parents'],
          '#id' => HtmlUtility::getUniqueId('edit-' . implode('-', $parents_for_id)),
          '#ajax' => isset($element['#ajax']) ? $element['#ajax'] : NULL,
          // Errors should only be shown on the parent radios element.
          '#error_no_message' => TRUE,
          '#weight' => $weight,
        ];
      }
    }
    return $element;
  }
  /**
   * Adds form element theming to an element if its title or description is set.
   *
   * This is used as a pre render function for checkboxes and radios.
   */
  public static function preRenderCompositeFormElement($element) {
    // Set the element's title attribute to show #title as a tooltip, if needed.
    if (isset($element['#title']) && $element['#title_display'] == 'attribute') {
      $element['#attributes']['title'] = $element['#title'];
      if (!empty($element['#required'])) {
        // Append an indication that this field is required.
        $element['#attributes']['title'] .= ' (' . t('Required') . ')';
      }
    }

    if (isset($element['#title']) || isset($element['#description'])) {
      // @see #type 'fieldgroup'
      $element['#attributes']['id'] = $element['#id'] . '--wrapper';
//      $element['#theme_wrappers'][] = 'fieldset';
      $element['#attributes']['class'][] = 'fieldgroup';
      $element['#attributes']['class'][] = 'form-composite';
    }
    return $element;
  }


}
