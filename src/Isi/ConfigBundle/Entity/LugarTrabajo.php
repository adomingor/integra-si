<?php

namespace Isi\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LugarTrabajo
 *
 * @ORM\Table(name="lugar_trabajo")
 * @ORM\Entity(repositoryClass="Isi\ConfigBundle\Repository\LugarTrabajoRepository")
 */
class LugarTrabajo
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
     * @ORM\Column(name="descrip", type="string", length=100, unique=true, options={"comment":"nombre del lugar (dpto de informatica, direccion de niñez adol. y flia., etc.)"})
     */
    private $descrip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado", type="date")
     */
    private $creado;

    /**
     * @var string
     *
     * @ORM\Column(name="objetivo", type="text")
     */
    private $objetivo;

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
     * Set descrip
     *
     * @param string $descrip
     *
     * @return LugarTrabajo
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
     * Set creado
     *
     * @param \DateTime $creado
     *
     * @return LugarTrabajo
     */
    public function setCreado($creado)
    {
        $this->creado = $creado;

        return $this;
    }

    /**
     * Get creado
     *
     * @return \DateTime
     */
    public function getCreado()
    {
        return $this->creado;
    }

    /**
     * Set objetivo
     *
     * @param string $objetivo
     *
     * @return LugarTrabajo
     */
    public function setObjetivo($objetivo)
    {
        $this->objetivo = $objetivo;

        return $this;
    }

    /**
     * Get objetivo
     *
     * @return string
     */
    public function getObjetivo()
    {
        return $this->objetivo;
    }

    /**
     * Set usuarioCrea
     *
     * @param string $usuarioCrea
     *
     * @return LugarTrabajo
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
     * @return LugarTrabajo
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
     * @return LugarTrabajo
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
     * @return LugarTrabajo
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
     * @return LugarTrabajo
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
     * @return LugarTrabajo
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
     * @ORM\ManyToOne(targetEntity="TiposLugarTrabajo")
     * @ORM\JoinColumn(name="tipo_lugar_trabajo_id", referencedColumnName="id", nullable=false)
     */
    private $tiposLugarTrabajo;

    /**
     * Set tiposLugarTrabajo
     *
     * @param \Isi\ConfigBundle\Entity\TiposLugarTrabajo $tiposLugarTrabajo
     *
     * @return LugarTrabajo
     */
    public function setTiposLugarTrabajo(\Isi\ConfigBundle\Entity\TiposLugarTrabajo $tiposLugarTrabajo)
    {
        $this->tiposLugarTrabajo = $tiposLugarTrabajo;

        return $this;
    }

    /**
     * Get tiposLugarTrabajo
     *
     * @return \Isi\ConfigBundle\Entity\TiposLugarTrabajo
     */
    public function getTiposLugarTrabajo()
    {
        return $this->tiposLugarTrabajo;
    }

    /**
     * @ORM\OneToMany(targetEntity="LugarTrabajoPers", mappedBy="lugarTrabajo")
     */
    private $lugarTrabPers;

    public function __construct() {
        $this->lugarTrabPers = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
