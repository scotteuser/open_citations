<?php

namespace Drupal\open_citations\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'citation' type.
 *
 * @FieldType(
 *   id = "citation",
 *   label = @Translation("Citation"),
 *   description = @Translation("This field stores citation DOI and meta
 *   details."), default_widget = "citation", default_formatter = "citation",
 *   category = @Translation("Reference"),
 * )
 */
class CitationFieldType extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['doi'] = DataDefinition::create('string')
      ->setLabel(t('DOI'))
      ->setDescription(t('The DOI of the citation.'));
    $properties['title'] = DataDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the citation.'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $columns = [
      'doi' => [
        'description' => 'The DOI of the citation.',
        'type' => 'varchar',
        'length' => 255,
      ],
      'title' => [
        'description' => 'The title of the citation.',
        'type' => 'varchar',
        'length' => 1000,
      ],
    ];

    return [
      'columns' => $columns,
    ];
  }

}
