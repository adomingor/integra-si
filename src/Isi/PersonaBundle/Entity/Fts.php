<?php

namespace Isi\PersonaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

 /**
  * Fts
  *
  * @ORM\Table(name="fts", indexes={@ORM\Index(name="ind_fts_vector_tsv", columns={"vector"})})
  * @ORM\Entity(repositoryClass="Isi\PersonaBundle\Repository\FtsRepository")
  */
class Fts
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
     * @var tsvector
     *
     * @ORM\Column(name="vector", type="tsvector")
     */
    private $vector;

    /**
     * @var string
     *
     * @ORM\Column(name="domicilio", type="text", nullable=true)
     */
    private $domicilio;


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
     * Set vector
     *
     * @param tsvector $vector
     *
     * @return Fts
     */
    public function setVector($vector)
    {
        $this->vector = $vector;

        return $this;
    }

    /**
     * Get vector
     *
     * @return tsvector
     */
    public function getVector()
    {
        return $this->vector;
    }

    /**
     * Set domicilio
     *
     * @param string $domicilio
     *
     * @return Fts
     */
    public function setDomicilio($domicilio)
    {
        $this->domicilio = $domicilio;

        return $this;
    }

    /**
     * Get domicilio
     *
     * @return string
     */
    public function getDomicilio()
    {
        return $this->domicilio;
    }

    /**
     * @ORM\OneToOne(targetEntity="Personas")
     * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
     **/

    private $persona;

    /**
     * Set persona
     *
     * @param \Isi\PersonaBundle\Entity\Personas $persona
     *
     * @return Fts
     */
    public function setPersona(\Isi\PersonaBundle\Entity\Personas $persona = null)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return \Isi\PersonaBundle\Entity\Personas
     */
    public function getPersona()
    {
        return $this->persona;
    }
}
