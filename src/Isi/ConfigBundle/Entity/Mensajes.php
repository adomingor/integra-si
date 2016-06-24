<?php

namespace Isi\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mensajes
 *
 * @ORM\Table(name="mensajes")
 * @ORM\Entity(repositoryClass="Isi\ConfigBundle\Repository\MensajesRepository")
 */
class Mensajes
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=70, nullable=true)
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
     * @ORM\OneToOne(targetEntity="TiposMensaje")
     * @ORM\JoinColumn(name="tipoMsj_id", referencedColumnName="id")
     */
     private $tipoMsj;


    /**
     * Set tipoMsj
     *
     * @param \Isi\ConfigBundle\Entity\TiposMensaje $tipoMsj
     *
     * @return Mensajes
     */
    public function setTipoMsj(\Isi\ConfigBundle\Entity\TiposMensaje $tipoMsj = null)
    {
        $this->tipoMsj = $tipoMsj;

        return $this;
    }

    /**
     * Get tipoMsj
     *
     * @return \Isi\ConfigBundle\Entity\TiposMensaje
     */
    public function getTipoMsj()
    {
        return $this->tipoMsj;
    }
}
