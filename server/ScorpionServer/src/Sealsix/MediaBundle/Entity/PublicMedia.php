<?php

namespace Sealsix\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PublicMedia
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PublicMedia
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
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\Video", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $video;

    /**
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\Image", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\Musique", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $musique;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getVideo() {
        return $this->video;
    }

    public function getImage() {
        return $this->image;
    }

    public function getMusique() {
        return $this->musique;
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


    public function setVideo($video) {
        $this->video = $video;
        return $this;
    }

    public function setImage($image) {
        $this->image = $image;
        return $this;
    }

    public function setMusique($musique) {
        $this->musique = $musique;
        return $this;
    }
}
