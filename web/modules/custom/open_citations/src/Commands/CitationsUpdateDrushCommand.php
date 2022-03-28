<?php

namespace Drupal\open_citations\Commands;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\open_citations\OpenCitationsBatches;
use Drush\Commands\DrushCommands;

/**
 * Citations update drush command.
 *
 * @package Drupal\open_citations\Commands
 */
class CitationsUpdateDrushCommand extends DrushCommands {

  use StringTranslationTrait;

  /**
   * Update citations from the OpenCitations API.
   *
   * @command update-citations
   *
   * @usage update-citations
   */
  public function updateCitations() {

    // Getting the items here rather than in the batch, as typically in such a
    // a form, you may want to add options, like updating only those less than
    // once month old, or by a particular author, etc. Here you could pass any
    // number of node IDs rather than all Publications like this.
    $items = OpenCitationsBatches::getNodeIdsForBatch();

    // Start a batch process.
    $operation_callback = [
      OpenCitationsBatches::class,
      'operationCallback',
    ];
    $finish_callback = [
      OpenCitationsBatches::class,
      'finishedCallback',
    ];
    $batch_builder = (new BatchBuilder())
      ->setTitle($this->t('Updating citations via Drush'))
      ->setFinishCallback($finish_callback)
      ->setInitMessage($this->t('Citation updating is starting'))
      ->setProgressMessage($this->t('Currently updating citation data.'))
      ->setErrorMessage($this->t('Citation updating has encountered an error.'));
    $batch_builder->addOperation($operation_callback, [$items]);
    batch_set($batch_builder->toArray());

    drush_backend_batch_process();
    $this->logger()->notice($this->t('Done updating citations.'));
  }

}
