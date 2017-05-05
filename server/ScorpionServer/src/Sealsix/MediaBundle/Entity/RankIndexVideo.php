<?php

namespace Sealsix\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RankIndexImage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sealsix\MediaBundle\Entity\RankIndexVideoRepository")
 */
class RankIndexVideo
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
     * @var float
     *
     * @ORM\Column(name="weight", type="float")
     */
    private $weight;

    /**
     * @var Sealsix\MediaBundle\Entity\Video
     *
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\Video", inversedBy="indexed", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
    */
    private $video;

    /**
     * @var Sealsix\MediaBundle\Entity\IndexMetaVideo
     *
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\IndexMetaVideo", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
    */
    private $word;


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
     * Set weight
     *
     * @param float $weight
     *
     * @return RankIndexImage
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set image
     *
     * @param \Sealsix\MediaBundle\Entity\Video $video
     *
     * @return RankIndexImage
     */
    public function setVideo(\Sealsix\MediaBundle\Entity\Video $video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Sealsix\MediaBundle\Entity\Video
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set word
     *
     * @param \Sealsix\MediaBundle\Entity\IndexMetaVideo $word
     *
     * @return RankIndexVideo
     */
    public function setWord(\Sealsix\MediaBundle\Entity\IndexMetaVideo $word)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return \Sealsix\MediaBundle\Entity\IndexMetaVideo
     */
    public function getWord()
    {
        return $this->word;
    }
}
