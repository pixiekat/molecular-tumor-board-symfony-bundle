<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Services\Interfaces;

use Pixiekat\FhirGenOpsApi\FhirGenOpsApi;

interface FhirGenOpsApiInterface {

  /**
   * FhirGenOpsApiInterface constructor.
   *
   * @param FhirGenOpsApi $fhirGenOpsApi
   */
  public function __construct(FhirGenOpsApi $fhirGenOpsApi);

  /**
   * Returns the \Pixiekat\FhirGenOpsApi\FhirGenOpsApi instance.
   *
   * @return FhirGenOpsApi
   */
  public function getFhirGenOpsApi(): FhirGenOpsApi;

/**
   * Gets the subject for this instance of the service.
   * 
   * @return string
   */
  public function getSubject(): ?string;

  /**
   * Sets the FhirGenOps API endpoint.
   *
   * @param string $endpoint
   */
  public function setFhirGenOpsApiEndpoint(string $endpoint): static;

  /**
   * Sets the subject for this instance of the service.
   * 
   * @params string $subject
   * @return self
   */
  public function setSubject(string $subject): static;
}