<?php

namespace Isi\PersonaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dnies
 *
 * @ORM\Table(name="dnies")
 * @ORM\Entity(repositoryClass="Isi\PersonaBundle\Repository\DniesRepository")
 */
class Dnies
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="integer")
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="pulgarDcho", type="text", nullable=true)
     */
    private $pulgarDcho;

    /**
     * @var string
     *
     * @ORM\Column(name="foto", type="text", nullable=true)
     */
    private $foto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="femision", type="date", nullable=true)
     */
    private $femision;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fvto", type="date", nullable=true)
     */
    private $fvto;

    /**
     * @var int
     *
     * @ORM\Column(name="nrotramite", type="bigint", nullable=true)
     */
    private $nrotramite;

    /**
     * @var string
     *
     * @ORM\Column(name="ejemplar", type="string", length=1, nullable=true)
     */
    private $ejemplar;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=90, nullable=true)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="codqr", type="string", length=90, nullable=true)
     */
    private $codqr;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Dnies
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set pulgarDcho
     *
     * @param string $pulgarDcho
     *
     * @return Dnies
     */
    public function setPulgarDcho($pulgarDcho)
    {
        $this->pulgarDcho = $pulgarDcho;

        return $this;
    }

    /**
     * Get pulgarDcho
     *
     * @return string
     */
    public function getPulgarDcho()
    {
        return $this->pulgarDcho;
    }

    /**
     * Set foto
     *
     * @param string $foto
     *
     * @return Dnies
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * Get foto
     *
     * @return string
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Set femision
     *
     * @param \DateTime $femision
     *
     * @return Dnies
     */
    public function setFemision($femision)
    {
        $this->femision = $femision;

        return $this;
    }

    /**
     * Get femision
     *
     * @return \DateTime
     */
    public function getFemision()
    {
        return $this->femision;
    }

    /**
     * Set fvto
     *
     * @param \DateTime $fvto
     *
     * @return Dnies
     */
    public function setFvto($fvto)
    {
        $this->fvto = $fvto;

        return $this;
    }

    /**
     * Get fvto
     *
     * @return \DateTime
     */
    public function getFvto()
    {
        return $this->fvto;
    }

    /**
     * Set nrotramite
     *
     * @param integer $nrotramite
     *
     * @return Dnies
     */
    public function setNrotramite($nrotramite)
    {
        $this->nrotramite = $nrotramite;

        return $this;
    }

    /**
     * Get nrotramite
     *
     * @return int
     */
    public function getNrotramite()
    {
        return $this->nrotramite;
    }

    /**
     * Set ejemplar
     *
     * @param string $ejemplar
     *
     * @return Dnies
     */
    public function setEjemplar($ejemplar)
    {
        $this->ejemplar = $ejemplar;

        return $this;
    }

    /**
     * Get ejemplar
     *
     * @return string
     */
    public function getEjemplar()
    {
        return $this->ejemplar;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Dnies
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set codqr
     *
     * @param string $codqr
     *
     * @return Dnies
     */
    public function setCodqr($codqr)
    {
        $this->codqr = $codqr;

        return $this;
    }

    /**
     * Get codqr
     *
     * @return string
     */
    public function getCodqr()
    {
        return $this->codqr;
    }
}
