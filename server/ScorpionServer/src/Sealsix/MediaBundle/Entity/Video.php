<?php

namespace Sealsix\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sealsix\MediaBundle\Entity\Media;
/**
 * Video
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Entity(repositoryClass="Sealsix\MediaBundle\Entity\VideoRepository")
 */
class Video extends Media
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
     * @var integer
     *
     * @ORM\Column(name="duree", type="float")
     */
    private $duree;

    /**
     * @var indexed
     *
     * @ORM\OneToMany(targetEntity="RankIndexVideo", mappedBy="video")
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
     * Set duree
     *
     * @param integer $duree
     *
     * @return Video
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * Get duree
     *
     * @return integer
     */
    public function getDuree()
    {
        return $this->duree;
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
     * @param \Sealsix\MediaBundle\Entity\RankIndexVideo $indexed
     *
     * @return Video
     */
    public function addIndexed(\Sealsix\MediaBundle\Entity\RankIndexVideo $indexed)
    {
        $this->indexed[] = $indexed;

        return $this;
    }

    /**
     * Remove indexed
     *
     * @param \Sealsix\MediaBundle\Entity\RankIndexVideo $indexed
     */
    public function removeIndexed(\Sealsix\MediaBundle\Entity\RankIndexVideo $indexed)
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
