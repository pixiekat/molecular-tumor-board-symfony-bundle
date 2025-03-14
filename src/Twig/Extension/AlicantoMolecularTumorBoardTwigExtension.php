<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Twig\Extension;

use Pixiekat\MolecularTumorBoard\Twig\Runtime\AlicantoMolecularTumorBoardTwigRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AlicantoMolecularTumorBoardTwigExtension extends AbstractExtension {
  public function getFilters(): array {
    return [
      new TwigFilter('find_gene', [AlicantoMolecularTumorBoardTwigRuntime::class, 'getGeneByVariant']),
      new TwigFilter('find_molecular_consequences', [AlicantoMolecularTumorBoardTwigRuntime::class, 'getMolecularConsequences']),
    ];
  }

  public function getFunctions(): array {
    return [
      //new TwigFunction('get_learning_path_manager', [AlicantoMolecularTumorBoardTwigExtension::class, 'getLearningPathManager']),
    ];
  }
}
