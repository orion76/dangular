<?php


namespace Drupal\dorion_helper;


interface DorionServiceInterface {

  public function preprocessElement($hook, &$variables, $info);

  public function prepareElement(&$elements);
}
