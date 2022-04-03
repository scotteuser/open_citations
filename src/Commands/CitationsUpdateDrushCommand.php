<?php

namespace Drupal\open_citations\Commands;

use Drupal\open_citations\OpenCitationsBatches;
use Drush\Commands\DrushCommands;

/**
 * Citations update drush command.
 *
 * @package Drupal\open_citations\Commands
 */
class CitationsUpdateDrushCommand extends DrushCommands {

  /**
   * Update citations from the OpenCitations API.
   *
   * @command update-citations
   *
   * @usage update-citations
   */
  public function updateCitations() {
    OpenCitationsBatches::initiateBatchProcessing();
    drush_backend_batch_process();
    $this->logger()->notice($this->t('Done updating citations.'));
  }

}
