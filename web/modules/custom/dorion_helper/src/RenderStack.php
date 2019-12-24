<?php


namespace Drupal\dorion_helper;


use Drupal\Core\Render\Element;
use SplStack;
use function dorion_stack;


class RenderStack {

  /** @var \SplStack */
  protected $stack;

  protected $tree = [];

  protected $currentId = 0;

  protected $dorionService;

  protected function nextId() {
    return ++$this->currentId;
  }

  public function __construct(DorionServiceInterface $dorionService) {
    $this->dorionService = $dorionService;
    $this->stack = new SplStack();
  }


  public function add(&$element, $parent_id = 0) {
    if (!isset($element[DorionService::DORION_ID])) {
      $element[DorionService::DORION_ID] = $this->nextId();
      $element[DorionService::DORION_PARENT] = $parent_id;
      $this->tree[$element[DorionService::DORION_ID]] = &$element;


      $this->dorionService->prepareElement($element);
    }

  }

  public function addRecursive(&$elements, $parent = 0) {
    $this->add($elements, $parent);
    foreach (Element::children($elements) as $key) {
      $this->addRecursive($elements[$key], $elements[DorionService::DORION_ID]);
    }
  }


  public static function preRender($element) {
    $n = 0;
    //    $service = dorion_stack();
    //    $service->add($element);
    return $element;
  }


  public function elementInfoAlter(&$type) {
    static $service;
    if (empty($service)) {
      /** @var RenderStack $service */
      $service = dorion_stack();
    }
    foreach (array_keys($type) as $name) {
      $type[$name] += ['#process' => []];
      $type[$name]['#pre_render'][] = [get_class($this), 'preRender'];

    }
  }

  public function preProcess(&$variables, $hook, $info) {
    if (isset($info['render element'])) {
      $render_element = $info['render element'];
      $element = isset($variables[$render_element]) ? $variables[$render_element] : NULL;
    }
    else {
      $n = 0;
    }

    if (empty($element)) {
      return;
    }
    if (isset($element['#dorion_id'])) {
      $n = 0;
    }
    else {
      $n = 0;
    }

  }

}
