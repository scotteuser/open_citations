<?php

namespace Drupal\open_citations\Form;

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
    OpenCitationsBatches::initiateBatchProcessing();
  }

}
