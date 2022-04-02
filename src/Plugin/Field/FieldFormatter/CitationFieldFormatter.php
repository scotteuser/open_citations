<?php

namespace Drupal\open_citations\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'citation' formatter.
 *
 * @FieldFormatter(
 *   id = "citation",
 *   label = @Translation("Citation"),
 *   description = @Translation("The formatter to display the citation DOI and
 *   meta details."), field_types = {
 *     "citation",
 *   }
 * )
 */
class CitationFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = [];
      $elements[$delta]['doi'] = ['#plain_text' => $item->doi];
      $elements[$delta]['title'] = ['#plain_text' => $item->title];
    }

    return $elements;
  }

}
