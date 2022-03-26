<?php

namespace Drupal\dummy_content\Plugin\DevelGenerate;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\devel_generate\Plugin\DevelGenerate\ContentDevelGenerate;

/**
 * Provides a PublicationsDevelGenerate plugin.
 *
 * @DevelGenerate(
 *   id = "dummy_content_publications",
 *   label = "publications with DOIs",
 *   description = "Generate a given number of Publications with real example
 *     DOIs.",
 *   url = "dummy_content_publications",
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
      '10.1103/physrevb.100.075144',
      '10.1103/physrevmaterials.2.074002',
      '10.1103/physrevmaterials.2.114401',
      '10.1103/physrevmaterials.3.053609',
      '10.1080/21663831.2020.1724204',
      '10.1039/d0nh00343c',
      '10.1002/adfm.202004613',
      '10.1103/physrevb.102.075111',
      '10.1016/j.trechm.2019.02.016',
      '10.1016/j.jmmm.2019.165642',
      '10.1016/j.ceramint.2019.07.339',
      '10.1016/j.mtchem.2020.100271',
      '10.1016/j.pmatsci.2020.100757',
      '10.1016/j.mtchem.2019.08.010',
      '10.1103/physrevb.104.035408',
      '10.1039/d0ta11103a',
      '10.1016/j.jcis.2021.03.115',
      '10.1103/physrevmaterials.5.056002',
      '10.1016/j.ceramint.2021.03.284',
      '10.1039/d1na00046b',
    ];
    return $sample_identifiers[array_rand($sample_identifiers)];
  }

}
