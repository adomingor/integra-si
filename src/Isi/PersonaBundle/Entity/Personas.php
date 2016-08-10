<?php

namespace Isi\PersonaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Personas
 *
 * @ORM\Table(name="personas",
 * indexes={
 * @ORM\Index(name="ind_personas_est_civiles", columns={"est_civil_id"}),
 * @ORM\Index(name="ind_personas_lugar_nacim", columns={"lugar_nacim_id"}),
 * @ORM\Index(name="ind_personas_fts", flags={"gin"}, columns={"fts"})
 * })
 * @ORM\Entity(repositoryClass="Isi\PersonaBundle\Repository\PersonasRepository")
 */
class Personas
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
     * @ORM\Column(name="apellido", type="string", length=45)
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=55)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="sexo", type="string", length=1)
     */
    private $sexo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fnac", type="date", nullable=true)
     */
    private $fnac;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ffallec", type="date", nullable=true)
     */
    private $ffallec;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=70, nullable=true)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="nn", type="boolean")
     */
    private $nn;

    /**
     * @var string
     *
     * @ORM\Column(name="descrip", type="text", nullable=true)
     */
    private $descrip;

    /**
     * @var string
     *
     * @ORM\Column(name="foto", type="text", nullable=true)
     */
    private $foto;

    /**
     * @var tsvector
     *
     * @ORM\Column(name="fts", type="tsvector", nullable=true)
     */
    private $fts;

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
     * Set apellido
     *
     * @param string $apellido
     *
     * @return Personas
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Personas
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set sexo
     *
     * @param string $sexo
     *
     * @return Personas
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return string
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set fnac
     *
     * @param \DateTime $fnac
     *
     * @return Personas
     */
    public function setFnac($fnac)
    {
        $this->fnac = $fnac;

        return $this;
    }

    /**
     * Get fnac
     *
     * @return \DateTime
     */
    public function getFnac()
    {
        return $this->fnac;
    }

    /**
     * Set ffallec
     *
     * @param \DateTime $ffallec
     *
     * @return Personas
     */
    public function setFfallec($ffallec)
    {
        $this->ffallec = $ffallec;

        return $this;
    }

    /**
     * Get ffallec
     *
     * @return \DateTime
     */
    public function getFfallec()
    {
        return $this->ffallec;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Personas
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set nn
     *
     * @param boolean $nn
     *
     * @return Personas
     */
    public function setNn($nn)
    {
        $this->nn = $nn;

        return $this;
    }

    /**
     * Get nn
     *
     * @return bool
     */
    public function getNn()
    {
        return $this->nn;
    }

    /**
     * Set descrip
     *
     * @param string $descrip
     *
     * @return Personas
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
     * Set foto
     *
     * @param string $foto
     *
     * @return Personas
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * Get foto
     *
     * @return string
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Set fts
     *
     * @param tsvector $fts
     *
     * @return Personas
     */
    public function setFts($fts)
    {
        $this->fts = $fts;

        return $this;
    }

    /**
     * Get fts
     *
     * @return tsvector
     */
    public function getFts()
    {
        return $this->fts;
    }

    /**
     * Set usuarioCrea
     *
     * @param string $usuarioCrea
     *
     * @return Personas
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
     * @return Personas
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
     * @return Personas
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
     * @return Personas
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
     * @return Personas
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
     * @return Personas
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
     * @ORM\ManyToOne(targetEntity="Isi\ConfigBundle\Entity\EstCiviles")
     * @ORM\JoinColumn(name="est_civil_id", referencedColumnName="id", nullable=false)
     **/
    private $estciviles;

    /**
     * @ORM\ManyToOne(targetEntity="Isi\ConfigBundle\Entity\LugarNacim")
     * @ORM\JoinColumn(name="lugar_nacim_id", referencedColumnName="id", nullable=false)
     **/
    private $lugarnacim;

    /**
     * @ORM\ManyToMany(targetEntity="Isi\ConfigBundle\Entity\IdentGeneros")
     * @ORM\JoinTable(name="personas_ident_generos",
     *      joinColumns={@ORM\JoinColumn(name="persona_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ident_genero_id", referencedColumnName="id", nullable=false)}
     *      )
     */
    private $identgeneros;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->identgeneros = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set estciviles
     *
     * @param \Isi\PersonaBundle\Entity\EstCiviles $estciviles
     *
     * @return Personas
     */
    public function setEstciviles(\Isi\ConfigBundle\Entity\EstCiviles $estciviles = null)
    {
        $this->estciviles = $estciviles;

        return $this;
    }

    /**
     * Get estciviles
     *
     * @return \Isi\PersonaBundle\Entity\EstCiviles
     */
    public function getEstciviles()
    {
        return $this->estciviles;
    }

    /**
     * Set lugarnacim
     *
     * @param \Isi\PersonaBundle\Entity\LugarNacim $lugarnacim
     *
     * @return Personas
     */
    public function setLugarnacim(\Isi\ConfigBundle\Entity\LugarNacim $lugarnacim = null)
    {
        $this->lugarnacim = $lugarnacim;

        return $this;
    }

    /**
     * Get lugarnacim
     *
     * @return \Isi\PersonaBundle\Entity\LugarNacim
     */
    public function getLugarnacim()
    {
        return $this->lugarnacim;
    }

    /**
     * Add identgenero
     *
     * @param \Isi\PersonaBundle\Entity\IdentGeneros $identgenero
     *
     * @return Personas
     */
    public function addIdentgenero(\Isi\ConfigBundle\Entity\IdentGeneros $identgenero)
    {
        $this->identgeneros[] = $identgenero;

        return $this;
    }

    /**
     * Remove identgenero
     *
     * @param \Isi\PersonaBundle\Entity\IdentGeneros $identgenero
     */
    public function removeIdentgenero(\Isi\ConfigBundle\Entity\IdentGeneros $identgenero)
    {
        $this->identgeneros->removeElement($identgenero);
    }

    /**
     * Get identgeneros
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdentgeneros()
    {
        return $this->identgeneros;
    }
}
