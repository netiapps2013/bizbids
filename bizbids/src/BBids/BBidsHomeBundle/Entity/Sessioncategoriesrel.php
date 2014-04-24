<?php
// src/BBids/BBidsHomeBundle/Entity/Sessioncategoriesrel.php
 
namespace BBids\BBidsHomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Sessioncategoriesrel
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $userid;

    /**
     * @var string
     */
    private $categoryid;


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
     * Set userid
     *
     * @param integer $userid
     * @return Sessioncategoriesrel
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return integer 
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set categoryid
     *
     * @param string $categoryid
     * @return Sessioncategoriesrel
     */
    public function setCategoryid($categoryid)
    {
        $this->categoryid = $categoryid;

        return $this;
    }

    /**
     * Get categoryid
     *
     * @return string 
     */
    public function getCategoryid()
    {
        return $this->categoryid;
    }
}
