<?php
// src/BBids/BBidsHomeBundle/Entity/Order.php

namespace BBids\BBidsHomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Order 
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $ordernumber;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var string
     */
    private $payoption;

    /**
     * @var string
     */
    private $transactionid;

    /**
     * @var string
     */
    private $bankname;

    /**
     * @var string
     */
    private $transactionstatus;

    /**
     * @var integer
     */
    private $author;

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
     * Set ordernumber
     *
     * @param string $ordernumber
     * @return Order
     */
    public function setOrdernumber($ordernumber)
    {
        $this->ordernumber = $ordernumber;

        return $this;
    }

    /**
     * Get ordernumber
     *
     * @return string 
     */
    public function getOrdernumber()
    {
        return $this->ordernumber;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Order
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
     * @return Order
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
     * Set amount
     *
     * @param string $amount
     * @return Order
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set payoption
     *
     * @param string $payoption
     * @return Order
     */
    public function setPayoption($payoption)
    {
        $this->payoption = $payoption;

        return $this;
    }

    /**
     * Get payoption
     *
     * @return string 
     */
    public function getPayoption()
    {
        return $this->payoption;
    }

    /**
     * Set transactionid
     *
     * @param string $transactionid
     * @return Order
     */
    public function setTransactionid($transactionid)
    {
        $this->transactionid = $transactionid;

        return $this;
    }

    /**
     * Get transactionid
     *
     * @return string 
     */
    public function getTransactionid()
    {
        return $this->transactionid;
    }

    /**
     * Set bankname
     *
     * @param string $bankname
     * @return Order
     */
    public function setBankname($bankname)
    {
        $this->bankname = $bankname;

        return $this;
    }

    /**
     * Get bankname
     *
     * @return string 
     */
    public function getBankname()
    {
        return $this->bankname;
    }

    /**
     * Set transactionstatus
     *
     * @param string $transactionstatus
     * @return Order
     */
    public function setTransactionstatus($transactionstatus)
    {
        $this->transactionstatus = $transactionstatus;

        return $this;
    }

    /**
     * Get transactionstatus
     *
     * @return string 
     */
    public function getTransactionstatus()
    {
        return $this->transactionstatus;
    }

    /**
     * Set author
     *
     * @param integer $author
     * @return Order
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return integer 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Order
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
}
