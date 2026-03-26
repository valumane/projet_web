<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: 'proj_produit')]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le libellé est obligatoire.')]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix unitaire est obligatoire.')]
    #[Assert\Positive(message: 'Le prix unitaire doit être strictement positif.')]
    private ?string $prixUnitaire = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La quantité en stock est obligatoire.')]
    #[Assert\Positive(message: 'La quantité en stock doit être strictement positive.')]
    private ?int $quantiteStock = null;

    /**
     * @var Collection<int, ContenuPanier>
     */
    #[ORM\OneToMany(targetEntity: ContenuPanier::class, mappedBy: 'produit')]
    private Collection $contenuPaniers;

    /**
     * @var Collection<int, Pays>
     */
    #[ORM\ManyToMany(targetEntity: Pays::class, inversedBy: 'produits')]
    #[ORM\JoinTable(name: 'proj_produit_pays')]
    #[ORM\JoinColumn(name: 'id_produit', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'id_pays', referencedColumnName: 'id')]
    private Collection $pays;

    public function __construct()
    {
        $this->contenuPaniers = new ArrayCollection();
        $this->pays = new ArrayCollection();
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

    /**
     * @return Collection<int, Pays>
     */
    public function getPays(): Collection
    {
        return $this->pays;
    }

    public function addPays(Pays $pays): static
    {
        if (!$this->pays->contains($pays)) {
            $this->pays->add($pays);
            $pays->addProduit($this);
        }

        return $this;
    }

    public function removePays(Pays $pays): static
    {
        if ($this->pays->removeElement($pays)) {
            $pays->removeProduit($this);
        }

        return $this;
    }
}