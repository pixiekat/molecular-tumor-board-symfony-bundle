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
   * Finds a component in a FHIR resource.
   *
   * @param array $data
   * @param string|null $system
   * @param string|null $code
   * @param string|null $display
   * @return array|null
   */
  public static function findComponent($data, $system = null, $code = null, $display = null);

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
   * Get variants given a subject and range item.
   * 
   * @param string $subject
   * @param string $rangeItem
   * @param bool $force
   * @return array
   */
  public function getVariantsBySubjectAndRangeItem(string $subject, string $rangeItem, $force = false): array;

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