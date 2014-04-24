<?php
// src/BBids/BBidsHomeBundle/Form/CategoryType.php

namespace BBids\BBidsHomeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use BBids\BBidsHomeBundle\Entity\Categories;

class CategoryType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$category = new Categories();

		$segmentArray = array();

		$segmentList = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('segmentid'=>0, 'parentid'=>0));

		foreach($segmentList as $segment){
			$segmentid = $segment->getId();
			$segmentname = $segment->getCategory();
			$segmentArray[$segmentid] = $segmentname;
		}		

		$builder->add('segmentid','choice', array('choices'=>$segmentArray, 'label'=>'Please choose a Segment'));
                $builder->add('category','text',array('label'=>'Enter a category name', 'attr'=>array('size'=>100)));
		$builder->add('group','choice', array('label'=>'Please choose a Group','choices'=>array('Buy It Now'=>'Buy It Now','Popular'=>'Popular', 'General'=>'General')));
		$builder->add('description','textarea',array('label'=>'Enter Description'));
		$builder->add('Add','submit',array('class'=>'btn-success-btn add'));
  	}

	public function getName()
	{
		return 'categories';
	}
}
?>
