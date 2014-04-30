<?php

namespace BBids\BBidsHomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BBidsBBidsHomeBundle:Default:index.html.twig', array('name' => $name));
    }
}
