<?php

namespace Sealsix\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RankIndexImage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sealsix\MediaBundle\Entity\RankIndexMusiqueRepository")
 */
class RankIndexMusique
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
     * @var Sealsix\MediaBundle\Entity\Musique
     *
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\Musique", inversedBy="indexed", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
    */
    private $musique;

    /**
     * @var Sealsix\MediaBundle\Entity\IndexMetaMusique
     *
     * @ORM\ManyToOne(targetEntity="Sealsix\MediaBundle\Entity\IndexMetaMusique", cascade={"remove"})
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
     * @param \Sealsix\MediaBundle\Entity\Musique $image
     *
     * @return RankIndexMusique
     */
    public function setMusique(\Sealsix\MediaBundle\Entity\Musique $musique)
    {
        $this->musique = $musique;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Sealsix\MediaBundle\Entity\Musique
     */
    public function getMusique()
    {
        return $this->musique;
    }

    /**
     * Set word
     *
     * @param \Sealsix\MediaBundle\Entity\IndexMetaMusique $word
     *
     * @return RankIndexMusique
     */
    public function setWord(\Sealsix\MediaBundle\Entity\IndexMetaMusique $word)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return \Sealsix\MediaBundle\Entity\IndexMetaMusique
     */
    public function getWord()
    {
        return $this->word;
    }
}
