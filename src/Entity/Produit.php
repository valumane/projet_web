<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: 'proj_produit')]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixUnitaire = null;

    #[ORM\Column]
    private ?int $quantiteStock = null;

    /**
     * @var Collection<int, ContenuPanier>
     */
    #[ORM\OneToMany(targetEntity: ContenuPanier::class, mappedBy: 'produit')]
    private Collection $contenuPaniers;

    public function __construct()
    {
        $this->contenuPaniers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getQuantiteStock(): ?int
    {
        return $this->quantiteStock;
    }

    public function setQuantiteStock(int $quantiteStock): static
    {
        $this->quantiteStock = $quantiteStock;

        return $this;
    }

    /**
     * @return Collection<int, ContenuPanier>
     */
    public function getContenuPaniers(): Collection
    {
        return $this->contenuPaniers;
    }

    public function addContenuPanier(ContenuPanier $contenuPanier): static
    {
        if (!$this->contenuPaniers->contains($contenuPanier)) {
            $this->contenuPaniers->add($contenuPanier);
            $contenuPanier->setProduit($this);
        }

        return $this;
    }

    public function removeContenuPanier(ContenuPanier $contenuPanier): static
    {
        if ($this->contenuPaniers->removeElement($contenuPanier)) {
            if ($contenuPanier->getProduit() === $this) {
                $contenuPanier->setProduit(null);
            }
        }

        return $this;
    }
}