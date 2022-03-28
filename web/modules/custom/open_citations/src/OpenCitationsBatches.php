<?php

namespace Drupal\open_citations;

use Drupal\node\Entity\Node;
use Drupal\open_citations\Entity\node\Publication;

/**
 * Provides the batch callbacks from the various sources.
 *
 * This gets triggered by the Form, DrushCommand, ViewsBulkOperations, etc.
 *
 * @package Drupal\open_citations
 */
class OpenCitationsBatches {

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
    if (empty($context['sandbox'])) {
      $context['sandbox']['progress'] = 0;
      $context['sandbox']['errors'] = [];
      $context['sandbox']['max'] = count($items);
    }

    // Nothing to process.
    if (!$context['sandbox']['max']) {
      $context['finished'] = 1;
      return;
    }

    // If we haven't yet processed all.
    if ($context['sandbox']['progress'] < $context['sandbox']['max']) {
      if (isset($items[$context['sandbox']['progress']])) {
        $node = Node::load($items[$context['sandbox']['progress']]);
        if ($node instanceof Publication) {

          // Let the editor know info about what is being run.
          if ($context instanceof \DrushBatchContext) {
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
