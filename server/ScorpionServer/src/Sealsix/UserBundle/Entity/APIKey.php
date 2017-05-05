<?php

namespace Sealsix\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * APIKey
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class APIKey
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="lifetime", type="integer")
     */
    private $lifetime;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="text")
     */
    private $hash;
    
    /**
     * @ORM\ManyToOne(targetEntity="Sealsix\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return APIKey
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set lifetime
     *
     * @param integer $lifetime
     *
     * @return APIKey
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * Get lifetime
     *
     * @return integer
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return APIKey
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
    
    public function __construct() {
        $date = new \DateTime();
        
        $this->setDate($date);
        $this->setLifetime(3600 * 24);
        $this->setHash(uniqid());
    }

    /**
     * Set user
     *
     * @param \Sealsix\UserBundle\Entity\User $user
     *
     * @return APIKey
     */
    public function setUser(\Sealsix\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Sealsix\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
