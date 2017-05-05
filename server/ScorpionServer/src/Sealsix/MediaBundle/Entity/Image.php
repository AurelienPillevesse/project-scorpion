<?php

namespace Sealsix\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sealsix\MediaBundle\Entity\Media;

/**
 * Image
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Entity(repositoryClass="Sealsix\MediaBundle\Entity\ImageRepository")
 */
class Image extends Media
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
     * @var indexed
     *
     * @ORM\OneToMany(targetEntity="RankIndexImage", mappedBy="image")
     */
    private $indexed;


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
     * Constructor
     */
    public function __construct()
    {
        $this->indexed = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add indexed
     *
     * @param \Sealsix\MediaBundle\Entity\RankIndexImage $indexed
     *
     * @return Image
     */
    public function addIndexed(\Sealsix\MediaBundle\Entity\RankIndexImage $indexed)
    {
        $this->indexed[] = $indexed;

        return $this;
    }

    /**
     * Remove indexed
     *
     * @param \Sealsix\MediaBundle\Entity\RankIndexImage $indexed
     */
    public function removeIndexed(\Sealsix\MediaBundle\Entity\RankIndexImage $indexed)
    {
        $this->indexed->removeElement($indexed);
    }

    /**
     * Get indexed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIndexed()
    {
        return $this->indexed;
    }
}
