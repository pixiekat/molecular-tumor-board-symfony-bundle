<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoardBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Pixiekat\MolecularTumorBoardBundle\DependencyInjection\MolecularTumorBoardExtension;

class MolecularTumorBoardBundle extends AbstractBundle {
  public function getPath(): string {
      return \dirname(__DIR__);
  }

  public function getContainerExtension(): ?ExtensionInterface {
    return new MolecularTumorBoardExtension();
  }
}