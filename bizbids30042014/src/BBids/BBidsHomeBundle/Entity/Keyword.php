<?php
// src/BBids/BBidsHomeBundle/Entity/Keyword.php

namespace BBids\BBidsHomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Keyword
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $parentid;

    /**
     * @var string
     */
    private $keyword;


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
     * Set parentid
     *
     * @param integer $parentid
     * @return Keyword
     */
    public function setParentid($parentid)
    {
        $this->parentid = $parentid;

        return $this;
    }

    /**
     * Get parentid
     *
     * @return integer 
     */
    public function getParentid()
    {
        return $this->parentid;
    }

    /**
     * Set keyword
     *
     * @param string $keyword
     * @return Keyword
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword
     *
     * @return string 
     */
    public function getKeyword()
    {
        return $this->keyword;
    }
}
