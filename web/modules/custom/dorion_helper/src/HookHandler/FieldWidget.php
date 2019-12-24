<?php


namespace Drupal\dorion_helper\HookHandler;


use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;
use function array_key_exists;
use function explode;
use function is_array;
use function t;

class FieldWidget {

  static function settingsSummary(&$summary, $context) {
    /** @var $field_definition \Drupal\field\FieldConfigInterface */
    $field_definition = $context['field_definition'];

    if ($field_definition->getType() === 'entity_reference_revisions') {
      $summary[]=Link::fromTextAndUrl(t('Link'),Url::fromRoute(''));
    }
    else {
      $settings = $context['widget']->getThirdPartySettings('dorion_helper', static::labelDefaultSettings());
      $additional_summary = t('Label place: "@place"', ['@place' => $settings['label']['place']]);

      if (is_array($summary)) {
        $summary[] = $additional_summary;
      }
      else {
        $summary = [$summary, $additional_summary];
      }
    }
  }

  static function getLabelPlaces() {
    return [
      'before' => t('Before'),
      'after' => t('After'),
    ];
  }

  static function getLabelModes() {
    return [
      'inline' => t('Inline'),
      'column' => t('Column'),
    ];
  }

  static function ___form(&$element, FormStateInterface $form_state, $context) {
    $settings = $context['widget']->getThirdPartySettings('dorion_helper');

    if ($settings && !empty($settings['label_place'])) {
      /** @var \Drupal\Core\Field\FieldDefinitionInterface $field_definition */
      $field_definition = $context['items']->getFieldDefinition();
      switch ($field_definition->getType()) {
        case 'entity_reference_revisions':
          $element['#label_place'] = $settings['label_place'];
          $element['#label_mode'] = $settings['label_mode'];
          break;

        default:
          $rewritten_title = explode('||', $settings['new_label']);
          foreach (Element::children($element) as $index => $child) {
            if (isset($element[$child]['#title']) && array_key_exists($index, $rewritten_title)) {
              $element[$child]['#title'] = $rewritten_title[$index];
            }
          }
          break;
      }
    }
  }

  static function labelDefaultSettings() {
    return [
      'label' => ['place' => 'before', 'mode' => 'column'],
    ];
  }

  /**
   * @param \Drupal\Core\Field\WidgetInterface $plugin
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   * @param $form_mode
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  static function thirdPartySettingsForm(WidgetInterface $plugin, FieldDefinitionInterface $field_definition, $form_mode, $form, FormStateInterface $form_state) {

    $settings = $plugin->getThirdPartySetting('dorion_helper', 'label', static::labelDefaultSettings());
    $element = [];
    $element['label'] = [
      '#type' => 'details',
      '#tree' => TRUE,
      '#title' => t('Label'),
    ];
    $element['label']['place'] = [
      '#type' => 'select',
      '#options' => static::getLabelPlaces(),
      '#title' => t('Place'),
      '#default_value' => $settings['place'],
    ];
    $element['label']['mode'] = [
      '#type' => 'select',
      '#title' => t('Mode'),
      '#options' => static::getLabelModes(),
      '#default_value' => $settings['mode'],
    ];

    return $element;
  }
}
