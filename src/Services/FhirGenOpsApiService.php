<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Services;

use Pixiekat\FhirGenOpsApi\FhirGenOpsApi;

class FhirGenOpsApiService {

  /**
   * The Pixiekat\FhirGenOpsApi\FhirGenOpsApi definition.
   * 
   * \Pixiekat\FhirGenOpsApi\FhirGenOpsApi $fhirGenOpsApi
   */
  private FhirGenOpsApi $fhirGenOpsApi;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->fhirGenOpsApi = new FhirGenOpsApi();
  }

  /**
   * {@inheritdoc}
   */
  public function getFhirGenOpsApi(): FhirGenOpsApi {
    return $this->fhirGenOpsApi;
  }

  /**
   * Sets the FhirGenOps API endpoint.
   *
   * @param string $endpoint
   */
  public function setFhirGenOpsApiEndpoint(string $endpoint): static {
    $this->fhirGenOpsApi->setEndpoint($endpoint);
  }
}