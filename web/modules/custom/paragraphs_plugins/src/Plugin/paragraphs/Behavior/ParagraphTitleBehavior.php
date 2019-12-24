<?php

namespace Drupal\paragraphs_plugins\Plugin\paragraphs\Behavior;

use Drupal\Component\Utility\SortArray;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Template\Attribute;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\ParagraphsBehaviorBase;
use function uasort;

/**
 * @ParagraphsBehavior(
 *   id = "paragraph_title",
 *   label = @Translation("Paragraph title element"),
 *   description = @Translation("Allows to select HTML wrapper for title."),
 *   weight = 0,
 * )
 */
class ParagraphTitleBehavior extends ParagraphsBehaviorBase {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ParagraphsType $paragraphs_type) {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function view(array &$build, Paragraph $paragraph, EntityViewDisplayInterface $display, $view_mode) {
    $n = 0;
    $title_field = $paragraph->getBehaviorSetting($this->getPluginId(), 'title_field', NULL);
    $title_tag = $paragraph->getBehaviorSetting($this->getPluginId(), 'title_tag', 'h2');
    if (!empty($title_field) && isset($build[$title_field])) {
      $field = $build[$title_field][0];
      $build[$title_field] = [
        '#type' => 'html_tag',
        '#tag' => $title_tag,
        'content' => ['#markup' => $field['#text']],
      ];

    }


  }

  /**
   * Adds a default set of helper variables for preprocessors and templates.
   *
   * This preprocess function is the first in the sequence of preprocessing
   * functions that are called when preparing variables of a paragraph.
   *
   * @param array $variables
   *   An associative array containing:
   *   - elements: An array of elements to display in view mode.
   *   - paragraph: The paragraph object.
   *   - view_mode: The view mode.
   */
  public function preprocess(&$variables) {
    $n = 0;
    if (isset($variables['content']['_layout_builder']) && count($variables['content']) === 1) {
      return;
    }

    unset($variables['content']['_layout_builder']);

    $paragraph = $variables['paragraph'];
    foreach ($paragraph->getAllBehaviorSettings() as $plugin_id => $settings) {
      switch ($plugin_id) {
        case 'paragraph_css_class':
          break;
        case 'paragraph_title':
          if ($title_field = $settings['title_field']) {
            $variables['title'] = $variables['content'][$title_field];
            unset($variables['content'][$title_field]);
          }
          $content_reverse = isset($settings['content_reverse']) && (boolean) $settings['content_reverse'];

          foreach (array_keys($variables['content']) as $field_name) {
            if (!isset($variables['content'][$field_name]['#attributes'])) {
              $variables['content'][$field_name]['#attributes'] = new Attribute();
            }

//            $variables['content'][$field_name]['#attributes']->addClass('field-item');

            if ($content_reverse) {
              $variables['content'][$field_name]['#weight'] *= -1;
            }

          }
          uasort($variables['content'], 'Drupal\Component\Utility\SortArray::sortByWeightProperty');

          break;
      }
    }
  }

  private function getFieldsOptions(Paragraph $paragraph) {
    $options = [];
    foreach ($paragraph->getFieldDefinitions() as $field_name => $definition) {
      /** @var $definition \Drupal\Core\Field\BaseFieldDefinition */
      $options[$field_name] = $definition->getLabel();
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildBehaviorForm(ParagraphInterface $paragraph, array &$form, FormStateInterface $form_state) {

    $form['title_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Title element'),
      '#description' => $this->t('Wrapper HTML element'),
      '#options' => $this->getFieldsOptions($paragraph),
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(), 'title_field'),
    ];

    $form['title_tag'] = [
      '#type' => 'select',
      '#title' => $this->t('Title element'),
      '#description' => $this->t('Wrapper HTML element'),
      '#options' => $this->getTitleOptions(),
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(), 'title_tag', 'h2'),
    ];

    $form['content_reverse'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Reverse content fields'),
      '#description' => $this->t('Reverse content fields'),
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(), 'content_reverse', FALSE),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(Paragraph $paragraph) {
    $summary = [];
    if ($title_element = $paragraph->getBehaviorSetting($this->getPluginId(), 'title_field', NULL)) {
      $title_tag = $paragraph->getBehaviorSetting($this->getPluginId(), 'title_tag', NULL);
      $summary[] = $this->t('Title element: @element<br>Tag: @tag', [
        '@element' => $title_element,
        '@tag' => $title_tag,
      ]);
    }

    if ($reverse_content = $paragraph->getBehaviorSetting($this->getPluginId(), 'reverse_content', FALSE)) {
      $summary[] = $this->t('Reverse content fields');
    }
    return $summary;
  }

  /**
   * Return options for heading elements.
   */
  private function getTitleOptions() {
    return [
      'h2' => '<h2>',
      'h3' => '<h3>',
      'h4' => '<h4>',
      'div' => '<div>',
      'span' => '<span>',
    ];
  }

}
