<?php

namespace Drupal\paragraphs_plugins\Plugin\paragraphs\Behavior;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\ParagraphsBehaviorBase;

/**
 * @ParagraphsBehavior(
 *   id = "paragraph_css_class",
 *   label = @Translation("Paragraph CSS class"),
 *   description = @Translation("Allows to set paragraph item css class."),
 *   weight = 0,
 * )
 */
class ParagraphCssClass extends ParagraphsBehaviorBase {

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

    if ($css_class = $paragraph->getBehaviorSetting($this->getPluginId(), 'css_class', NULL)) {
      $build['#attributes']['class'] = [$css_class];
    }


  }


  /**
   * {@inheritdoc}
   */
  public function buildBehaviorForm(ParagraphInterface $paragraph, array &$form, FormStateInterface $form_state) {

    $form['css_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CSS Class'),
      '#description' => $this->t('Paragraph CSS Class'),
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(), 'css_class'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(Paragraph $paragraph) {
    $summary = [];
    if ($css_class = $paragraph->getBehaviorSetting($this->getPluginId(), 'css_class', NULL)) {
      $summary[] = $this->t('CSS Class: @class', ['@class' => $css_class]);
    }
    return $summary;
  }

}
