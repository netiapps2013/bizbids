<?php
// src/BBidsBBidsHomeBundle/Entity;

namespace BBids\BBidsHomeBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
*/

class FBUser extends BaseUser{
	/** @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
        */

	protected $id;

	public function __construct()
	{
		parent::__construct();		
	}

}

?>
