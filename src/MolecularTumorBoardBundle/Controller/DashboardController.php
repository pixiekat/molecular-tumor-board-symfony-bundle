<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController {
  #[Route('/molecular-tumor-board', name: 'mtb_dashboard')]
  public function index(): Response {
    return $this->render('dashboard/index.html.twig', []);
  }
}
