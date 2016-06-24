<?php

namespace Isi\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TiposMensaje
 *
 * @ORM\Table(name="tipos_mensaje")
 * @ORM\Entity(repositoryClass="Isi\ConfigBundle\Repository\TiposMensajeRepository")
 */
class TiposMensaje
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
     * @ORM\Column(name="descrip", type="string", length=20)
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
     * Set descrip
     *
     * @param string $descrip
     *
     * @return TiposMensaje
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
}

