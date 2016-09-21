<?php

namespace Isi\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TiposGrSociales
 *
 * @ORM\Table(name="tipos_gr_sociales")
 * @ORM\Entity(repositoryClass="Isi\ConfigBundle\Repository\TiposGrSocialesRepository")
 */
class TiposGrSociales
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
     * @ORM\Column(name="tipo", type="string", length=30, unique=true, options={"comment":"nombre del tipo de grupo social"})
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="descrip", type="text", options={"comment":"descripción del grupo social"})
     */
    private $descrip;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_crea", type="string", length=25)
     */
    private $usuario_crea;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_crea", type="string", length=25, nullable=true)
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
     * @ORM\Column(name="ip_actu", type="string", length=25, nullable=true)
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
     * Set tipo
     *
     * @param string $tipo
     *
     * @return TiposGrSociales
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set descrip
     *
     * @param string $descrip
     *
     * @return TiposGrSociales
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
     * Set usuarioCrea
     *
     * @param string $usuarioCrea
     *
     * @return TiposGrSociales
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
     * @return TiposGrSociales
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
     * @return TiposGrSociales
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
     * @return TiposGrSociales
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
     * @return TiposGrSociales
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
     * @return TiposGrSociales
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
