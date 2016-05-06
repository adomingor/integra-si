<?php

namespace Isi\PersonaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstCiviles
 *
 * @ORM\Table(name="est_civiles")
 * @ORM\Entity(repositoryClass="Isi\PersonaBundle\Repository\EstCivilesRepository")
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
}
