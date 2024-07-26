<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Twig\Runtime;
use Pixiekat\AlicantoLearning\DependencyInjection\AlicantoMolecularTumorBoardExtension;
use Pixiekat\AlicantoLearning\Entity;
use Pixiekat\AlicantoLearning\Services;
use Twig\Extension\RuntimeExtensionInterface;

class AlicantoMolecularTumorBoardTwigRuntime implements RuntimeExtensionInterface {
  
  /**
   * The constructor for this runtime.
   */
  public function __construct(
    //private Services\AlicantoLearningPathManager $learningPathManager,
  ) { }

  //public function getLearningPathManager(): Services\AlicantoLearningPathManager {
  //  return $this->learningPathManager;
  //}
}
