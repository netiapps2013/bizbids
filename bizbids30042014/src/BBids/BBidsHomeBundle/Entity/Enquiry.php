<?php
// src/BBids/BBidsHomeBundle/Entity/Enquiry.php

namespace BBids\BBidsHomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Enquiry
{
	
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $subj;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $city;

    /**
     * @var integer
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
     * @var \DateTime
     */
    private $updated;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $updatedby;


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
     * Set subj
     *
     * @param string $subj
     * @return Enquiry
     */
    public function setSubj($subj)
    {
        $this->subj = $subj;

        return $this;
    }

    /**
     * Get subj
     *
     * @return string 
     */
    public function getSubj()
    {
        return $this->subj;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Enquiry
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
     * Set city
     *
     * @param string $city
     * @return Enquiry
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

    /**
     * Set category
     *
     * @param integer $category
     * @return Enquiry
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return integer 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set authorid
     *
     * @param integer $authorid
     * @return Enquiry
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
     * @return Enquiry
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Enquiry
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Enquiry
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
     * Set updatedby
     *
     * @param integer $updatedby
     * @return Enquiry
     */
    public function setUpdatedby($updatedby)
    {
        $this->updatedby = $updatedby;

        return $this;
    }

    /**
     * Get updatedby
     *
     * @return integer 
     */
    public function getUpdatedby()
    {
        return $this->updatedby;
    }
}
