<?php

namespace Drupal\mercury_import\Plugin\migrate_plus\data_parser;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\mercury_import\TNVEDIterator;
use Drupal\migrate\MigrateException;
use Drupal\migrate_plus\DataParserPluginBase;
use League\Csv\Exception;
use League\Csv\Reader;
use function array_key_exists;
use function implode;
use function is_array;
use function str_replace;

/**
 * Source for CSV files.
 *
 * Available configuration options:
 * - path: Path to the  CSV file. File streams are supported.
 * - ids: Array of column names that uniquely identify each record.
 * - header_offset: (optional) The record to be used as the CSV header and the
 *   thereby each record's field name. Defaults to 0 and because records are
 *   zero indexed. Can be set to null to indicate no header record.
 * - fields: (optional) nested array of names and labels to use instead of a
 *   header record. Will overwrite values provided by header record. If used,
 *   name is required. If no label is provided, name is used instead for the
 *   field description.
 * - delimiter: (optional) The field delimiter (one character only). Defaults to
 *   a comma (,).
 * - enclosure: (optional) The field enclosure character (one character only).
 *   Defaults to double quote marks.
 * - escape: (optional) The field escape character (one character only).
 *   Defaults to a backslash (\).
 *
 * @codingStandardsIgnoreStart
 *
 * Example with minimal options:
 * @code
 * source:
 *   plugin: csv
 *   path: /tmp/countries.csv
 *   ids: [id]
 *
 * # countries.csv
 * id,country
 * 1,Nicaragua
 * 2,Spain
 * 3,United States
 * @endcode
 *
 * In this example above, the migration source will use a single-column id using the
 * value from the 'id' column of the CSV file.
 *
 * Example with most options configured:
 * @code
 * source:
 *   plugin: csv
 *   path: /tmp/countries.csv
 *   ids: [id]
 *   delimiter: '|'
 *   enclosure: "'"
 *   escape: '`'
 *   header_offset: null
 *   fields:
 *     -
 *       name: id
 *       label: ID
 *     -
 *       name: country
 *       label: Country
 *
 * # countries.csv
 * 'really long string that makes this unique'|'United States'
 * 'even longer really long string that makes this unique'|'Nicaragua'
 * 'even more longer really long string that makes this unique'|'Spain'
 * 'escaped data'|'one`'s country'
 * @endcode
 *
 * In this example above, we override the default character controls for delimiter,
 * enclosure and escape. We also set a null header offset to indicate no header.
 *
 * @codingStandardsIgnoreEnd
 *
 * @see http://php.net/manual/en/splfileobject.setcsvcontrol.php
 *
 * @DataParser(
 *   id = "csv_tnved",
 *   title = @Translation("CsvTNVED")
 * )
 */
class CsvTNVED extends DataParserPluginBase implements ContainerFactoryPluginInterface {

  const ID_PREFIX = 'tnved';

  protected $level_tnved;

  protected $codes;

  protected $iterator;

  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    $configuration['item_selector'] = '';
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->setConfiguration($configuration);
    $this->level_tnved = $configuration['level_tnved'];
    $this->codes = $configuration['codes'];
  }

  protected function addId(TNVEDIterator $iterator) {
    $result = [];

    while ($iterator->valid()) {

      $item = &$iterator->current();
      $ids = [];
      for ($i = 1; $i <= $this->level_tnved; $i++) {
        $ids[] = $item["code_{$i}"];
      }
      $item['id'] = implode('::', $ids);
      $iterator->next();
    }
  }


  /**
   * Opens the specified URL.
   *
   * @param string $url
   *   URL to open.
   *
   * @return \League\Csv\Reader
   *   TRUE if the URL was successfully opened, FALSE otherwise.
   */
  protected function openSourceUrl($url) {
    $handle = fopen($url, 'r');

    while ( ($data = fgetcsv($handle,0,'|') ) !== FALSE ) {
      $n=0;
    }
    try {
      //      $header=$reader->getHeader();
      $header = [];
//      $this->iterator = $reader->getRecords($header);
    } catch (MigrateException $e) {
      $n = 0;
    } catch (Exception $e) {
      $n = 0;
    }

    return TRUE;
  }

  protected function createReader($csv) {
    return Reader::createFromString($csv);
  }

  /**
   * Get the CSV reader.
   *
   * @return \League\Csv\Reader
   *   The reader.
   *
   * @throws \League\Csv\Exception
   */
  protected function getReader($csv) {

    $reader = Reader::createFromString($csv);
    $reader->setDelimiter($this->configuration['delimiter']);
    $reader->setEnclosure($this->configuration['enclosure']);
    $reader->setEscape($this->configuration['escape']);
    $reader->setHeaderOffset($this->configuration['header_offset']);
    return $reader;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'path' => '',
      'ids' => [],
      'header_offset' => 0,
      'fields' => [],
      'delimiter' => ",",
      'enclosure' => "\"",
      'escape' => "\\",
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    // We must preserve integer keys for column_name mapping.
    $this->configuration = NestedArray::mergeDeepArray([$this->defaultConfiguration(), $configuration], TRUE);
  }


  /**
   * Retrieves the next row of data. populating currentItem.
   *
   * Retrieves from the open source URL.
   */
  protected function fetchNextRow() {
    $current = $this->iterator->current();
    if ($current) {
      foreach ($this->fieldSelectors() as $field_name => $selector) {
        $field_data = $current;


        if (is_array($field_data) && array_key_exists($selector, $field_data)) {
          $field_data = $field_data[$selector];
        }
        else {
          $field_data = '';
        }

        $this->currentItem[$field_name] = $field_data;
      }
      $this->iterator->next();
    }
  }
}
