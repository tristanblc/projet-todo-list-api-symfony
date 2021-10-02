<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImagesRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *   normalizationContext  = {"groups" = {"user:read"}},
 *   denormalizationContext = {"groups" = {"user:write"}},
 *    itemOperations ={
 *        "get" ={
 *             "denormalization_context" = {"groups" = {"user:read"}}
 *        },
 *        "delete" = {
 *        "security" = "is_granted('ROLE_ADMIN')",
 *        "denormalization_context" = {"groups" = {"admin:read"}}},
 *        "put" ={
 *        "security" = "is_granted('ROLE_ADMIN')",
 *        "denormalization_context" = {"groups" = {"admin:write"}}}
 * 
 * 

 *     },
 *    collectionOperations={ 
 *          "get" = {
 *                "denormalization_context" = {"groups" = {"user:read"}}
 *           },
 *          "post" ={
 *             "method" = "POST",
 *             "security" = "is_granted('ROLE_ADMIN')",
 *             "denormalization_context" = {"groups" = {"admin:write"}},
 *             "validation_groups" = {"create"}
 *           },
 *         
 * }
 * )
 * @ORM\Entity(repositoryClass=ImagesRepository::class)
 */
class Images
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("admin:read")
     * @Groups("admin:write")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:read")
     * @Groups("admin:read")
     * @Groups("admin:write")
     */
    private $src;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:read")
     * @Groups("admin:read")
     * @Groups("admin:write")
     */
    private $libelle;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("user:read")
     * @Groups("admin:read")
     * @Groups("admin:write")
     */
    private $datetime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(string $src): self
    {
        $this->src = $src;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }
}
