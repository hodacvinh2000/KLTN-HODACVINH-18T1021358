<?php

namespace App\Entity;

use App\Repository\AnhRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnhRepository::class)
 */
class Anh
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Taikhoangame")
     * @ORM\JoinColumn(name="tkgame_id", referencedColumnName="id", nullable=false)
     */
    private $tkgame;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }
    public function getTkgame(): ?Taikhoangame
    {
        return $this->tkgame;
    }

    public function setGame(Taikhoangame $tkgame): self
    {
        $this->tkgame = $tkgame;

        return $this;
    }
}
