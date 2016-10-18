<?php

namespace Isi\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LugarTrabajoPers
 *
 * @ORM\Table(name="lugar_trabajo_pers")
 * @ORM\Entity(repositoryClass="Isi\ConfigBundle\Repository\LugarTrabajoPersRepository")
 */
class LugarTrabajoPers
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
     * @var \DateTime
     *
     * @ORM\Column(name="horario", type="time", options={"comment":"horario en que trabaja la persona en ese lugar"})
     */
    private $horario;

    /**
     * @var string
     *
     * @ORM\Column(name="funciones", type="text", options={"comment":"que tarea realiza en ese lugar"}))
     */
    private $funciones;

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
     * Set horario
     *
     * @param \DateTime $horario
     *
     * @return LugarTrabajoPers
     */
    public function setHorario($horario)
    {
        $this->horario = $horario;

        return $this;
    }

    /**
     * Get horario
     *
     * @return \DateTime
     */
    public function getHorario()
    {
        return $this->horario;
    }

    /**
     * Set funciones
     *
     * @param string $funciones
     *
     * @return LugarTrabajoPers
     */
    public function setFunciones($funciones)
    {
        $this->funciones = $funciones;

        return $this;
    }

    /**
     * Get funciones
     *
     * @return string
     */
    public function getFunciones()
    {
        return $this->funciones;
    }

    /**
     * Set usuarioCrea
     *
     * @param string $usuarioCrea
     *
     * @return LugarTrabajoPers
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
     * @return LugarTrabajoPers
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
     * @return LugarTrabajoPers
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
     * @return LugarTrabajoPers
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
     * @return LugarTrabajoPers
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
     * @return LugarTrabajoPers
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
     * @ORM\ManyToOne(targetEntity="Isi\PersonaBundle\Entity\Personas", inversedBy="lugTrabPers")
     * @ORM\JoinColumn(name="personas_id", referencedColumnName="id", nullable=false)
     */
    private $personas;

    /**
     * @ORM\ManyToOne(targetEntity="LugarTrabajo", inversedBy="lugarTrabajo")
     * @ORM\JoinColumn(name="lugar_trabajo_id", referencedColumnName="id", nullable=false)
     */
    private $lugarTrab;

    /**
     * Set personas
     *
     * @param \Isi\PersonaBundle\Entity\Personas $personas
     *
     * @return LugarTrabajoPers
     */
    public function setPersonas(\Isi\PersonaBundle\Entity\Personas $personas)
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

    /**
     * Set lugarTrab
     *
     * @param \Isi\ConfigBundle\Entity\LugarTrabajo $lugarTrab
     *
     * @return LugarTrabajoPers
     */
    public function setLugarTrab(\Isi\ConfigBundle\Entity\LugarTrabajo $lugarTrab)
    {
        $this->lugarTrab = $lugarTrab;

        return $this;
    }

    /**
     * Get lugarTrab
     *
     * @return \Isi\ConfigBundle\Entity\LugarTrabajo
     */
    public function getLugarTrab()
    {
        return $this->lugarTrab;
    }
}
