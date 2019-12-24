<?php


namespace Drupal\dorion_bem;


use ArrayAccess;
use Countable;
use Iterator;
use function implode;

class PathArray implements Countable, ArrayAccess, Iterator {

  protected $collection = [];

  protected $_keys = [];

  protected $empty;

  public function __construct($empty) {
    $this->empty = $empty;
  }

  protected function createHash(array $path) {
    return implode('::', $path);
  }

  /**
   * Count elements of an object
   *
   * @link https://php.net/manual/en/countable.count.php
   * @return int The custom count as an integer.
   * </p>
   * <p>
   * The return value is cast to an integer.
   * @since 5.1.0
   */
  public function count() {
    return count($this->collection);
  }

  /**
   * Whether a offset exists
   *
   * @link https://php.net/manual/en/arrayaccess.offsetexists.php
   *
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   *
   * @return bool true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   * @since 5.0.0
   */
  public function offsetExists($offset) {
    $key = $this->createHash($offset);
    return isset($this->collection[$key]);
  }

  /**
   * Offset to retrieve
   *
   * @link https://php.net/manual/en/arrayaccess.offsetget.php
   *
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   *
   * @return mixed Can return all value types.
   * @since 5.0.0
   * @throws \Drupal\dorion_bem\BemException
   */
  public function &offsetGet($offset) {
    if (!$this->offsetExists($offset)) {
      $this->offsetSet($offset, $this->empty);
    }
    return $this->collection[$this->createHash($offset)];
  }

  /**
   * Offset to set
   *
   * @link https://php.net/manual/en/arrayaccess.offsetset.php
   *
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   *
   * @return void
   * @since 5.0.0
   * @throws \Drupal\dorion_bem\BemException
   */
  public function offsetSet($offset, $value) {

    if (is_null($offset)) {
      throw new BemException('Offset can not be NULL');
    }
    else {
      $hash = $this->createHash($offset);
      if (!isset($this->collection[$hash])) {
        $this->_keys[$hash] = $offset;
      }
      $this->collection[$hash] = $value;
    }

  }

  /**
   * Offset to unset
   *
   * @link https://php.net/manual/en/arrayaccess.offsetunset.php
   *
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   *
   * @return void
   * @since 5.0.0
   */
  public function offsetUnset($offset) {
    $hash = $this->createHash($offset);
    unset($this->collection[$hash]);
    unset($this->_keys[$hash]);
  }

  /**
   * Return the current element
   *
   * @link https://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   * @since 5.0.0
   */
  public function current() {
    return $this->collection[$this->currentHash()];
  }

  /**
   * Move forward to next element
   *
   * @link https://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function next() {
    next($this->_keys);
  }

  /**
   * Return the key of the current element
   *
   * @link https://php.net/manual/en/iterator.key.php
   * @return mixed scalar on success, or null on failure.
   * @since 5.0.0
   */
  public function key() {
    $hash = $this->currentHash();
    return isset($this->_keys[$hash]) ? $this->_keys[$hash] : NULL;
  }

  /**
   * Checks if current position is valid
   *
   * @link https://php.net/manual/en/iterator.valid.php
   * @return bool The return value will be casted to boolean and then evaluated.
   * Returns true on success or false on failure.
   * @since 5.0.0
   */
  public function valid() {
    $hash = $this->currentHash();
    return !is_null($hash) && isset($this->collection[$hash]);
  }

  /**
   * Rewind the Iterator to the first element
   *
   * @link https://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function rewind() {
    reset($this->_keys);
  }

  protected function currentHash() {
    return key($this->_keys);
  }
}
