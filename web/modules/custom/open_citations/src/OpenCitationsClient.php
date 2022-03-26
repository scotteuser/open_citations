<?php

namespace Drupal\open_citations;

use Drupal\Component\Serialization\Json;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Open Citations API client.
 *
 * @package Drupal\open_citations
 */
class OpenCitationsClient implements ContainerInjectionInterface {

  /**
   * GuzzleHttp client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Constructor.
   */
  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \GuzzleHttp\ClientInterface $http_client */
    $http_client = $container->get('http_client');
    return new static($http_client);
  }

  /**
   * Get citations of a given DOI.
   *
   * @param string $doi
   *   The DOI for the content item.
   *
   * @return array
   *   The citations array returned by OpenCitations.
   */
  public function getCitationsForDoi($doi) {
    $response = $this->httpClient->request('GET', 'https://opencitations.net/index/api/v1/citations/' . $doi);
    if ($response->getStatusCode() == 200) {

      // Get the identifiers to make a subsequent meta data call.
      $citations = $response->getBody()->getContents();
      $citations = Json::decode($citations);
      if (!$citations) {
        return [];
      }

      // For the sake of the demo, let's do a maximum of 5 citations.
      $max = 5;
      $results = [];

      // Build an array of DOIs of the citations for bulk retrieveal of the
      // metadata about those citations.
      foreach ($citations as &$citation) {
        if (!isset($citation['citing'])) {
          continue;
        }

        // Retrieve the DOI from the citation. This comes in the format
        // "coci => 10.1021/acsnano.9b07708" but the DOI is only after the "=>".
        $doi = substr($citation['citing'], strpos($citation['citing'], '=>') + 3);
        $doi = trim($doi);
        if (isset($results[$doi])) {
          continue;
        }

        // Get meta data.
        if (count($results) <= $max && $metadata = $this->getMetadataForDoi($doi)) {
          if (isset($metadata['title'])) {
            $results[$doi] = $metadata['title'];
          }
        }
      }
      return $results;
    }
    return [];
  }

  /**
   * Get metadata of a given DOI.
   *
   * @param string $doi
   *   The DOI for the citations.
   *
   * @return array
   *   The metadata array returned by OpenCitations.
   */
  public function getMetadataForDoi($doi) {
    $response = $this->httpClient->request('GET', 'https://w3id.org/oc/index/api/v1/metadata/' . $doi);
    if ($response->getStatusCode() == 200) {
      $results = $response->getBody()->getContents();
      $results = Json::decode($results);
      return reset($results);
    }
    return [];
  }

}