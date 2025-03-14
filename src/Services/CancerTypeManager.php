<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Services;
use Pixiekat\MolecularTumorBoard\Services\Interfaces;

class CancerTypeManager implements Interfaces\CancerTypeManagerInterface {

  /**
   * {@inheritDoc}
   */
  public function getCancerTypes(): array {
    return [
      'Breast Cancer' => [
        'Breast Carcinoma',
        'Breast Sarcoma',
      ],
    ];
  }
}
