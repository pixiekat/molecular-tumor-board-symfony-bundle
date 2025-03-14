<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Services;

use Doctrine\ORM\EntityManagerInterface;
use Pixiekat\FhirGenOpsApi\FhirApi;
use Pixiekat\FhirGenOpsApi\FhirGenOpsApi;
use Pixiekat\MolecularTumorBoard\Entity;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class FhirGenOpsApiService {

  /**
   * The Pixiekat\FhirGenOpsApi\FhirApi definition.
   *
   * \Pixiekat\FhirGenOpsApi\FhirApi $fhirApi
   */
  private FhirApi $fhirApi;

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
  public function __construct(
    protected EntityManagerInterface $entityManager,
    protected TagAwareCacheInterface $cache,
    protected RequestStack $requestStack,
    protected Logger $logger,
  ) {
    $this->fhirGenOpsApi = new FhirGenOpsApi();
    $this->fhirApi = new FhirApi();
  }

  /**
   * {@inheritdoc}
   */
  public static function findComponent($data, $system = null, $code = null, $display = null) {
    if (!is_array($data)) {
      return null;
    }

    foreach ($data as $item) {
      if (isset($item['code']) && isset($item['code']['coding'])) {
        foreach ($item['code']['coding'] as $coding) {
          if (
            ($system && $coding['system'] === $system) ||
            ($code && $coding['code'] === $code) ||
            ($display && $coding['display'] === $display)
          ) {
            return $item;
          }
        }
      }
    }

    return null;
  }

  /**
   * {@inheritdoc}
   */
  public function getFhirApi(): FhirApi {
    return $this->fhirApi;
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
  public function getMolecularConsequences(Entity\Variant $variant, $force = false): ?array {
    $cacheBeta = ($force) ? INF : 1.0;
    $variantId = $variant->getVariantId();
    $subject = $variant->getSubject();
    $cacheKey = 'molecular_consequences_' . $subject . '_' . $variantId;
    $consequences = $this->cache->get($cacheKey, function (ItemInterface $item) use ($cacheBeta, $variant, $subject) {
      $expiresAt = (new \DateTime())->setTimeZone(new \DateTimeZone('America/New_York'))->setTimestamp(strtotime('+1 day'));
      $item->tag(['variants', 'subject_' . $subject]);

      $consequences = [];
      try {
        $results = $this->fhirGenOpsApi->findSubjectMolecularConsequences($subject, ['variants' => [$variant->getVariantId()]]);
        if (!empty($results) && isset($results['resourceType'])) {
          if ($results['resourceType'] == 'Parameters') {
            $parameters = $results['parameter'];
            foreach ($parameters as $parameter) {
              if ($parameter['name'] == 'consequence') {
                $resource = $parameter['resource'];
                $components = $resource['component'];
                $consequence = [];
                $feature_consequence = self::findComponent($components, null, 'feature-consequence', null);
                if ($feature_consequence) {
                  $consequence['feature_consequence'] = $feature_consequence['valueCodeableConcept']['coding'][0]['display'];
                }
                if (isset($resource['interpretation'])) {
                  $consequence['interpretation'] = $resource['interpretation'][0]['text'];
                }
                if (!empty($consequence)) {
                  $consequences[] = $consequence;
                }
              }
            }
          }
        }
      }
      catch (\Exception $e) {
        $this->logger->error('Error fetching molecular consequences', ['exception' => $e]);
        if ($cacheBeta <> INF) {
          $expiresAt = (new \DateTime())->setTimeZone(new \DateTimeZone('America/New_York'))->setTimestamp(strtotime('now'));
        }
      }
      return $consequences;
    }, $cacheBeta);
    return $consequences;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubject(): ?string {
    return $this->subject;
  }

  /**
   * {@inheritdoc}
   */
  public function getVariantsBySubjectAndRangeItem(string $subject, string $rangeItem, $force = false): array {
    $cacheBeta = ($force) ? INF : 1.0;
    $cacheKey = 'variants_by_subject_and_range_item_' . $subject . '_' . $rangeItem;
    $cacheItem = $this->cache->get($cacheKey, function (ItemInterface $item) use ($cacheBeta, $subject, $rangeItem) {
      $expiresAt = (new \DateTime())->setTimeZone(new \DateTimeZone('America/New_York'))->setTimestamp(strtotime('+1 hour'));
      $item->tag(['variants', 'subject_' . $subject]);

      $variants = [];
      try {
        $results = $this->fhirGenOpsApi->findSubjectVariants($subject, $rangeItem, $params = []);
        if (!empty($results)) {
          if (isset($results['resourceType']) && $results['resourceType'] == 'Parameters') {
            if (isset($results['parameter']) && !empty($results['parameter'])) {
              foreach ($results['parameter'] as $parameters) {
                if (isset($parameters['name']) && $parameters['name'] == 'variants') {
                  $parts = $parameters['part'];
                  $rangeItem = null;
                  $presence = false;
                  foreach ($parts as $id => $part) {
                    if ($part['name'] == 'rangeItem') {
                      $rangeItem = $part['valueString'];
                    }
                    if ($part['name'] == 'presence') {
                      $presence = $part['valueBoolean'];
                    }
                    if ($part['name'] == 'variant' && !is_null($rangeItem) && !is_null($presence)) {
                      $variant = (new Entity\Variant)->setPresence($presence)->setSubject($subject)->setRangeItem($rangeItem)->setRawVariantData($part['resource']);
                      $components = $part['resource']['component'];
                      if (!empty($components)) {
                        if ($allelicState = self::findComponent($components, null, null, 'Allelic state')) {
                          $variant->setAllelicState($allelicState['valueCodeableConcept']['coding'][0]['display']);
                        }
                        if ($variantId = self::findComponent($components, null, null, 'Discrete genetic variant')) {
                          $variant->setVariantId($variantId['valueCodeableConcept']['coding'][0]['code']);
                        }
                        $variant->setRawVariantComponents($components);
                      }
                      $variants[] = $variant;
                    }
                  }
                }
              }
            }
          }
        }
        return $variants;
      } catch (\Exception $e) {
        $this->logger->error('Error fetching variants by subject and range item', ['exception' => $e]);
        if ($cacheBeta <> INF) {
          $expiresAt = (new \DateTime())->setTimeZone(new \DateTimeZone('America/New_York'))->setTimestamp(strtotime('now'));
        }
        return $variants;
      }
    }, $cacheBeta);
    return $cacheItem;
  }

  /**
   * {@inheritdoc}
   */
  public function lookupBuildCoordinatesByRangeOrGene(string $gene = null, string $range = null, $force = false): array {
    $cacheBeta = ($force) ? INF : 1.0;
    $lookup = null;
    $findTheGene = false;
    if (!is_null($gene)) {
      $cacheKey = 'build_coordinates__coordinates_lookup__gene__%s';
      $lookup = $gene;
    }
    if (!is_null($range)) {
      $cacheKey = 'build_coordinates__find_the_gene_lookup__range__%s';
      $lookup = $range;
      $findTheGene = true;
    }

    $key = sprintf($cacheKey, str_ireplace(":", "__", $lookup));
    $cacheItem = $this->cache->get($key, function (ItemInterface $item) use ($cacheBeta, $key, $lookup, $findTheGene) {
      $expiresAt = (new \DateTime())->setTimeZone(new \DateTimeZone('America/New_York'))->setTimestamp(strtotime('+1 month'));
      $item->tag([$key]);

      $coordinates = [];
      try {
        if ($findTheGene) {
          $results = $this->fhirGenOpsApi->findTheGene($lookup);
        } else {
          $results = $this->fhirGenOpsApi->getFeatureCoordinates(null, $lookup, null, null);
        }
        if (!empty($results)) {
          $result = current($results);
          $coordinates =  $result;
        }
        return $coordinates;
      } catch (\Exception $e) {
        $this->logger->error('Error fetching build coordinates by range or gene', ['exception' => $e]);
        if ($cacheBeta <> INF) {
          $expiresAt = (new \DateTime())->setTimeZone(new \DateTimeZone('America/New_York'))->setTimestamp(strtotime('now'));
        }
        return $coordinates;
      }
      $item->expiresAfter($expiresAt);
    }, $cacheBeta);
    return $cacheItem;
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
