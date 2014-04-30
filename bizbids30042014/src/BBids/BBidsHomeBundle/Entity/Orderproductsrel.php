<?php
// src/BBids/BBidsHomeBundle/Entity/Orderproductsrel.php

namespace BBids\BBidsHomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Orderproductsrel
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $ordernumber;

    /**
     * @var integer
     */
    private $categoryid;

    /**
     * @var integer
     */
    private $leadpack;


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
     * Set ordernumber
     *
     * @param integer $ordernumber
     * @return Orderproductsrel
     */
    public function setOrdernumber($ordernumber)
    {
        $this->ordernumber = $ordernumber;

        return $this;
    }

    /**
     * Get ordernumber
     *
     * @return integer 
     */
    public function getOrdernumber()
    {
        return $this->ordernumber;
    }

    /**
     * Set categoryid
     *
     * @param integer $categoryid
     * @return Orderproductsrel
     */
    public function setCategoryid($categoryid)
    {
        $this->categoryid = $categoryid;

        return $this;
    }

    /**
     * Get categoryid
     *
     * @return integer 
     */
    public function getCategoryid()
    {
        return $this->categoryid;
    }

    /**
     * Set leadpack
     *
     * @param integer $leadpack
     * @return Orderproductsrel
     */
    public function setLeadpack($leadpack)
    {
        $this->leadpack = $leadpack;

        return $this;
    }

    /**
     * Get leadpack
     *
     * @return integer 
     */
    public function getLeadpack()
    {
        return $this->leadpack;
    }
}
