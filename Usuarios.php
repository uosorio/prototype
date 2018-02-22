<?php

namespace RDER\SFPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usuarios
 *
 * @ORM\Table(name="sfp.tblusuarios")
 * @ORM\Entity(repositoryClass="RDER\SFPBundle\Repository\UsuariosRepository")
 */
class Usuarios
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=255)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="area", type="string", length=255, nullable=true)
     */
    private $area;

    /**
     * @var int
     *
     * @ORM\Column(name="role", type="integer")
     */
    private $role;

    /**
     * @var bool
     *
     * @ORM\Column(name="estado", type="boolean")
     */
    private $estado;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *  
     * @ORM\ManyToMany(targetEntity="Cecos", inversedBy="usuarios")
     * @ORM\JoinTable(
     *  name="sfp.tblusuariocecos",
     *  joinColumns={
     *      @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="ceco", referencedColumnName="id")
     *  }
     * )
     */
    private $cecos;


    public function __construct()
    {
        $this->cecos = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    public function __toString()
    {
        return (String) $this->id;
    }
    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Usuarios
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
     * Set usuario
     *
     * @param string $usuario
     *
     * @return Usuarios
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuarios
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set area
     *
     * @param string $area
     *
     * @return Usuarios
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set role
     *
     * @param integer $role
     *
     * @return Usuarios
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return integer
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     *
     * @return Usuarios
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return bool
     */
    public function getEstado()
    {
        return $this->estado;
    }

    // /**
    //  * Set idcecos
    //  *
    //  * @param \RDER\SFPBundle\Entity\Cecos $idcecos
    //  * @return Usuarios
    //  */
    // public function setIdCecos(\RDER\SFPBundle\Entity\Cecos $idcecos = null)
    // {
    //     $this->idcecos = $idcecos;
    
    //     return $this;
    // }

    // /**
    //  * Get idcecos
    //  *
    //  * @return \RDER\SFPBundle\Entity\Cecos
    //  */
    // public function getIdCecos()
    // {
    //     return $this->idcecos;
    // }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|Cecos[]
     */
    public function getCecos()
    {
        return $this->cecos;
    }

    /**
     * @param Cecos $cecos
     */
    public function removeCecos(Cecos $cecos)
    {
        if (false === $this->cecos->contains($cecos)) {
            return;
        }
        $this->cecos->removeElement($cecos);
        $cecos->removeCategory($this);
    }

    /**
     * @param Cecos $cecos
     */
    public function addCecos(Cecos $cecos)
    {
        if (true === $this->cecos->contains($cecos)) {
            return;
        }
        $this->cecos->add($cecos);
        $cecos->addCategory($this);
    }


}

