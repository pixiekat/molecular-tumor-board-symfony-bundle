<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Entity\Interfaces;

interface VariantInterface {

  /**
   * Gets the presence of this variant.
   *
   * @return string
   */
  public function getPresence(): string;

/**
   * Gets the range item of the variant.
   *
   * @return string
   */
  public function getRangeItem(): string;

  /**
   * Gets the raw variant data.
   *
   * @return array
   */
  public function getRawVariantData(): array;

  /**
   * Sets the presence for this variant.
   * 
   * @param bool $presence
   * @return self
   */
  public function setPresence(bool $presence): static;

  /**
   * Sets the range item for this variant.
   * 
   * @param string $rangeItem
   * @return self
   */
  public function setRangeItem(string $rangeItem): static;

  /**
   * Sets the raw variant data.
   * 
   * @param array $rawVariantData
   * @return self
   */
  public function setRawVariantData(array $rawVariantData): self;
}