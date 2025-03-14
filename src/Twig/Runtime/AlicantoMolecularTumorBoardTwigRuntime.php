<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Twig\Runtime;
use Pixiekat\MolecularTumorBoard\DependencyInjection\AlicantoMolecularTumorBoardExtension;
use Pixiekat\MolecularTumorBoard\Entity;
use Pixiekat\MolecularTumorBoard\Services;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\RuntimeExtensionInterface;

class AlicantoMolecularTumorBoardTwigRuntime implements RuntimeExtensionInterface {

  /**
   * The constructor for this runtime.
   */
  public function __construct(
    private Services\FhirGenOpsApiService $fhirGenOpsApi,
    private TwigEnvironment $twig,
  ) { }

  public function getGeneByVariant(Entity\Variant $variant): ?string {
    if (empty($variant->getGene())) {
      try {
        $rangeItem = $variant->getRangeItem();
        $results = $results = $this->fhirGenOpsApi->lookupBuildCoordinatesByRangeOrGene($gene = null, $range = $rangeItem);
        if ($results && !empty($results)) {
          if (isset($results['geneSymbol'])) {
            return $results['geneSymbol'];
          }
        }
      } catch (\Exception $e) {
        return null;
      }
    }
    return null;
  }

  public function getMolecularConsequences(Entity\Variant $variant): string {
    $consequences = $this->fhirGenOpsApi->getMolecularConsequences($variant);
    $template = $this->twig->render('@PixiekatMolecularTumorBoard/variants/molecular-consequence.html.twig', ['consequences' => $consequences]);
    return $template;
  }
}
