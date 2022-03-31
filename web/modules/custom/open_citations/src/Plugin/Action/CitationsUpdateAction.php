<?php

namespace Drupal\open_citations\Plugin\Action;

use Drupal\Core\Session\AccountInterface;
use Drupal\open_citations\Entity\node\Publication;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;

/**
 * Updates the Citations from OpenCitations.
 *
 * Actions are part of Drupal Core, but to run them in batches, use Views Bulk
 * Operations instead.
 *
 * @Action(
 *   id = "node_citations_update_action",
 *   label = @Translation("Update Citations from OpenCitations."),
 *   type = "node"
 * )
 */
class CitationsUpdateAction extends ViewsBulkOperationsActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity instanceof Publication) {
      $entity->updateOpenCitationsCitations();
      $entity->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($object->getEntityTypeId() === 'node') {
      $access = $object->access('update', $account, TRUE)
        ->andIf($object->access('edit', $account, TRUE));
      return $return_as_object ? $access : $access->isAllowed();
    }

    // Other entity types may have different access methods and properties.
    // Just be cautious to return either AccessResultInterface or bool
    // depending on the $return_as_object value.
    return $object->access('update', $account, $return_as_object);
  }

}
