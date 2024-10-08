<?php
declare(strict_types=1);
namespace Pixiekat\MolecularTumorBoard\Entity;

use Pixiekat\MolecularTumorBoard\Repository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Repository\TumorRepository::class)]
#[ORM\Table(name: "mtb_tumors")]
class Tumor {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: "integer")]
  private $id;

  #[ORM\Column(type: "string", length: 255)]
  private $name;

  public function getId(): ?int {
    return $this->id;
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): self {
    $this->name = $name;
    return $this;
  }
}