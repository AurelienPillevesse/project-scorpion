<?php

namespace Sealsix\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Comment
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
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetimetz")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Sealsix\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Comment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
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
