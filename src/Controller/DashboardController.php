<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Pixiekat\MolecularTumorBoard\Entity;
use Pixiekat\MolecularTumorBoard\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController {

  public function __construct(private EntityManagerInterface $entityManager) {}

  #[Route('/', name: 'mtb_dashboard')]
  public function index(): Response {
    return $this->render('@PixiekatMolecularTumorBoard/dashboard/index.html.twig', []);
  }

  #[Route('/tumors', name: 'mtb_tumors')]
  public function tumors(EntityManagerInterface $entityManager, Request $request): Response {

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

    return $this->render('@PixiekatMolecularTumorBoard/dashboard/tumors.html.twig', [
      'form' => $form->createView(),
    ]);
  }
}
