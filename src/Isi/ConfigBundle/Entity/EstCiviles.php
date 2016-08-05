<?php

namespace Isi\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstCiviles
 *
 * @ORM\Table(name="est_civiles")
 * @ORM\Entity(repositoryClass="Isi\ConfigBundle\Repository\EstCivilesRepository")
 */
class EstCiviles
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
     * @var string
     *
     * @ORM\Column(name="descrip", type="string", length=30, unique=true, options={"comment":"descripción del estado civil"})
     */
    private $descrip;

    /**
     * @var int
     *
     * @ORM\Column(name="codindec", type="integer", options={"comment":"código del INDEC para el estado civil"})
     */
    private $codindec;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_crea", type="string", length=25, options={"comment":"usuario que crea el registro"}))
     */
    private $usuario_crea;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_crea", type="string", length=25, nullable=true, options={"comment":"dirección IPV4 desde donde se crea el registro"})
     */
    private $ip_crea;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_crea", type="datetimetz", options={"comment":"fecha en la que se crea el registro"})
     */
    private $fecha_crea;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_actu", type="string", length=25, options={"comment":"usuario que actualiza el registro, la 1ra vez es el mismo usuario que lo crea, luego los disparadores se encargan de actualizar este campo"})
     */
    private $usuario_actu;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_actu", type="string", length=25, options={"comment":"dirección IPV4 desde donde se actualiza el registro, la 1ra vez es el mismo usuario que lo crea, luego los disparadores se encargan de actualizar este campo"})
     */
    private $ip_actu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_actu", type="datetimetz", options={"comment":"fecha en que se actualiza el registro, la 1ra vez es la misma fecha en que se lo crea, luego los disparadores se encargan de actualizar este campo"})
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
     * Set descrip
     *
     * @param string $descrip
     *
     * @return EstCiviles
     */
    public function setDescrip($descrip)
    {
        $this->descrip = $descrip;

        return $this;
    }

    /**
     * Get descrip
     *
     * @return string
     */
    public function getDescrip()
    {
        return $this->descrip;
    }

    /**
     * Set codindec
     *
     * @param integer $codindec
     *
     * @return EstCiviles
     */
    public function setCodindec($codindec)
    {
        $this->codindec = $codindec;

        return $this;
    }

    /**
     * Get codindec
     *
     * @return int
     */
    public function getCodindec()
    {
        return $this->codindec;
    }

    /**
     * Set usuarioCrea
     *
     * @param string $usuarioCrea
     *
     * @return EstCiviles
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
     * @return EstCiviles
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
     * @return EstCiviles
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
     * @return EstCiviles
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
     * @return EstCiviles
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
     * @return EstCiviles
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
}
