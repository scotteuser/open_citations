<?php

namespace Drupal\open_citations\Plugin\DevelGenerate;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\devel_generate\Plugin\DevelGenerate\ContentDevelGenerate;

/**
 * Provides a PublicationsDevelGenerate plugin.
 *
 * @DevelGenerate(
 *   id = "open_citations_publications",
 *   label = "publications with DOIs",
 *   description = "Generate a given number of Publications with real example
 *     DOIs.",
 *   url = "open_citations_publications",
 *   permission = "administer devel_generate",
 *   settings = {
 *     "num" = 50,
 *     "kill" = FALSE
 *   }
 * )
 */
class PublicationsDevelGenerate extends ContentDevelGenerate {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    // Force publication content type only.
    $form['node_types']['#options'] = array_intersect_key($form['node_types']['#options'], ['publication' => 'publication']);
    $form['node_types']['#default_value'] = $form['node_types']['#options'];

    // Simplify generation form.
    $form['node_types']['#access'] = FALSE;
    $form['authors-wrap']['#access'] = FALSE;
    $form['max_comments']['#access'] = FALSE;
    $form['add_type_label']['#access'] = FALSE;
    $form['add_alias']['#access'] = FALSE;
    $form['add_statistics']['#access'] = FALSE;
    $form['language']['#access'] = FALSE;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function populateFields(EntityInterface $entity) {
    parent::populateFields($entity);
    if ($entity->hasField('field_doi')) {
      $entity->set('title', ucfirst($entity->label()));
      $entity->set('field_doi', self::getRandomDoi());
    }
  }

  /**
   * {@inheritDoc}
   */
  public static function getRandomDoi() {
    $sample_identifiers = [
      '10.1103/physrevb.98.035120',
      '10.1103/physrevb.96.195168',
      '10.1080/21663831.2019.1570980',
      '10.1080/21663831.2018.1463298',
      '10.1103/physrevb.98.104202',
      '10.1126/sciadv.aao6850',
      '10.1080/02670844.2019.1611195',
      '10.1039/c7qm00488e',
    ];
    return $sample_identifiers[array_rand($sample_identifiers)];
  }

}
