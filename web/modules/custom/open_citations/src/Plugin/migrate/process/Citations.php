<?php

namespace Drupal\open_citations\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Load citations via OpenCitations API.
 *
 * @code
 * process:
 *   name:
 *     plugin: citations
 *     value: some_field
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "citations"
 * )
 */
class Citations extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($doi, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($doi)) {
      return [];
    }

    $data = [];

    /** @var \Drupal\open_citations\OpenCitationsClient $open_citations_client */
    $open_citations_client = \Drupal::service('open_citations.client');
    if ($citations = $open_citations_client->getCitationsForDoi($doi)) {
      foreach ($citations as $doi => $title) {
        $data[] = [
          'title' => $title,
          'doi' => $doi,
        ];
      }
    }

    return $data;
  }

}
