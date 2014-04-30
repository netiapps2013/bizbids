<?php
// src/BBids/BBidsHomeBundle/Entity/Categories.php

namespace BBids\BBidsHomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Categories
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $segmentid;

    /**
     * @var integer
     */
    private $parentid;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $group;

    /**
     * @var string
     */
    private $price;

    /**
     * @var integer
     */
    private $status;


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
     * Set segmentid
     *
     * @param integer $segmentid
     * @return Categories
     */
    public function setSegmentid($segmentid)
    {
        $this->segmentid = $segmentid;

        return $this;
    }

    /**
     * Get segmentid
     *
     * @return integer 
     */
    public function getSegmentid()
    {
        return $this->segmentid;
    }

    /**
     * Set parentid
     *
     * @param integer $parentid
     * @return Categories
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
     * Set category
     *
     * @param string $category
     * @return Categories
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set group
     *
     * @param string $group
     * @return Categories
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return string 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return Categories
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Categories
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @var string
     */
    private $description;


    /**
     * Set description
     *
     * @param string $description
     * @return Categories
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @var string
     */
    private $suite;


    /**
     * Set suite
     *
     * @param string $suite
     * @return Categories
     */
    public function setSuite($suite)
    {
        $this->suite = $suite;

        return $this;
    }

    /**
     * Get suite
     *
     * @return string 
     */
    public function getSuite()
    {
        return $this->suite;
    }
}
