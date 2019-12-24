<?php

namespace Drupal\mercury_import;

use FilterIterator;
use Iterator;
use function in_array;

class TNVEDIterator extends FilterIterator {

  protected $codes;

  public function __construct(Iterator $iterator, array $codes) {
    parent::__construct( $iterator);
    $this->codes = $codes;
  }

  /**
   * Check whether the current element of the iterator is acceptable
   *
   * @link https://php.net/manual/en/filteriterator.accept.php
   * @return bool true if the current element is acceptable, otherwise false.
   * @since 5.1.0
   */
  public function accept() {
    $record = $this->getInnerIterator()->current();
    return $this->isCodeValid($record) && $this->isDateEndEmpty($record);
  }

  protected function isCodeValid($record) {
    $valid = TRUE;
    foreach ($this->codes as $field_name => $codes) {
      if (!in_array($record[$field_name], $codes)) {
        $valid = FALSE;
        break;
      }
    }
    return $valid;
  }

  protected function isDateEndEmpty($record) {
    return !isset($record['date_end']) || !empty($record['date_end']);
  }
}
