<?php

namespace Isi\PersonaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IdentGeneros
 *
 * @ORM\Table(name="ident_generos")
 * @ORM\Entity(repositoryClass="Isi\PersonaBundle\Repository\IdentGenerosRepository")
 */
class IdentGeneros
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
     * @ORM\Column(name="genero", type="string", length=30, unique=true, options={"comment":"nombre del género"})
     */
    private $genero;

    /**
     * @var string
     *
     * @ORM\Column(name="descrip", type="text", options={"comment":"descripción del género"})
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
     * Set genero
     *
     * @param string $genero
     *
     * @return IdentGeneros
     */
    public function setGenero($genero)
    {
        $this->genero = $genero;

        return $this;
    }

    /**
     * Get genero
     *
     * @return string
     */
    public function getGenero()
    {
        return $this->genero;
    }

    /**
     * Set descrip
     *
     * @param string $descrip
     *
     * @return IdentGeneros
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
