<?php

namespace Drupal\dorion_attributes\Form;

use Drupal;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dorion_attributes\DorionAttributesPluginManager;
use Drupal\dorion_attributes\Entity\DorionAttributesInterface;
use Drupal\dorion_attributes\Plugin\DorionAttributesPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DorionAttributesPluginConfigForm.
 */
class DorionAttributesForm extends EntityForm implements DorionAttributesFormInterface {

  /** @var DorionAttributesPluginManager */
  protected $pluginManagerAttributes;

  public function __construct(DorionAttributesPluginManager $pluginManager) {
    $this->pluginManagerAttributes = $pluginManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.dorion_attributes')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state, $attributes_config = NULL) {
    $form = parent::form($form, $form_state);
    /** @var $entity DorionAttributesInterface */

    $entity = $this->entity;
    $action = $entity->isNew() ? 'add' : 'edit';
    $form['#action'] = $entity->toUrl($action);
    /** @var $plugin DorionAttributesPluginInterface */

    $plugin = $entity->getPlugin();

    $n = 0;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#description' => $this->t("Label for the Dorion attributes plugin config."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dorion_attributes\Entity\DorionAttributes::load',
      ],
      '#disabled' => !$entity->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    $form += $plugin->form($entity, $form, $form_state);

    return $form;
  }


  protected function actionsElement(array $form, FormStateInterface $form_state) {
    $actions = parent::actionsElement($form, $form_state);
    $n = 0;
    $actions['submit']['#ajax'] = ['callback' => [$this, 'ajaxSubmit']];
    $actions['#attached']['library'][] = 'core/drupal.dialog.ajax';
    return $actions;
  }

  public function ajaxSubmit($form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $selector = '*[data-dorion-input-selector="' . $this->entity->get('input_selector') . '"]';
    //    $response->addCommand(new InvokeCommand($selector, 'val', [$this->entity->id()]));
    $response->addCommand(new CloseModalDialogCommand());
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dorion_attributes = $this->entity;
    $status = $dorion_attributes->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Dorion attributes plugin config.', [
          '%label' => $dorion_attributes->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Dorion attributes plugin config.', [
          '%label' => $dorion_attributes->label(),
        ]));
    }
    $form_state->setRedirectUrl($dorion_attributes->toUrl('collection'));
  }

}
