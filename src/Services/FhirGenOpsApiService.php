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
   * The subject for this instance of the service.
   * 
   * @var string $subject
   */
  private ?string $subject = null;

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
   * {@inheritdoc}
   */
  public function getSubject(): ?string {
    return $this->subject;
  }
  /**
   * Sets the FhirGenOps API endpoint.
   *
   * @param string $endpoint
   */
  public function setFhirGenOpsApiEndpoint(string $endpoint): static {
    $this->fhirGenOpsApi->setEndpoint($endpoint);
  }

  /**
   * {@inheritdoc}
   */
  public function setSubject(string $subject): static {
    $this->subject = $subject;
    return $this;
  }
}