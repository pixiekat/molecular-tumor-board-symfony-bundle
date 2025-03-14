<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Pixiekat\MolecularTumorBoard\Entity;
use Pixiekat\MolecularTumorBoard\Form;
use Pixiekat\MolecularTumorBoard\Services;
use Symfony\Component\Form\Extension\Core\Type as FormTypes;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController {

  public function __construct(
    private EntityManagerInterface $entityManager,
    private TranslatorInterface $translator,
  ) {}

  #[Route('/', name: 'mtb_dashboard')]
  public function index(Request $request): Response {

    $session = $request->getSession();
    $session->set('foo', 'bar');
    dump("Session foo is set to: " . $session->get('foo'));

    $form = $this->createForm(Form\MolecularTumorBoardPatientFormType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      if ($form->get('cancel')->isClicked()) {
        $session->remove('patient');
        $this->addFlash('info', 'Patient addition cancelled');
        return $this->redirectToRoute('mtb_dashboard');
      }

      if (!$form->isValid()) {
        $this->addFlash('error', 'Please correct the errors below');
      }
      else {
        $data = $form->getData();
        if (!empty($data['patient'])) {
          $session->set('patient', $data['patient']);
        }
        $this->addFlash('success', 'Patient added successfully');
        return $this->redirectToRoute('mtb_tumors');
      }
    }
    return $this->render('@PixiekatMolecularTumorBoard/dashboard/index.html.twig', [
      'form' => $form->createView() ?? [],
      'patient' => $session->get('patient') ?? null,
    ]);
  }

  #[Route('/tumors', name: 'mtb_tumors')]
  public function tumors(Services\FhirGenOpsApiService $fhirGenOpsApiService, EntityManagerInterface $entityManager, Request $request): Response {

    $session = $request->getSession();
    if (empty($session->get('patient'))) {
      $this->addFlash('error', 'Please add a patient first');
      return $this->redirectToRoute('mtb_dashboard');
    }
    $patient = $session->get('patient');
    $fhirGenOpsApiService->getFhirGenOpsApi()->setEndpoint('https://fhir-gen-ops.herokuapp.com/');
    //NC_000002.12:29213993:A:C
    //NC_000019.9:11200138-11244496
    //$variants = $fhirGenOpsApiService->getVariantsBySubjectAndRangeItem($patient, 'NC_000002.11:29415639-30144452', true);
    //dump($variants);

    $ranges = ['ALK', 'BRAF', 'EGFR', 'ERBB2', 'KRAS', 'MET'];
    $form = $this->createFormBuilder(['ranges' => implode(',', $ranges)])
      ->add('ranges', FormTypes\TextType::class, [
        'label' => $this->translator->trans('Ranges'),
      ])
      ->add('submit', FormTypes\SubmitType::class, [
        'attr' => [
          'class' => 'btn-primary',
        ],
        'label' => $this->translator->trans('Go!'),
      ])
      ->add('cancel', FormTypes\SubmitType::class, [
        'attr' => [
          'class' => 'btn-link',
        ],
        'label' => $this->translator->trans('Reset'),
      ])
      ->getForm();
    $form->handleRequest($request);
    $coordinates = $variants = [];
    if ($form->isSubmitted() && $form->get('cancel')->isClicked()) {
      return $this->redirectToRoute('mtb_tumors');
    }
    if ($form->isSubmitted() && $form->isValid()) {
      $data = $form->getData();
      $ranges = explode(',', $data['ranges']);
    }

    $variants = [];
    //foreach ($ranges as $range) {
      //$variants[] = $fhirGenOpsApiService->getVariantsBySubjectAndRangeItem($patient, $range, true);
    //}
    //$ranges = ['ALK', 'BRAF', 'EGFR', 'ERBB2', 'KRAS', 'MET', 'NTRK1', 'NTRK2', 'NTRK3', 'RET', 'ROS1', 'APC', 'NC_000001.11:11794399-11794400'];

    foreach ($ranges as $range) {
      if (preg_match('/^NC_\d{6}\.\d+:(\d+)-(\d+)$/', $range, $matches)) {
        $result = $fhirGenOpsApiService->lookupBuildCoordinatesByRangeOrGene(null, $matches[0]);
      }
      else {
        $result = $fhirGenOpsApiService->lookupBuildCoordinatesByRangeOrGene($range, null);
      }
      if (isset($result['build37Coordinates'])) {
        $variantResult = $fhirGenOpsApiService->getVariantsBySubjectAndRangeItem($patient, $result['build37Coordinates']);
        $variants = array_merge($variants, $variantResult);
      }
      $coordinates[$range] = $result;
    }

    /*
    $tumor = new Entity\Tumor();

    $form = $this->createForm(Form\TumorFormType::class, $tumor, [
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager = $this->entityManager;
      $entityManager->persist($tumor);
      $entityManager->flush();
      $this->addFlash('success', 'Tumor added successfully');
      return $this->redirectToRoute('mtb_tumors');
    }
    */

    return $this->render('@PixiekatMolecularTumorBoard/dashboard/tumors.html.twig', [
      'form' => $form->createView(),
      'patient' => $session->get('patient') ?? null,
      'variants' => $variants ?? [],
    ]);
  }
}
