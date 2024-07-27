<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Services;

use Doctrine\ORM\EntityManagerInterface;
use Pixiekat\FhirGenOpsApi\FhirGenOpsApi;
use Pixiekat\MolecularTumorBoard\Entity;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class FhirGenOpsApiService {

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
  public function getFhirGenOpsApi(): FhirGenOpsApi {
    return $this->fhirGenOpsApi;
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
                  $rangeItem = $presence = null;
                  foreach ($parts as $id => $part) {
                    if ($part['name'] == 'rangeItem') {
                      $rangeItem = $part['valueString'];
                    }
                    if ($part['name'] == 'presence') {
                      $presence = $part['valueBoolean'];
                    }
                    if ($part['name'] == 'variant' && !is_null($rangeItem) && !is_null($presence)) {
                      $variant = (new Entity\Variant)->setPresence($presence)->setRangeItem($rangeItem)->setRawVariantData($part['resource']);
                      $components = $part['resource']['component'];
                      if (!empty($components)) {
                        if ($allelicState = self::findComponent($components, null, null, 'Allelic state')) {
                          $variant->setAllelicState($allelicState['valueCodeableConcept']['coding'][0]['display']);
                        }
                        if ($variantId = self::findComponent($components, null, null, 'Discrete genetic variant')) {
                          dump("variantId: " . $variantId['valueCodeableConcept']['coding'][0]['code']);
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