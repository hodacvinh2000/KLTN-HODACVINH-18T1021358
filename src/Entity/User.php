<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $tendangnhap;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $matkhau;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $hoten;

    /**
     * @ORM\Column(type="date")
     */
    private $ngaysinh;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     */
    private $sodt;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $gioitinh;

    /**
     * @ORM\Column(type="integer")
     */
    private $sodu;

    /**
     * @ORM\Column(type="integer")
     */
    private $quyen;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $verticalCode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTendangnhap(): ?string
    {
        return $this->tendangnhap;
    }

    public function setTendangnhap(string $tendangnhap): self
    {
        $this->tendangnhap = $tendangnhap;

        return $this;
    }

    public function getMatkhau(): ?string
    {
        return $this->matkhau;
    }

    public function setMatkhau(string $matkhau): self
    {
        $this->matkhau = $matkhau;

        return $this;
    }

    public function getHoten(): ?string
    {
        return $this->hoten;
    }

    public function setHoten(string $hoten): self
    {
        $this->hoten = $hoten;

        return $this;
    }

    public function getNgaysinh(): ?\DateTimeInterface
    {
        return $this->ngaysinh;
    }

    public function setNgaysinh(\DateTimeInterface $ngaysinh): self
    {
        $this->ngaysinh = $ngaysinh;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSodt(): ?string
    {
        return $this->sodt;
    }

    public function setSodt(string $sodt): self
    {
        $this->sodt = $sodt;

        return $this;
    }

    public function getGioitinh(): ?string
    {
        return $this->gioitinh;
    }

    public function setGioitinh(string $gioitinh): self
    {
        $this->gioitinh = $gioitinh;

        return $this;
    }

    public function getSodu(): ?int
    {
        return $this->sodu;
    }

    public function setSodu(int $sodu): self
    {
        $this->sodu = $sodu;

        return $this;
    }

    public function getQuyen(): ?int
    {
        return $this->quyen;
    }

    public function setQuyen(int $quyen): self
    {
        $this->quyen = $quyen;

        return $this;
    }

    public function getVerticalCode(): ?string
    {
        return $this->verticalCode;
    }

    public function setVerticalCode(string $verticalCode): self
    {
        $this->verticalCode = $verticalCode;

        return $this;
    }
}
