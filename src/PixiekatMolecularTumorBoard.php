<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Pixiekat\MolecularTumorBoard\DependencyInjection\MolecularTumorBoardExtension;

class PixiekatMolecularTumorBoard extends AbstractBundle {
  public function getPath(): string {
      return \dirname(__DIR__);
  }

  public function getContainerExtension(): ?ExtensionInterface {
    return new MolecularTumorBoardExtension();
  }
}