<?php

namespace App\Entity;

use App\Repository\ThegameRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThegameRepository::class)
 */
class Thegame
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
    private $seri;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cardnumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=false)
     */
    private $game;

    /**
     * @ORM\Column(type="integer")
     */
    private $gia;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeri(): ?string
    {
        return $this->seri;
    }

    public function setSeri(string $seri): self
    {
        $this->seri = $seri;

        return $this;
    }

    public function getCardNumber(): ?string
    {
        return $this->cardnumber;
    }

    public function setCardNumber(string $cardnumber): self
    {
        $this->cardnumber = $cardnumber;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getGia(): ?int
    {
        return $this->gia;
    }

    public function setGia(int $gia): self
    {
        $this->gia = $gia;

        return $this;
    }
}
