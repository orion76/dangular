<?php

namespace Drupal\dorion_element\Form;;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class DorionAttributesForm.
 */
class DorionElementFormValidator {

  protected $form_state;

  protected $form;

  public function __construct(&$form, FormStateInterface $form_state) {
    $this->form = &$form;
    $this->form_state = $form_state;

  }



  protected function getKeyValue($item) {
    $value = $item['value'];
    return $value['name'] === 'class' ? $value['value'] : $value['name'];
  }


  protected function clearEmptyContext(&$values) {

  }



  public function validate() {

    //        $this->form_state->setError($this->form['base']['id'], 'Break save');
  }


}
