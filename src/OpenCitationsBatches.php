<?php

namespace Drupal\open_citations;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\node\Entity\Node;
use Drupal\open_citations\Entity\node\Publication;

/**
 * Provides the batch callbacks from the various sources.
 *
 * This gets triggered by the Form, DrushCommand, Node submit callback, etc.
 *
 * @package Drupal\open_citations
 */
class OpenCitationsBatches {

  /**
   * Initiate the batch processing.
   */
  public static function initiateBatchProcessing() {
    // Here you could pass any number of node IDs rather than all Publications
    // like this.
    $items = self::getNodeIdsForBatch();

    // Start a batch process.
    $operation_callback = [
      OpenCitationsBatches::class,
      'operationCallback',
    ];
    $finish_callback = [
      OpenCitationsBatches::class,
      'finishedCallback',
    ];

    // Define the messaging the user should see by default.
    $batch_builder = (new BatchBuilder())
      ->setTitle(t('Updating citations via the UI'))
      ->setFinishCallback($finish_callback)
      ->setInitMessage(t('Citation updating is starting'))
      ->setProgressMessage(t('Currently updating citation data.'))
      ->setErrorMessage(t('Citation updating has encountered an error.'));

    // Add as many operations as you would like. Each operation goes through
    // the progress bar from start to finish, then goes on to the next batch.
    $batch_builder->addOperation($operation_callback, [$items]);

    // If we are not inside a form submit handler we also need to call
    // batch_process() to initiate the redirect.
    batch_set($batch_builder->toArray());
  }

  /**
   * Get the node IDs of all publications.
   *
   * @return array
   *   The node IDs of all publications to process.
   */
  public static function getNodeIdsForBatch() {
    $entity_query = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->getQuery();
    $entity_query->condition('type', 'publication');
    $results = (array) $entity_query->execute();
    return array_values($results);
  }

  /**
   * Process callback for the batch set in the TriggerBatchForm form.
   *
   * @param array $items
   *   The items import form.
   * @param array|\DrushBatchContext $context
   *   The batch context.
   */
  public static function operationCallback(array $items, &$context) {

    // Context sandbox is empty on initial load. Here we take care of things
    // that need to be done once only. This context is then subsequently
    // available for every subsequent batch run.
    if (empty($context['sandbox'])) {
      $context['sandbox']['progress'] = 0;
      $context['sandbox']['errors'] = [];
      $context['sandbox']['max'] = count($items);
    }

    // If we have nothing to process, mark the batch as 100% complete (0 = not
    // started, eg 0.5 = 50% completed, 1 = 100% completed).
    if (!$context['sandbox']['max']) {
      $context['finished'] = 1;
      return;
    }

    // If we haven't yet processed all.
    if ($context['sandbox']['progress'] < $context['sandbox']['max']) {

      // This is a counter that is passed from batch run to batch run.
      if (isset($items[$context['sandbox']['progress']])) {
        $node = Node::load($items[$context['sandbox']['progress']]);
        if ($node instanceof Publication) {

          // Let the editor know info about what is being run.
          // If via drush command or drush updatedb, also let the user know the
          // progress percentage as they will not see the progress bar.
          if (PHP_SAPI === 'cli') {
            $context['message'] = t('[@percentage] Updating "@item" (@id)', [
              '@percentage' => round(($context['sandbox']['progress'] / $context['sandbox']['max']) * 100) . '%',
              '@item' => $node->label(),
              '@id' => $node->id(),
            ]);
          }
          else {
            $context['message'] = t('Updating "@item" (@id)', [
              '@item' => $node->label(),
              '@id' => $node->id(),
            ]);
          }

          // Update the citations and save.
          $node->updateOpenCitationsCitations();
          $node->save();
        }
        else {

          // Failed to load the publication. Possibly deleted since the batch
          // began?
          $context['sandbox']['errors'][] = t('Unable to process ID @id', [
            '@id' => $items[$context['sandbox']['progress']],
          ]);
        }
      }

      $context['sandbox']['progress']++;

      // Results are passed to the finished callback.
      $context['results']['items'][] = $items[$context['sandbox']['progress']];
    }

    // When progress equals max, finished is '1' which means completed. Any
    // decimal between '0' and '1' is used to determine the percentage of
    // the progress bar.
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  }

  /**
   * Batch finished callback.
   *
   * @param bool $success
   *   Batch encountered errors or not.
   * @param array $results
   *   The processed chapters.
   * @param array $operations
   *   The different batches that were run.
   */
  public static function finishedCallback($success, array $results, array $operations) {
    if ($success) {

      // The 'success' parameter means no fatal PHP errors were detected.
      $message = t('@count publications were updated.', [
        '@count' => count($results['items']),
      ]);
      \Drupal::messenger()->addStatus($message);
    }
    else {

      // A fatal error occurred.
      $message = t('Finished with an error.');
      \Drupal::messenger()->addWarning($message);
    }
  }

}
