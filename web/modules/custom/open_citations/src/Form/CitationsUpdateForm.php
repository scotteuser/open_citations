<?php

namespace Drupal\open_citations\Form;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\open_citations\OpenCitationsBatches;

/**
 * Citations update form.
 */
class CitationsUpdateForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'citations_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['info'] = [
      '#type' => 'item',
      '#plain_text' => $this->t('Trigger updating all citation data from OpenCitations.'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update data'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

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
      ->setTitle($this->t('Updating citations via the UI'))
      ->setFinishCallback($finish_callback)
      ->setInitMessage($this->t('Citation updating is starting'))
      ->setProgressMessage($this->t('Currently updating citation data.'))
      ->setErrorMessage($this->t('Citation updating has encountered an error.'));
    $batch_builder->addOperation($operation_callback, [$items]);
    batch_set($batch_builder->toArray());
  }

}
