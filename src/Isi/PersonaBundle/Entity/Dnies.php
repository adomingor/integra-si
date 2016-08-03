<?php

namespace Isi\PersonaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dnies
 *
 * @ORM\Table(name="dnies",
 * indexes={
 * @ORM\Index(name="ind_dnies_numero", columns={"numero"})
 * },
 * uniqueConstraints={@ORM\UniqueConstraint(name="uk_dnies_numero", columns={"numero"}, options={"where":"numero > 1000000"})
 * }
 * )
 * @ORM\Entity(repositoryClass="Isi\PersonaBundle\Repository\DniesRepository")
 */
class Dnies
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
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
     * @var string
     *
     * @ORM\Column(name="usuario_crea", type="string", length=25)
     */
    private $usuario_crea;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_crea", type="string", length=25)
     */
    private $ip_crea;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_crea", type="datetimetz")
     */
    private $fecha_crea;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_actu", type="string", length=25)
     */
    private $usuario_actu;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_actu", type="string", length=25)
     */
    private $ip_actu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_actu", type="datetimetz")
     */
    private $fecha_actu;


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

    /**
     * Set usuarioCrea
     *
     * @param string $usuarioCrea
     *
     * @return Dnies
     */
    public function setUsuarioCrea($usuarioCrea)
    {
        $this->usuario_crea = $usuarioCrea;

        return $this;
    }

    /**
     * Get usuarioCrea
     *
     * @return string
     */
    public function getUsuarioCrea()
    {
        return $this->usuario_crea;
    }

    /**
     * Set ipCrea
     *
     * @param string $ipCrea
     *
     * @return Dnies
     */
    public function setIpCrea($ipCrea)
    {
        $this->ip_crea = $ipCrea;

        return $this;
    }

    /**
     * Get ipCrea
     *
     * @return string
     */
    public function getIpCrea()
    {
        return $this->ip_crea;
    }

    /**
     * Set fechaCrea
     *
     * @param \DateTime $fechaCrea
     *
     * @return Dnies
     */
    public function setFechaCrea($fechaCrea)
    {
        $this->fecha_crea = $fechaCrea;

        return $this;
    }

    /**
     * Get fechaCrea
     *
     * @return \DateTime
     */
    public function getFechaCrea()
    {
        return $this->fecha_crea;
    }

    /**
     * Set usuarioActu
     *
     * @param string $usuarioActu
     *
     * @return Dnies
     */
    public function setUsuarioActu($usuarioActu)
    {
        $this->usuario_actu = $usuarioActu;

        return $this;
    }

    /**
     * Get usuarioActu
     *
     * @return string
     */
    public function getUsuarioActu()
    {
        return $this->usuario_actu;
    }

    /**
     * Set ipActu
     *
     * @param string $ipActu
     *
     * @return Dnies
     */
    public function setIpActu($ipActu)
    {
        $this->ip_actu = $ipActu;

        return $this;
    }

    /**
     * Get ipActu
     *
     * @return string
     */
    public function getIpActu()
    {
        return $this->ip_actu;
    }

    /**
     * Set fechaActu
     *
     * @param \DateTime $fechaActu
     *
     * @return Dnies
     */
    public function setFechaActu($fechaActu)
    {
        $this->fecha_actu = $fechaActu;

        return $this;
    }

    /**
     * Get fechaActu
     *
     * @return \DateTime
     */
    public function getFechaActu()
    {
        return $this->fecha_actu;
    }

    /**
     * @ORM\OneToOne(targetEntity="Personas", cascade={"persist"})
     * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
     */
     public $personas;

    /**
     * Set personas
     *
     * @param \Isi\PersonaBundle\Entity\Personas $personas
     *
     * @return Dnies
     */
    public function setPersonas(\Isi\PersonaBundle\Entity\Personas $personas = null)
    {
        $this->personas = $personas;

        return $this;
    }

    /**
     * Get personas
     *
     * @return \Isi\PersonaBundle\Entity\Personas
     */
    public function getPersonas()
    {
        return $this->personas;
    }
}
