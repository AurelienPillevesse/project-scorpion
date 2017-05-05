<?php

namespace Sealsix\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MediaLikes
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MediaLikes
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
     * @ORM\ManyToOne(targetEntity="Sealsix\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\Image")
     * @ORM\JoinColumn(nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\Musique")
     * @ORM\JoinColumn(nullable=true)
     */
    private $musique;

    /**
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\Video")
     * @ORM\JoinColumn(nullable=true)
     */
    private $video;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    public function getVideo()
    {
        return $this->video;
    }

    public function setVideo($video)
    {
        $this->video = $video;
        return $this;
    }

    public function getMusique()
    {
        return $this->musique;
    }

    public function setMusique($musique)
    {
        $this->musique = $musique;
        return $this;
    }

    public function getMedia() {
        if($this->image === null && $this->musique === null) {
            return $this->video;
        }
        if($this->image === null && $this->video === null) {
            return $this->musique;
        }
        if($this->musique === null && $this->video === null) {
            return $this->image;
        }
    }
}
