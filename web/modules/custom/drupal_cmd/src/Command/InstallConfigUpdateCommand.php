<?php

namespace Drupal\drupal_cmd\Command;

use Drupal\Console\Command\Shared\ExtensionTrait;
use Drupal\Console\Core\Utils\ChainQueue;
use Drupal\Console\Extension\Manager;
use Drupal\metatag\Generator\MetatagGroupGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Core\Command\Command;
use function array_slice;
use function is_file;
use function is_string;
use function scandir;

/**
 * Class InstallConfigUpdateCommand.
 *
 * Drupal\Console\Annotations\DrupalCommand (
 *     extension="drupal_cmd",
 *     extensionType="module"
 * )
 */
class InstallConfigUpdateCommand extends Command {

  use ExtensionTrait;

  /**
   * The console extension manager.
   *
   * @var \Drupal\Console\Extension\Manager
   */
  protected $extensionManager;

  /**
   * @var ChainQueue
   */
  protected $chainQueue;

  public function __construct(Manager $extensionManager, ChainQueue $chainQueue) {
    $this->extensionManager = $extensionManager;
    $this->chainQueue = $chainQueue;
    parent::__construct();
  }


  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $n = 0;
    $this
      ->setName('dc-conf:update')
      ->setDescription($this->trans('commands.dc-conf.update.description'))
      ->addOption(
        'extension',
        NULL,
        InputOption::VALUE_REQUIRED,
        $this->trans('commands.common.options.extension')
      )
      ->addOption(
        'extension-type',
        NULL,
        InputOption::VALUE_REQUIRED,
        $this->trans('commands.common.options.extension-type')
      )
      ->addOption(
        'extension-path',
        NULL,
        InputOption::VALUE_REQUIRED,
        $this->trans('commands.common.options.extension-path')
      );;

  }

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {
    $this->getIo()->info('interact');
    /** @var \Drupal\Core\Extension\Extension $extension */
    $extension = $input->getOption('extension');
    if (!$extension) {
      $extension = $this->extensionQuestion(TRUE, TRUE);
      $input->setOption('extension', $extension);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->getIo()->info('execute');
    $this->getIo()->info($this->trans('commands.dc-conf.update.messages.success'));

    /** @var \Drupal\Core\Extension\Extension $extension */
    $extension = $input->getOption('extension');
    if (is_string($extension)) {
      $extension = $this->extensionManager->getModule($extension);
    }


    $directory = $extension->getPath(TRUE) . '/config/install';
    $output->writeln('path:' . $directory);
    $files = $this->getFilesList($directory, $output);


    $this->chainQueue->addCommand('config:import:single', ['--directory' => $directory, '--file' =>  $files]);
    $this->chainQueue->addCommand('cache:rebuild', ['all']);

    //    drupal config:import:single --directory=sites/all/modules/custom/coin_import/config/install --file=migrate_plus.migration.location_country.yml

  }

  private function getFilesList($dir, OutputInterface $output) {
    $list = array_slice(scandir($dir), 2);
    $files = [];
    $output->writeln('Files:');
    foreach ($list as $name) {
      if (is_file("{$dir}/{$name}")) {
        $files[] = $name;
        $output->writeln('   ' . $name);
      }
    }
    return $files;
  }

}
