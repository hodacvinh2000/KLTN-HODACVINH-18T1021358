<?php

namespace App\Entity;

use App\Repository\BinhluannhiemvuRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BinhluannhiemvuRepository::class)
 */
class Binhluannhiemvu
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $binhluan;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Nhiemvu")
     * @ORM\JoinColumn(name="nhiemvu_id", referencedColumnName="id", nullable=false)
     */
    private $nhiemvu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="nguoibinhluan_id", referencedColumnName="id", nullable=false)
     */
    private $nguoibinhluan;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Binhluannhiemvu")
         * @ORM\JoinColumn(name="phanhoi_id", referencedColumnName="id", nullable=true)
     */
    private $phanhoi;

    /**
     * @ORM\Column(type="datetime")
     */
    private $thoigian;

    /**
     * @ORM\Column(type="integer")
     */
    private $sophanhoi;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBinhluan(): ?string
    {
        return $this->binhluan;
    }

    public function setBinhluan(string $binhluan): self
    {
        $this->binhluan = $binhluan;

        return $this;
    }

    public function getNhiemvu(): ?Nhiemvu
    {
        return $this->nhiemvu;
    }

    public function setNhiemvu(Nhiemvu $nhiemvu): self
    {
        $this->nhiemvu = $nhiemvu;

        return $this;
    }

    public function getNguoibinhluan(): ?User
    {
        return $this->nguoibinhluan;
    }

    public function setNguoibinhluan(User $nguoibinhluan): self
    {
        $this->nguoibinhluan = $nguoibinhluan;

        return $this;
    }

    public function getPhanhoi(): ?Binhluannhiemvu
    {
        return $this->phanhoi;
    }

    public function setPhanhoi(Binhluannhiemvu $phanhoi): self
    {
        $this->phanhoi = $phanhoi;

        return $this;
    }

    public function getThoigian(): ?\DateTimeInterface
    {
        return $this->thoigian;
    }

    public function setThoigian(\DateTimeInterface $thoigian): self
    {
        $this->thoigian = $thoigian;

        return $this;
    }

    public function getSophanhoi(): ?int
    {
        return $this->sophanhoi;
    }

    public function setSophanhoi(int $sophanhoi): self
    {
        $this->sophanhoi = $sophanhoi;

        return $this;
    }
}
