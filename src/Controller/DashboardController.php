<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController {
  #[Route('/', name: 'mtb_dashboard')]
  public function index(): Response {
    return $this->render('@PixiekatMolecularTumorBoard/dashboard/index.html.twig', []);
  }
}
