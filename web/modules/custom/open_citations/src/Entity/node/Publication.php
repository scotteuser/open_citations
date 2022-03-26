<?php

namespace Drupal\open_citations\Entity\node;

use Drupal\node\Entity\Node;

/**
 * Node subclass for Publication.
 *
 * @package Drupal\open_citations\Entity\Node
 */
class Publication extends Node {

  /**
   * Load the citations from the OpenCitations API.
   *
   * @return array
   *   An array of citations from OpenCitations.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function loadOpenCitationsCitations(): array {
    if (!$this->get('field_doi')->isEmpty()) {
      /** @var \Drupal\open_citations\OpenCitationsClient $open_citations_client */
      $open_citations_client = \Drupal::service('open_citations.client');
      return $open_citations_client->getCitationsForDoi($this->get('field_doi')->value);
    }
    return [];
  }

  /**
   * Update the local storage of citations from OpenCitations.
   */
  public function updateOpenCitationsCitations(): void {
    if ($citations = $this->loadOpenCitationsCitations()) {
      $data = [];
      foreach ($citations as $doi => $title) {
        $data[] = [
          'title' => $title,
          'doi' => $doi,
        ];
      }
      $this->set('field_citations', $data);
    }
  }

}
