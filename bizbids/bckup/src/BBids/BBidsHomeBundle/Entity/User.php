<?php
// src/BBids/BbidsHomeBundle/Entity/User.php

namespace BBids\BBidsHomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class User
{

    /**
     * @var integer
     */
    private $id;

    
   

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

   

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var \DateTime
     */
    private $lastaccess;

    /**
     * @var integer
     */
    private $logincount;

    /**
     * @var string
     */
    private $userhash;

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
     * @var integer
     */
    private $mobilecode;

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
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
     * @return User
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
     * Set lastaccess
     *
     * @param \DateTime $lastaccess
     * @return User
     */
    public function setLastaccess($lastaccess)
    {
        $this->lastaccess = $lastaccess;

        return $this;
    }

    /**
     * Get lastaccess
     *
     * @return \DateTime 
     */
    public function getLastaccess()
    {
        return $this->lastaccess;
    }

    /**
     * Set logincount
     *
     * @param integer $logincount
     * @return User
     */
    public function setLogincount($logincount)
    {
        $this->logincount = $logincount;

        return $this;
    }

    /**
     * Get logincount
     *
     * @return integer 
     */
    public function getLogincount()
    {
        return $this->logincount;
    }

    /**
     * Set userhash
     *
     * @param string $userhash
     * @return User
     */
    public function setUserhash($userhash)
    {
        $this->userhash = $userhash;

        return $this;
    }

    /**
     * Get userhash
     *
     * @return string 
     */
    public function getUserhash()
    {
        return $this->userhash;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return User
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
     * Set mobilecode
     *
     * @param integer $mobilecode
     * @return User
     */
    public function setMobilecode($mobilecode)
    {
        $this->mobilecode = $mobilecode;

        return $this;
    }

    /**
     * Get mobilecode
     *
     * @return integer 
     */
    public function getMobilecode()
    {
        return $this->mobilecode;
    }
}
