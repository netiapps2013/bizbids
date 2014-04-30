<?php
// src/BBids/BBidsHomeBundle/Entity/EnquirySubcategoryRel.php

namespace BBids\BBidsHomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class EnquirySubcategoryRel 
{
	
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $enquiryid;

    /**
     * @var integer
     */
    private $subcategoryid;


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
     * Set enquiryid
     *
     * @param integer $enquiryid
     * @return EnquirySubcategoryRel
     */
    public function setEnquiryid($enquiryid)
    {
        $this->enquiryid = $enquiryid;

        return $this;
    }

    /**
     * Get enquiryid
     *
     * @return integer 
     */
    public function getEnquiryid()
    {
        return $this->enquiryid;
    }

    /**
     * Set subcategoryid
     *
     * @param integer $subcategoryid
     * @return EnquirySubcategoryRel
     */
    public function setSubcategoryid($subcategoryid)
    {
        $this->subcategoryid = $subcategoryid;

        return $this;
    }

    /**
     * Get subcategoryid
     *
     * @return integer 
     */
    public function getSubcategoryid()
    {
        return $this->subcategoryid;
    }
}
