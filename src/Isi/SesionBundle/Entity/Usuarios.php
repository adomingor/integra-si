<?php

namespace Isi\SesionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Usuarios
 *
 * @ORM\Table(name="usuarios")
 * @ORM\Entity(repositoryClass="Isi\SesionBundle\Repository\UsuariosRepository")
 */
 class Usuarios implements AdvancedUserInterface, \Serializable
 {
     /**
      * @var integer
      *
      * @ORM\Column(name="id", type="integer", nullable=false)
      * @ORM\Id
      * @ORM\GeneratedValue(strategy="IDENTITY")
      * @ORM\SequenceGenerator(sequenceName="usuarios_id_seq", allocationSize=1, initialValue=1)
      */
     private $id;

     /**
      * @ORM\Column(type="string", length=25, unique=true, nullable=false)
      */
     private $username;

     /**
      * @ORM\Column(type="string", length=64, nullable=false)
      */
     private $password;

     /**
      * @ORM\Column(type="string", length=64)
      */
      private $salt;

     /**
      * @ORM\Column(type="string", length=60, unique=true, nullable=false)
      */
     private $email;

     /**
      * @ORM\Column(name="is_active", type="boolean", nullable=false)
      */
     private $isActive;

     /**
      * @ORM\ManyToMany(targetEntity="Roles", inversedBy="usuarios")
      *
      */
     private $roles;

     /**
      * @var string
      *
      * @ORM\Column(name="imagen", type="text", nullable=true)
      */
     private $imagen;

     /**
      * @var string
      *
      * @ORM\Column(name="perselec", type="text", nullable=true)
      */
     private $perselec;

     /**
      * @var string
      *
      * @ORM\Column(name="menu_color", type="string", nullable=true)
      */
     private $menu_color;

     /**
      * @var string
      *
      * @ORM\Column(name="menu_opacidad", type="string", nullable=true)
      */
     private $menu_opacidad;

     /**
      * @var string
      *
      * @ORM\Column(name="menu_color_letra", type="string", nullable=true)
      */
     private $menu_color_letra;

     public function __construct()
     {
         $this->roles = new ArrayCollection();
     }

     /**
      * Get id
      *
      * @return integer
      */
     public function getId()
     {
         return $this->id;
     }

     /**
      * @inheritDoc
      */
     public function getUsername()
     {
         return $this->username;
     }

     /**
      * Set username
      *
      * @param string $username
      * @return Usuarios
      */
     public function setUsername($username)
     {
         $this->username = $username;

         return $this;
     }

     /**
      * @inheritDoc
      */
     public function getPassword()
     {
         return $this->password;
     }

     /**
      * Set password
      *
      * @param string $password
      * @return Usuarios
      */
     public function setPassword($password)
     {
         $this->password = $password;

         return $this;
     }

     /**
      * @inheritDoc
      */
     public function getSalt()
     {
         // you *may* need a real salt depending on your encoder
         // see section on salt below
         return null;
     }

     /**
      * Set salt
      *
      * @param string $salt
      *
      * @return Usuarios
      */
     public function setSalt($salt)
     {
         $this->salt = $salt;

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
      * Set email
      *
      * @param string $email
      * @return Usuarios
      */
     public function setEmail($email)
     {
         $this->email = $email;

         return $this;
     }

     /**
      * Get isActive
      *
      * @return boolean
      */
     public function getIsActive()
     {
         return $this->isActive;
     }

     /**
      * Set isActive
      *
      * @param boolean $isActive
      * @return Usuarios
      */
     public function setIsActive($isActive)
     {
         $this->isActive = $isActive;

         return $this;
     }

     /**
      * Get imagen
      *
      * @return string
      */
     public function getImagen()
     {
         return $this->imagen;
     }

     /**
      * Set imagen
      *
      * @param string $imagen
      * @return Usuarios
      */
     public function setImagen($imagen)
     {
         $this->imagen = $imagen;

         return $this;
     }

     /**
      * Set perselec
      *
      * @param string $perselec
      *
      * @return Usuarios
      */
     public function setPerselec($perselec)
     {
         $this->perselec = $perselec;

         return $this;
     }

     /**
      * Get perselec
      *
      * @return string
      */
     public function getPerselec()
     {
         return $this->perselec;
     }
 
     /**
      * Set menuColor
      *
      * @param string $menuColor
      *
      * @return Usuarios2
      */
     public function setMenuColor($menuColor)
     {
         $this->menu_color = $menuColor;

         return $this;
     }

     /**
      * Get menuColor
      *
      * @return string
      */
     public function getMenuColor()
     {
         return $this->menu_color;
     }

     /**
      * Set menuOpacidad
      *
      * @param string $menuOpacidad
      *
      * @return Usuarios2
      */
     public function setMenuOpacidad($menuOpacidad)
     {
         $this->menu_opacidad = $menuOpacidad;

         return $this;
     }

     /**
      * Get menuOpacidad
      *
      * @return string
      */
     public function getMenuOpacidad()
     {
         return $this->menu_opacidad;
     }

     /**
      * Set menuColorLetra
      *
      * @param string $menuColorLetra
      *
      * @return Usuarios2
      */
     public function setMenuColorLetra($menuColorLetra)
     {
         $this->menu_color_letra = $menuColorLetra;

         return $this;
     }

     /**
      * Get menuColorLetra
      *
      * @return string
      */
     public function getMenuColorLetra()
     {
         return $this->menu_color_letra;
     }

     /**
      * @inheritDoc
      */
     public function getRoles()
     {
         return $this->roles->toArray();
     }

     /**
      * @inheritDoc
      */
     public function eraseCredentials()
     {
     }

     /**
      * @see \Serializable::serialize()
      */
     public function serialize()
     {
         return serialize(array(
             $this->id,
             $this->username,
             $this->password,
             // see section on salt below
             // $this->salt,
         ));
     }

     /**
      * @see \Serializable::unserialize()
      */
     public function unserialize($serialized)
     {
         list (
             $this->id,
             $this->username,
             $this->password,
             // see section on salt below
             // $this->salt
         ) = unserialize($serialized);
     }

     public function isAccountNonExpired()
     {
         return true;
     }

     public function isAccountNonLocked()
     {
         return true;
     }

     public function isCredentialsNonExpired()
     {
         return true;
     }

     public function isEnabled()
     {
         return $this->isActive;
     }

     /**
      * Add roles
      *
      * @param \Isi\SesionBundle\Entity\Roles $roles
      * @return Usuarios
      */
     public function addRoles(\Isi\SesionBundle\Entity\Roles $roles)
     {
         $this->roles[] = $roles;

         return $this;
     }

     /**
      * Remove roles
      *
      * @param \Isi\SesionBundle\Entity\Roles $roles
      */
     public function removeRoles(\Isi\SesionBundle\Entity\Roles $roles)
     {
         $this->roles->removeElement($roles);
     }

     /**
      * Add roles
      *
      * @param \Isi\SesionBundle\Entity\Roles $roles
      * @return Usuarios
      */
     public function addRole(\Isi\SesionBundle\Entity\Roles $roles)
     {
         $this->roles[] = $roles;

         return $this;
     }

     /**
      * Remove roles
      *
      * @param \Isi\SesionBundle\Entity\Roles $roles
      */
     public function removeRole(\Isi\SesionBundle\Entity\Roles $roles)
     {
         $this->roles->removeElement($roles);
     }

     /**
       * @ORM\OneToOne(targetEntity="Isi\PersonaBundle\Entity\Personas")
       * @ORM\JoinColumn(name="persona_id", referencedColumnName="id", nullable=false)
       */
    private $persona;

    /**
     * Set persona
     *
     * @param \Isi\PersonaBundle\Entity\Personas $persona
     *
     * @return Usuarios
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
