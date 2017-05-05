<?php

namespace Sealsix\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RankIndexImage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sealsix\MediaBundle\Entity\RankIndexImageRepository")
 */
class RankIndexImage
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
     * @var Sealsix\MediaBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\Image", inversedBy="indexed", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
    */
    private $image;

    /**
     * @var Sealsix\MediaBundle\Entity\IndexMetaImage
     *
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\IndexMetaImage", cascade={"remove"})
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
     * @param \Sealsix\MediaBundle\Entity\Image $image
     *
     * @return RankIndexImage
     */
    public function setImage(\Sealsix\MediaBundle\Entity\Image $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Sealsix\MediaBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set word
     *
     * @param \Sealsix\MediaBundle\Entity\IndexMetaImage $word
     *
     * @return RankIndexImage
     */
    public function setWord(\Sealsix\MediaBundle\Entity\IndexMetaImage $word)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return \Sealsix\MediaBundle\Entity\IndexMetaImage
     */
    public function getWord()
    {
        return $this->word;
    }
}
