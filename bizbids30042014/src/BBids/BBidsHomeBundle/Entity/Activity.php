<?php
// src/BBids/BBidsHomeBundle/Entity/Activity.php

namespace BBids\BBidsHomeBundle\Entity;

use Doctrine\ORM\Mapping as Doctrine;

class Activity 
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $category;

    /**
     * @var integer
     */
    private $authorid;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var integer
     */
    private $type;


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
     * Set message
     *
     * @param string $message
     * @return Activity
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Activity
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
     * Set authorid
     *
     * @param integer $authorid
     * @return Activity
     */
    public function setAuthorid($authorid)
    {
        $this->authorid = $authorid;

        return $this;
    }

    /**
     * Get authorid
     *
     * @return integer 
     */
    public function getAuthorid()
    {
        return $this->authorid;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Activity
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Activity
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @var string
     */
    private $city;


    /**
     * Set city
     *
     * @param string $city
     * @return Activity
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }
}
