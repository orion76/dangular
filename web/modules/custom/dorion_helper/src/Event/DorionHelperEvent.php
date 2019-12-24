<?php

namespace Drupal\dorion_helper\Event;

use Symfony\Component\EventDispatcher\Event;

class DorionHelperEvent extends Event {

  /**
   * Called during hook_preprocess_html().
   */
  const PREPARE_ELEMENT = 'dorion_helper.prepare.element';
  const ELEMENT_PREPROCESS = 'dorion_helper.preprocess.element';

  protected $data;

  protected $result;

  public function __construct( $data) {
    $this->data = $data;
  }

  public function getData() {
    return $this->data;
  }

  public function getResult() {
    return $this->result;
  }

  public function setResult($result) {
    return $this->result = $result;
  }

}
