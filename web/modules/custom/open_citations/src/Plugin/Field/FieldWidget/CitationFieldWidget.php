<?php

namespace Drupal\open_citations\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'citation' widget.
 *
 * @FieldWidget(
 *   id = "citation",
 *   label = @Translation("Citation"),
 *   description = @Translation("The widget to manage the citation DOI and meta
 *   details."), field_types = {
 *     "citation",
 *   },
 * )
 */
class CitationFieldWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['doi'] = [
      '#type' => 'textfield',
      '#title' => $this->t('DOI'),
      '#description' => $this->t('Enter the Digital Object Identifier (DOI) for this citation.'),
      '#maxlength' => 255,
      '#default_value' => $items[$delta]->doi ?? '',
    ];
    $element['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => $this->t('Enter the Title for this citation.'),
      '#maxlength' => 1000,
      '#default_value' => $items[$delta]->title ?? '',
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $key => $value) {

      // Remove records that are fully empty.
      if (empty($value['doi']) && empty($value['title'])) {
        unset($values[$key]);
      }
    }
    return $values;
  }

}
