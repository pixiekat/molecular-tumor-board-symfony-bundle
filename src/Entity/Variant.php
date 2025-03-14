<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Entity;

use Doctrine\ORM\Mapping as ORM;
use Pixiekat\MolecularTumorBoard\Entity\Interfaces;
use Pixiekat\MolecularTumorBoard\Services;

class Variant implements Interfaces\VariantInterface {

  /**
   * The Allelic state of the variant.
   *
   * @var string $allelicState
   */
  private string $allelicState = '';

  /**
   * The raw variant components.
   *
   * @var array $components
   */
  private array $components = [];

  /**
   * The gene of the variant.
   */
  private ?string $gene = null;

  /**
   * The range item of the variant.
   *
   * @var string $rangeItem
   */
  private string $rangeItem = '';

  /**
   * The presence of the variant.
   *
   * @var bool $presence
   */
  private bool $presence = false;

  /**
   * The subject of the variant.
   *
   * @var string $subject
   */
  private ?string $subject = null;

  /**
   * The raw variant data.
   *
   * @var array $rawVariantData
   */
  private array $rawVariantData = [];

  /**
   * The discrete genetic variant ID.
   *
   * @var string $variantId
   */
  private ?string $variantId = null;

  public function getAllelicState(): string {
    if (empty($this->allelicState)) {
      $part = $this->getRawVariantComponents();
      if ($allelicState = Services\FhirGenOpsApiService::findComponent($part, null, null, 'Allelic state')) {
        $variant->setAllelicState($allelicState['valueCodeableConcept']['coding'][0]['display']);
      }
    }
    return $this->allelicState;
  }

  /**
   * {@inheritdoc}
   */
  public function getGene(): ?string {
    return $this->gene ?? null;
  }

  /**
   * {@inheritdoc}
   */
  public function getPresence(): string {
    return $this->presence;
  }

  /**
   * {@inheritdoc}
   */
  public function getRangeItem(): string {
    return $this->rangeItem;
  }

  public function getRawVariantComponents(): array {
    return $this->components;
  }

  /**
   * {@inheritdoc}
   */
  public function getRawVariantData(): array {
    return $this->rawVariantData;
  }

  public function getSubject(): ?string {
    return $this->subject;
  }

  public function getVariantId(): ?string {
    if (empty($this->variantId)) {
      $part = $this->getRawVariantComponents();
      if ($variantId = Services\FhirGenOpsApiService::findComponent($part, null, null, 'Discrete genetic variant')) {
        $this->variantId = $variantId['valueCodeableConcept']['coding'][0]['code'];
      }
    }
    return $this->variantId;
  }

  public function setAllelicState(string $allelicState): static {
    $this->allelicState = $allelicState;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setGene(string $gene): static {
    $this->gene = $gene;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setPresence(bool $presence): static {
    $this->presence = $presence;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setRangeItem(string $rangeItem): static {
    $this->rangeItem = $rangeItem;
    return $this;
  }

  public function setRawVariantComponents(array $components): static {
    $this->components = $components;
    return $this;
  }

  /**
   *{@inheritdoc}
   */
  public function setRawVariantData(array $rawVariantData): static {
    $this->rawVariantData = $rawVariantData;
    return $this;
  }

  public function setSubject(string $subject): static {
    $this->subject = $subject;
    return $this;
  }

  public function setVariantId(string $variantId): static {
    $this->variantId = $variantId;
    return $this;
  }
}
