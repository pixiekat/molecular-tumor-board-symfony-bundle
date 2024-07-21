<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class MolecularTumorBoardBundle extends AbstractBundle {
  public function getPath(): string {
      return \dirname(__DIR__);
  }
}