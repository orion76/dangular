<?php

namespace Drupal\dorion_attributes\Form;



use Drupal\Core\Entity\EntityFormInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines an interface for entity form classes.
 */
interface DorionAttributesFormInterface extends EntityFormInterface {

  public function form(array $form, FormStateInterface $form_state, $config_id);

}
