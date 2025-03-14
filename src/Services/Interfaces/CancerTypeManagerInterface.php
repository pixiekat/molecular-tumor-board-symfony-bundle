<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Services\Interfaces;

interface CancerTypeManagerInterface {

  /**
   * Gets the cancer types.
   *
   * @return array
   */
  public function getCancerTypes(): array;
}
