<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Services;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationLoader {
  public function __construct(
    private TranslatorInterface $translator,
    private string $translationDir
  ) { }

  public function loadTranslations(): void {
    $loader = new YamlFileLoader();
    $loader->load($this->translationDir . '/messages.en.yaml', 'en', $this->translator);
  }
}