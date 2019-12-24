<?php

namespace Drupal\dorion_element\Event;

use Symfony\Component\EventDispatcher\Event;

class DorionElementFormEvent extends Event {


  const ADD_ATTRIBUTES = 'dorion_element.form.add_attributes';


  protected $element_type;

  protected $plugins_data;

  protected $result;

  public function __construct($element_type, $plugin_data) {
    $this->element_type = $element_type;
    $this->plugins_data = $plugin_data;
  }


  public function getElementType() {
    return $this->element_type;
  }

  public function getPluginsData() {
    return $this->plugins_data;
  }


  public function getResult() {
    return $this->result;
  }

  public function setResult($result) {
    return $this->result = $result;
  }

}
