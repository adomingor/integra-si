<?php

namespace Isi\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mensajes
 *
 * @ORM\Table(name="mensajes")
 * @ORM\Entity(repositoryClass="Isi\AdminBundle\Repository\MensajesRepository")
 */
class Mensajes
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
     * @ORM\Column(name="titulo", type="string", length=70)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="descrip", type="text")
     */
    private $descrip;


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
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Mensajes
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set descrip
     *
     * @param string $descrip
     *
     * @return Mensajes
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
     * @ORM\ManyToOne(targetEntity="TiposMensaje")
     * @ORM\JoinColumn(name="tiposMensaje_id", referencedColumnName="id", nullable=false)
     */
    private $tipoMensaje;

    /**
     * Set tipoMensaje
     *
     * @param \Isi\AdminBundle\Entity\TiposMensaje $tipoMensaje
     *
     * @return Mensajes
     */
    public function setTipoMensaje(\Isi\AdminBundle\Entity\TiposMensaje $tipoMensaje = null)
    {
        $this->tipoMensaje = $tipoMensaje;

        return $this;
    }

    /**
     * Get tipoMensaje
     *
     * @return \Isi\AdminBundle\Entity\TiposMensaje
     */
    public function getTipoMensaje()
    {
        return $this->tipoMensaje;
    }
}
