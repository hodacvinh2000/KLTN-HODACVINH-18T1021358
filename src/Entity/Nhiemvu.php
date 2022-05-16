<?php

namespace App\Entity;

use App\Repository\NhiemvuRepository;
use Cassandra\Type\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NhiemvuRepository::class)
 */
class Nhiemvu
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=false)
     */
    private $game;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $noidung;

    /**
     * @ORM\Column(type="integer")
     */
    private $trangthai;

    /**
     * @ORM\Column(type="datetime")
     */
    private $ngaydang;


    /**
     * @ORM\Column(type="string", length=250)
     */
    private $tieude;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoidung(): ?string
    {
        return $this->noidung;
    }

    public function setNoidung(string $noidung): self
    {
        $this->noidung = $noidung;

        return $this;
    }

    public function getTrangthai(): ?int
    {
        return $this->trangthai;
    }

    public function setTrangthai(int $trangthai): self
    {
        $this->trangthai = $trangthai;

        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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

    public function getNgaydang(): ?\DateTimeInterface
    {
        return $this->ngaydang;
    }

    public function setNgaydang(\DateTimeInterface $ngaydang): self
    {
        $this->ngaydang = $ngaydang;

        return $this;
    }

    public function getTieude(): ?string
    {
        return $this->tieude;
    }

    public function setTieude(string $tieude): self
    {
        $this->tieude = $tieude;

        return $this;
    }
}
