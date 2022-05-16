<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tengame;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $anh;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTengame(): ?string
    {
        return $this->tengame;
    }

    public function setTengame(string $tengame): self
    {
        $this->tengame = $tengame;

        return $this;
    }

    public function getAnh(): ?string
    {
        return $this->anh;
    }

    public function setAnh(string $anh): self
    {
        $this->anh = $anh;

        return $this;
    }
}
