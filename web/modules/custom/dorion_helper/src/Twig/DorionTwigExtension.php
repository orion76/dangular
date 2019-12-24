<?php


namespace Drupal\dorion_helper\Twig;


use Drupal\Core\Template\Attribute;
use Drupal\Core\Template\AttributeArray;
use function array_diff;
use function array_filter;
use function array_merge;
use function array_shift;
use function array_unique;
use function func_get_args;
use function implode;
use function is_string;
use function reset;

class DorionTwigExtension extends \Twig_Extension {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'dorion_helper';
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('bem_block', [$this, 'bem_block']),
      new \Twig_SimpleFunction('bem_elem', [$this, 'bem_elem']),
      new \Twig_SimpleFunction('initClasses', [$this, 'initClasses']),
      new \Twig_SimpleFunction('xjoin', [$this, 'xjoin']),
      new \Twig_SimpleFunction('xmerge', [$this, 'xmerge']),
      new \Twig_SimpleFunction('xAttributes', [$this, 'xAttributes']),
//      new \Twig_SimpleFunction('create_bem', [$this, 'create_bem']),
      new \Twig_SimpleFunction('xattr', [$this, 'xattr']),
      new \Twig_SimpleFunction('xdebug', [$this, 'xdebug'], [
        'is_safe' => ['html'],
        'needs_environment' => TRUE,
        'needs_context' => TRUE,
        'is_variadic' => TRUE,
      ]),
    ];
  }

  /**
   * Provides Kint function to Twig templates.
   *
   * Handles 0, 1, or multiple arguments.
   *
   * Code derived from https://github.com/barelon/CgKintBundle.
   *
   * @param \Twig_Environment $env
   *   The twig environment instance.
   * @param array $context
   *   An array of parameters passed to the template.
   * @param array $args
   *   An array of parameters passed the function.
   *
   * @return string
   *   String representation of the input variables.
   */
  public function xdebug(\Twig_Environment $env, array $context, array $args = []) {


    // No arguments passed to kint(), display full Twig context.
    if (empty($args)) {
      $variables = [];
      foreach ($context as $key => $value) {
        if (!$value instanceof \Twig_Template) {
          $variables[$key] = $value;
        }
      }
    }
    else {
      // Try to get the names of variables from the Twig template.
      $parameters = $this->getTwigFunctionParameters();

      // If there is only one argument, pass to Kint without too much hassle.
      if (count($args) == 1) {
        $variables = reset($args);
        $variable_name = reset($parameters);

      }
      else {
        foreach ($args as $index => $arg) {
          $name = !empty($parameters[$index]) ? $parameters[$index] : $index;
          $variables['_index_' . $index . '_' . $name] = $arg;
        }
      }
    }

    return $variables;
  }

  /**
   * @param array $external
   * @param array $default
   *
   * @return array
   */
  public function initClasses($external, $default = []) {
    $n = 0;
    if (empty($external)) {
      $external = [];
    }
    $output = array_unique(array_merge($default, $external));
    return $output;
  }

  public function bem_block($block, $modifiers = []) {
    return $this->_bem($block, $modifiers);
  }

  protected function _bem($bem) {
    $classes = [];
    $base_class = $bem['block'];
    if (isset($bem['elem'])) {
      $base_class = "{$base_class}__{$bem['elem']}";
    }

    if (isset($bem['mod'])) {
      if (is_string($bem['mod'])) {
        $bem['mod'] = [$bem['mod']];
      }
      foreach (array_filter($bem['mod']) as $modifier) {
        $classes[] = "{$base_class}--{$modifier}";
      }
    }
    return $classes;
  }


  public function bem_elem($block, $element, $modifiers = []) {
    $element_class = "{$block}__{$element}";
    return $this->_bem($element_class, $modifiers);
  }

  public function xjoin(array $arr, $delimiter = ' ') {
    return implode($delimiter, $arr);
  }

  public function xAttributes($classes = [], Attribute $attributes = NULL) {
    if (is_null($attributes)) {
      $attributes = new Attribute();
    }
    //    foreach ($classes as $class) {
    $attributes = $attributes->addClass($classes);
    //    }

    return $attributes;
  }

  public function xattr(Attribute $attr) {
    //    $attr->addClass('test');
    $classes = $attr->getClass();
    if (!$classes) {
      $attr->offsetSet('class', []);
      $classes = $attr->getClass();
    }

    $bems = func_get_args();
    array_shift($bems);

    $this->addClass($classes, $bems);

    return $attr;
  }

  public function create_bem() {
    return new Bem();
  }

  protected function addClass(AttributeArray &$classes, $bems) {

    $bem_classes = [];
    foreach ($bems as $bem) {
      $bem_classes = array_merge($bem_classes, $bem->getClasses());
    }
    $extra_classes = array_diff($bem_classes, $classes->value());

    $classes->exchangeArray(array_merge($bem_classes, $extra_classes));
  }

  public function xmerge() {
    $args = func_get_args();
    return $this->toFlat($args);
  }

  protected function toFlat(array $args) {
    $return = [];
    array_walk_recursive($args, function ($a) use (&$return) {
      $return[] = $a;
    });
    return array_filter($return);
  }


}
