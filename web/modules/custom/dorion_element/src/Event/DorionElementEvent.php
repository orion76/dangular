<?php

namespace Drupal\dorion_element\Event;

use Symfony\Component\EventDispatcher\Event;

class DorionElementEvent extends Event {

  /**
   * Called during hook_preprocess_html().
   */
  const PREPROCESS_ATTRIBUTES = 'dorion_element.preprocess.attributes';

  const SETTINGS_FORM_ADD_PLUGINS = 'dorion_element.form.add_plugins';

  const SETTINGS_FORM_VALIDATE = 'dorion_element.form.validate';

  const SETTINGS_FORM_SUBMIT = 'dorion_element.form.submit';

  protected $data;

  protected $pluginConfigs;

  protected $result;

  protected $elementName;

  public function __construct($elementName, $data, $pluginConfigs) {
    $this->elementName = $elementName;
    $this->data = $data;
    $this->pluginConfigs = $pluginConfigs;
  }

  public function getPluginConfigs() {
    return $this->pluginConfigs;
  }

  public function getElementName() {
    return $this->elementName;
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
