<?php

namespace Drupal\gpb_form_elements\Plugin\Field\FieldWidget;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsButtonsWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'options_buttons' widget.
 *
 * @FieldWidget(
 *   id = "radio_buttons",
 *   label = @Translation("radio buttons"),
 *   field_types = {
 *     "boolean",
 *     "entity_reference",
 *     "list_integer",
 *     "list_float",
 *     "list_string",
 *   },
 *   multiple_values = TRUE
 * )
 */
class RadioButtonsWidget extends OptionsButtonsWidget {

  /** @var boolean */
  protected $required;

  /** @var boolean */
  protected $multiple;

  /** @var boolean */
  protected $has_value;

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    if ($this->multiple) {

    }
    else {
      $element['#type'] = 'radio_buttons';
    }


    return $element;
  }


}
