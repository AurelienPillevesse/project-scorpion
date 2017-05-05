<?php

namespace Sealsix\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sealsix\UserBundle\Entity\User;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @ORM\MappedSuperclass
 * @ExclusionPolicy("all")
 */
abstract class Media {
    
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Expose
     */
    protected $nom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="auteur", type="string", length=255, nullable=true)
     * @Expose
     */
    protected $auteur;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Expose
     */
    protected $description;
    
    /**
     * @var string
     *
     * @ORM\Column(name="style", type="string", length=255, nullable=true)
     * @Expose
     */
    protected $style;
    
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Expose
     */
    protected $type;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePublication", type="datetime")
     * @Expose
     */
    private $datePublication;
    
    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    protected $path;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Sealsix\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Expose
     */    
    protected $owner;
    
    /**
     *
     * @Expose
     */
    protected $streamUrl;
    
    function getNom() {
        return $this->nom;
    }
    
    function getAuteur() {
        return $this->auteur;
    }
    
    function getDescription() {
        return $this->description;
    }
    
    function getStyle() {
        return $this->style;
    }
    
    function getType() {
        return $this->type;
    }
    
    function getPath() {
        return $this->path;
    }
    
    function getDatePublication() {
        return $this->datePublication;
    }
    
    function getOwner() {
        return $this->owner;
    }
    
    function getStreamUrl() {
        return $this->streamUrl;
    }
    
    function setNom($nom) {
        $this->nom = $nom;
    }
    
    function setAuteur($auteur) {
        $this->auteur = $auteur;
    }
    
    function setDatePublication($datePublication) {
        $this->datePublication = $datePublication;
    }
    
    function setDescription($description) {
        $this->description = $description;
    }
    
    function setStyle($style) {
        $this->style = $style;
    }
    
    function setType($type) {
        $this->type = $type;
    }
    
    function setPath($path) {
        $this->path = $path;
    }
    
    function setOwner(User $owner) {
        $this->owner = $owner;
    }
    
    function setStreamUrl($streamUrl) {
        $this->streamUrl = $streamUrl;
    }
}
