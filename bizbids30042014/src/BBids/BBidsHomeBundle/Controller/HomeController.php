<?php
namespace BBids\BBidsHomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BBids\BBidsHomeBundle\Entity\Enquiry;
use BBids\BBidsHomeBundle\Entity\Categories;
use Symfony\Component\HttpFoundation\Response;
use BBids\BBidsHomeBundle\Entity\EnquirySubcategoryRel;
use BBids\BBidsHomeBundle\Entity\User;
use BBids\BBidsHomeBundle\Entity\Vendorenquiryrel;
use Symfony\Component\Validator\Mapping\ClassMetaData;
use Symfony\Component\Validator\Constraints as Assert;
use BBids\BBidsHomeBundle\Entity\Account;
use BBids\BBidsHomeBundle\Entity\Activity;

class HomeController extends Controller
{
	public function node3Action()
	{
		return $this->render('BBidsBBidsHomeBundle:Home:node3.html.twig');
	}
	public function node2Action()
	{
		return $this->render('BBidsBBidsHomeBundle:Home:node2.html.twig');
	}

	public function node1Action()
	{
		return $this->render('BBidsBBidsHomeBundle:Home:node1.html.twig');
	}

	public function acceptenquiryAction($enquiryid, $category)
	{
		$session = $this->container->get('session');
		
		$uid = $session->get('uid');
		
		$categoryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('category'=>$category));
		
		foreach($categoryArray as $c){
				$categoryid = $c->getId();
		}
		
		$enquiryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Vendorenquiryrel')->findBy(array('enquiryid'=>$enquiryid, 'vendorid'=>$uid));
		foreach($enquiryArray as $enquiry){
			$id = $enquiry->getId();
		}
			
		$em = $this->getDoctrine()->getManager();

		$vendor = $em->getRepository('BBidsBBidsHomeBundle:Vendorenquiryrel')->find($id);

		$vendor->setLeadstatus(1);

		$em->flush();

		
		$vendorcatArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Vendorcategoriesrel')->findBy(array('vendorid'=>$uid, 'categoryid'=>$categoryid));
		foreach($vendorcatArray as $vendorcat){
			$id = $vendorcat->getId();
			$leadpack = $vendorcat->getLeadpack();
		}

		
		
		$em = $this->getDoctrine()->getManager();
			
		$query = $em->createQueryBuilder()
			->select('count(e.id)')
			->from('BBidsBBidsHomeBundle:EnquirySubcategoryRel', 'e')
			->add('where', 'e.enquiryid = :enquiryid')
			->setParameter('enquiryid', $enquiryid);

		echo $subcount = $query->getQuery()->getSingleScalarResult();
		

		$nowpack = $leadpack - $subcount;
		
		$em = $this->getDoctrine()->getManager();

		$lead = $em->getRepository('BBidsBBidsHomeBundle:Vendorcategoriesrel')->find($id);

		$lead->setLeadpack($nowpack);
	
		$em->flush();

		if($nowpack == 0){
			$em = $this->getDoctrine()->getManager();

	                $lead = $em->getRepository('BBidsBBidsHomeBundle:Vendorcategoriesrel')->find($id);

          	  	$lead->setStatus(0);

                	$em->flush();
			
			$subcatArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('parentid'=>$categoryid));

			foreach($subArray as $sub){
				$subid = $sub->getId();
		
				$vendorsubArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Vendorsubcategoriesrel')->findBy(array('vendorid'=>$uid, 'categoryid'=>$subid));
				foreach($vendorsubArray as $vendorsubrel){
					$relid = $vendorsubrel->getId();

					$em = $this->getDoctrine()->getManager();

					$vendorrel = $em->getRepository('BBidsBBidsHomeBundle:Vendorsubcategoriesrel')->find($relid);

					$vendorrel->setStatus(0);

					$em->flush();
				}
			}

		}

		return $this->redirect($this->generateUrl('b_bids_b_bids_vendor_enquiries'));
		
	}
	
	public function indexAction(Request $request)
	{
		$session = $this->container->get('session');

		$em = $this->getDoctrine()->getManager();
		$qb = $em->createQueryBuilder();
    		$qb->select('count(enquiry.id)');
    		$qb->from('BBidsBBidsHomeBundle:Enquiry','enquiry');
			
		$categoryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('parentid'=>0));

		$catArray = array();
		
		foreach($categoryArray as $c){
			$catname = $c->getCategory();
			$catArray[$catname] = $catname;
		}

		$cities = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:City')->findAll();
		
		$cityArray = array();
		
		foreach($cities as $c){
			$city = $c->getCity();
			$cityArray[$city] = $city;
		}

		$categories = new Categories();


		$form = $this->createFormBuilder($categories)
			->add('category', 'choice', array('label'=>'What job do you need done', 'expanded'=>FALSE, 'attr' => array('class'=>'form-control'), 'choices'=>$catArray, 'empty_value'=>'Select  a category', 'empty_data'=>null))
			->add('city', 'choice', array('label'=>'where do you need the job done', 'expanded'=>FALSE, 'attr' => array('class'=>'form-control'), 'choices'=>$cityArray, 'empty_value'=>'select a city', 'empty_data'=>null, 'mapped'=>FALSE ))
			->add('submit', 'submit', array('label'=>'Get quotes Now', 'attr' => array('class'=>'btn btn-success btn-block')))
			->getForm();
		
    		$count = $qb->getQuery()->getSingleScalarResult();
		
		$query = $em->createQueryBuilder()
			->select('a.category, a.created, a.type, ac.contactname, a.city')
			->from('BBidsBBidsHomeBundle:Activity', 'a')
			->innerJoin('BBidsBBidsHomeBundle:Account', 'ac', 'WITH', 'a.authorid=ac.userid')
			->add('orderBy', 'a.id DESC');


		$activities = $query->getQuery()->getResult();
			

		$form->handleRequest($request);

		if($request->isMethod('POST')){
			if($form->isValid()){
				$category = $form['category']->getData();
			
				return $this->redirect("http://192.168.1.10/~anup/bizbids/web/app_dev.php/search?category=$category");
			}
			return $this->render('BBidsBBidsHomeBundle:Home:index.html.twig', array('enquiries'=>$count, 'activities'=>$activities, 'form'=>$form->createView()));
		}
	 	return $this->render('BBidsBBidsHomeBundle:Home:index.html.twig', array('enquiries'=>$count, 'activities'=>$activities, 'form'=>$form->createView()));
	}

	public function searchAction()
	{

		$request = Request::createFromGlobals();		

		$category =  $request->query->get('category');
			
		$categoryRow = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('category'=>$category));
                foreach($categoryRow as $category){
                        $categoryid = $category->getId();
                }
                
             if(!isset($categoryid)){
			return $this->redirect($this->generateUrl('b_bids_b_bids_home_homepage'));
		}
		$session = $this->container->get('session');

		if($session->has('uid')){
			return $this->redirect($this->generateUrl('b_bids_b_bids_post_by_search', array('categoryid'=>$categoryid)));
		}
		else{
			return $this->redirect($this->generateUrl('b_bids_b_bids_post_by_search_inline', array('categoryid'=>$categoryid)));	
		}
	}

	public function postbysearchinlineAction(Request $request, $categoryid)
	{

		$enquiry = new Enquiry();

		$categoryRow = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$categoryid));
                foreach($categoryRow as $category){
                        $parentid = $category->getParentid();
                }
		
		if($parentid == 0){
			$parentid = $categoryid;
		}
		
		$categorynamelist = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$parentid));

		foreach($categorynamelist as $category){
			$categoryname = $category->getCategory();
		}
		
		$subcategoryList = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('parentid'=>$parentid));

                $subcategory = array();

                foreach($subcategoryList as $sublist){
                        $subid = $sublist->getId();
                        $subcat = $sublist->getCategory();
                        $subcategory[$subid] = $subcat;
                }
		
		$cities = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:City')->findAll();

                $cityArray = array();

                foreach($cities as $c){
                        $city = $c->getCity();
                        $cityArray[$city] = $city;
                }
	
		$form = $this->createFormBuilder($enquiry)
                     ->add('subcategory','choice', array('choices'=>$subcategory, 'expanded'=>TRUE,  'multiple'=>TRUE, 'mapped'=>FALSE))
                    ->add('category', 'hidden', array('data'=>$parentid))
                     ->add('hire','choice', array('choices'=> array('1'=>'Urgent', '2'=>'-1-3 days', '3'=>'-4-7 days','4'=>'-7-14 days','5'=>'Just Planning'), 'mapped'=>FALSE, 'label'=>'When are you looking to hire *'))
		      ->add('subj','text',array('label'=>'Enter your subject *'))
                     ->add('description','textarea', array('label'=>'Please provide any further details of the job *', 'attr'=>array('max_length'=>'5')))
                     ->add('email', 'email', array('label'=>'Your Email address *', 'mapped'=>FALSE))
		     ->add('contactname','text', array('label'=>'Enter your full name *', 'mapped'=>FALSE))
                     ->add('homecode', 'text', array('label'=>'Your Contact Number ','mapped'=>FALSE, 'required'=>False, 'attr'=>array('size'=>5, 'placeholder'=>'Area Code')))
                     ->add('mobile', 'text', array('label'=>'Your primary contact Number(mobile) *', 'mapped'=>FALSE, 'attr'=>array('size'=>12)))
                     ->add('homephone', 'text', array('label'=>FALSE, 'mapped'=>FALSE, 'required'=>FALSE, 'attr'=>array('size'=>12, 'placeholder'=>'Phone Number')))
                      ->add('city','choice', array('label'=>'Please choose your city *', 'choices'=>$cityArray, 'empty_value'=>'Select city', 'empty_data'=>null))
                     ->add('accept','checkbox', array('label'=>'I accept terms and conditions and Privacy Policy of BBids', 'required'=>TRUE, 'mapped'=>FALSE))
                     ->add('Get Quotes Now', 'submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
                     ->getForm();

		$form->handleRequest($request);
		
		if($request->isMethod('POST')){
			if($form->isValid()){
				$subcategoryids  = $form['subcategory']->getData();
					
				$subcount = count($subcategoryids);				

				if($subcount == 0){
					$session = $this->container->get('session');

					$session->getFlashBag()->add('error', 'Please select atleast one subcategory');

                                        return $this->render('BBidsBBidsHomeBundle:Home:postbysearchinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));

				}
				$subj = $form['subj']->getData();

				if($subj == ""){
					$session = $this->container->get('session');

                                        $session->getFlashBag()->add('error', 'Subject field cannot be blank');

                                        return $this->render('BBidsBBidsHomeBundle:Home:postbysearchinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));

				}
				
				$email = $form['email']->getData();

				$userArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBY(array('email'=>$email));

				foreach($userArray as $u){
					$id = $u->getId();
					$userstatus = $u->getStatus();
				}

				/* if(isset($id)){
					return $this->redirect($this->generateUrl('b_bids_b_bids_user_authenticate', array('email'=>$email)));
				} */


			        $smsphone = $form['mobile']->getData();
				$home = $form['homecode']->getData();
				$phone = $form['homephone']->getData();
				$contactname = $form['contactname']->getData();
                                if(!empty($home)){
                                        $homephone = $form['homecode']->getData()."-".$form['homephone']->getData();
                                }
                                else{
                                        $homephone = "";
                                }
                           	$session = $this->container->get('session');

				if (ctype_alpha(str_replace(' ', '', $contactname)) === false) {     
					$session->getFlashBag()->add('error', 'Contact name should contain only alphabets and spaces');
					
					return $this->render('BBidsBBidsHomeBundle:Home:postbysearchinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));
				
				}
				
				if(strlen($smsphone) != 10){
					 $session->getFlashBag()->add('error', 'Mobile Number should be exactly 10 digits');

                                        return $this->render('BBidsBBidsHomeBundle:Home:postbysearchinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));

				}

				if(!is_numeric($smsphone)){
					 $session->getFlashBag()->add('error', 'Mobile phone field should contain only numbers.');

                                        return $this->render('BBidsBBidsHomeBundle:Home:postbysearchinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));

				}
			
				if(!empty($phone) && !empty($home) &&(!is_numeric($home) || !is_numeric($phone))){
					$session->getFlashBag()->add('error', 'Home Phone should contain only numbers');
                                       return $this->render('BBidsBBidsHomeBundle:Home:postbysearchinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));

				}
				if(isset($id)){
					$userid = $id;
					$mobilecode = rand(100000,999999);
					
					$em = $this->getDoctrine()->getManager();

					$user = $em->getRepository('BBidsBBidsHomeBundle:User')->find($id);
					
					$user->setMobilecode($mobilecode);

					$em->flush();
				}
				else {
					$user =  new User();


  	                	        $created = new \DateTime();
        	                        $userhash = $this->generateHash(16);
                	                $mobilecode = rand(100000,999999);

                                	$user->setEmail($email);
                                	$user->setCreated($created);
               		                $user->setUpdated($created);
                        	        $user->setUserhash($userhash);
                                	$user->setStatus(0);
                	                $user->setMobilecode($mobilecode);

                        	        $em = $this->getDoctrine()->getManager();
                                	$em->persist($user);
                                	$em->flush();
				
					$userid = $user->getId();
                                	$account = new Account();

                                	$account->setProfileid(2);
                                	$account->setUserid($userid);
                                	$account->setSmsphone($smsphone);
                                	$account->setHomephone($homephone);
					$account->setContactname($contactname);
			
                	                $em = $this->getDoctrine()->getManager();
                        	        $em->persist($account);
                                	$em->flush();
				}
				$categoryid = $form['category']->getData();
                                $subcategoryids  = $form['subcategory']->getData();
                                $subj = $form['subj']->getData();
                                $description = $form['description']->getData();
                                $city = $form['city']->getData();
				$created = new \DateTime();

                                $enquiry = new Enquiry();
                                $enquiry->setSubj($subj);
                                $enquiry->setDescription($description);
                                $enquiry->setCity($city);
                                $enquiry->setCategory($categoryid);
                                $enquiry->setAuthorid($userid);
                                $enquiry->setCreated($created);
                                $enquiry->setUpdated($created);
                                $enquiry->setStatus(0);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($enquiry);
                                $em->flush();

                                $enquiryid = $enquiry->getId();
                                
                                $vendorArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Vendorcategoriesrel')->findBy(array('categoryid'=>$categoryid));
					

								foreach($vendorArray as $vendor){
									$vendorid = $vendor->getVendorid();
									$status = $vendor->getStatus();
							
									if($status == 1){
										$vendorenquiryrel = new Vendorenquiryrel();
		
										$vendorenquiryrel->setVendorid($vendorid);
										$vendorenquiryrel->setEnquiryid($enquiryid);
	
										$em = $this->getDoctrine()->getManager();
										$em->persist($vendorenquiryrel);
										$em->flush();
									}
								}
                                $count = count($subcategoryids);
					
                                for($i=0; $i<$count; $i++){
                                        $enquirysubrel = new EnquirySubcategoryRel();
                                        $enquirysubrel->setEnquiryid($enquiryid);
                                        $enquirysubrel->setSubcategoryid($subcategoryids[$i]);
					
					                   $em = $this->getDoctrine()->getManager();
                                        $em->persist($enquirysubrel);
                                        $em->flush();

					
				}
				
				$categoryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$categoryid));
				foreach($categoryArray as $c){
					$cat = $c->getCategory();
				}

				$activity = new Activity();
				
				$activity->setMessage('New Enquiry');
				$activity->setCity($city);
				$activity->setCategory($cat);
				$activity->setAuthorid($userid);
				$activity->setCreated($created);
				$activity->setType(1);
			
				$em = $this->getDoctrine()->getManager();
				$em->persist($activity);
				$em->flush();
				
				//echo $userstatus;
				//exit;i
	
				if(isset($id)){
					if($userstatus == 1){
						return $this->redirect($this->generateUrl('b_bids_b_bids_user_authenticate', array('email'=>$email, 'enquiryid'=>$enquiryid)));
					}
					if($userstatus == 0 || $userstatus == 2){
						return $this->redirect($this->generateUrl('b_bids_b_bids_user_sms_verify', array('smscode'=>$mobilecode, 'userid'=>$userid, 'path'=>'inline','enquiryid'=>$enquiryid)));
					}
					if($userstatus == 3){
						$session = $this->container->get('session');
	
						$session->getFlashBag()->add('error', 'Your account has been blocked. Please contact the system administrator for further details.');
						return $this->render('::error.html.twig');
					}
				}
				else {
					return $this->redirect($this->generateUrl('b_bids_b_bids_user_sms_verify', array('smscode'=>$mobilecode, 'userid'=>$userid, 'path'=>'inline','enquiryid'=>$enquiryid)));
				}
			}
			else{
				return $this->render('BBidsBBidsHomeBundle:Home:postbysearchinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));
			}
		}
		return $this->render('BBidsBBidsHomeBundle:Home:postbysearchinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));
	}

	public function postbysearchAction(Request $request, $categoryid)
	{
		$enquiry = new Enquiry();
                $categories = new Categories();
			
		$categoryRow = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$categoryid));
                foreach($categoryRow as $category){
                        $parentid = $category->getParentid();
                }

                if($parentid == 0){
                        $parentid = $categoryid;
                }
		
		$categorynamelist = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$parentid));

                foreach($categorynamelist as $category){
                        $categoryname = $category->getCategory();
                }


                $subcategoryList = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('parentid'=>$parentid));

                $subcategory = array();

                foreach($subcategoryList as $sublist){
                        $subid = $sublist->getId();
                        $subcat = $sublist->getCategory();
                        $subcategory[$subid] = $subcat;
                }

		$cities = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:City')->findAll();

                $cityArray = array();

                foreach($cities as $c){
                        $city = $c->getCity();
                        $cityArray[$city] = $city;
                }

		$form = $this->createFormBuilder($enquiry)
                        ->add('category','hidden', array('data'=>$parentid))
                        ->add('subcategory','choice', array('choices'=>$subcategory, 'multiple'=>TRUE, 'expanded'=>TRUE,  'label'=>'Select a subcategory', 'empty_value'=>'Choose subcategories', 'empty_data'=>null,  'mapped'=>FALSE, 'required'=>TRUE))
                        ->add('subj','text', array('label'=>'Enter Subject *: '))
                        ->add('description','textarea', array('label'=>'Describe your enquiry *'))
                        ->add('city','choice', array('label'=>'Please choose your city * ', 'choices'=>$cityArray, 'empty_value'=>'Select city', 'empty_data'=>null))
                        ->add('Post Job','submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
                        ->getForm();
                $form->handleRequest($request);
		
		if($request->isMethod('POST')){
			if($form->isValid()){

				$subj = $form['subj']->getData();

                                if($subj == ""){
                                        $session = $this->container->get('session');

                                        $session->getFlashBag()->add('error', 'Subject field cannot be blank');

                                        return $this->render('BBidsBBidsHomeBundle:Home:postbysearch.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));

                                }

				$session = $this->container->get('session');

				$categoryid = $form['category']->getData();
                                $subcategoryids  = $form['subcategory']->getData();
                                $subj = $form['subj']->getData();
                                $description = $form['description']->getData();
                                $city = $form['city']->getData();

                                $uid = $session->get('uid');
                                $created = new \DateTime();

                                $enquiry = new Enquiry();
                                $enquiry->setSubj($subj);
                                $enquiry->setDescription($description);
                                $enquiry->setCity($city);
                                $enquiry->setCategory($categoryid);
                                $enquiry->setAuthorid($uid);
                                $enquiry->setCreated($created);
                                $enquiry->setUpdated($created);
                                $enquiry->setStatus(1);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($enquiry);
                                $em->flush();
				
				$enquiryid = $enquiry->getId();
                                $count = count($subcategoryids);
								$vendorArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Vendorcategoriesrel')->findBy(array('categoryid'=>$categoryid));
					

								foreach($vendorArray as $vendor){
									$vendorid = $vendor->getVendorid();
									$status = $vendor->getStatus();
							
									if($status == 1){
										$vendorenquiryrel = new Vendorenquiryrel();
		
										$vendorenquiryrel->setVendorid($vendorid);
										$vendorenquiryrel->setEnquiryid($enquiryid);
	
										$em = $this->getDoctrine()->getManager();
										$em->persist($vendorenquiryrel);
										$em->flush();
									}
								}
								
                                for($i=0; $i<$count; $i++){
                                        $enquirysubrel = new EnquirySubcategoryRel();
                                        $enquirysubrel->setEnquiryid($enquiryid);
                                        $enquirysubrel->setSubcategoryid($subcategoryids[$i]);
					
										$em = $this->getDoctrine()->getManager();
                                        $em->persist($enquirysubrel);
                                        $em->flush();

					
                                }
				$categoryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$categoryid));
                                foreach($categoryArray as $c){
                                        $cat = $c->getCategory();
                                }

                                $activity = new Activity();

                                $activity->setMessage('New Enquiry');
				$activity->setCity($city);
                                $activity->setCategory($cat);
                                $activity->setAuthorid($uid);
                                $activity->setCreated($created);
                                $activity->setType(1);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($activity);
                                $em->flush();

				 return $this->redirect($this->generateUrl('b_bids_b_bids_enquiry_processing'));
			
			}
			else{
				return $this->render('BBidsBBidsHomeBundle:Home:postbysearch.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));
			}
		}
		
		return $this->render('BBidsBBidsHomeBundle:Home:postbysearch.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));
						
	}

	public function postjobAction(Request $request)
	{
		$session = $this->container->get('session');
	

		if($session->has('uid')){
			return $this->redirect($this->generateUrl('b_bids_b_bids_post_quote'));
		}
		else {
			return $this->redirect($this->generateUrl('b_bids_b_bids_post_quote_inline'));
		}
	}

	 public function generateHash($length)
        {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randomString = '';

                for($i=0; $i< $length; $i++) {
                        $randomString .= $characters[rand(0, strlen($characters) - 1)];
                }

                return $randomString;
        }

	

	public function postquoteAction(Request $request)
	{
		$enquiry = new Enquiry();
		$categories = new Categories();
		
		$categoriesArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('parentid'=>0));

		foreach($categoriesArray as $category){
			$categoryname = $category->getCategory();
			$categoryid = $category->getId();
			$categorylist[$categoryid] = $categoryname;
		}

		$subcategoriesArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('segmentid'=>0));

		foreach($subcategoriesArray as $subcategory){
			$subcategoryid = $subcategory->getId();
			$subcategoryname = $subcategory->getCategory();
			$subcategories[$subcategoryid] = $subcategoryname;
		}
	

		$cityArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:City')->findAll();

                $cityList = array();

                foreach($cityArray as $city){
                        $cityname = $city->getCity();
                        $cityList[$cityname] = $cityname;
                }
	
		$form = $this->createFormBuilder($enquiry)
			->add('category','choice', array('choices'=>$categorylist, 'empty_value'=>'Select a Category', 'empty_data'=>null, 'label'=>'Select a category '))
			->add('subcategory','choice', array('choices'=>$subcategories, 'multiple'=>TRUE,  'label'=>'Select a subcategory', 'empty_value'=>'Choose subcategories', 'empty_data'=>null,  'mapped'=>FALSE, 'required'=>TRUE))
			->add('subj','text', array('label'=>'Enter Subject : '))
			->add('description','textarea', array('label'=>'Describe your enquiry'))
			->add('city','choice', array('label'=>'Please choose your city', 'choices'=>$cityList, 'empty_value'=>'Select city', 'empty_data'=>null))
			->add('Post Job','submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
			->getForm();
		$form->handleRequest($request);

		if($request->isMethod('POST')){
			if($form->isValid()){
				$subj = $form['subj']->getData();

                                if($subj == ""){
                                        $session = $this->container->get('session');

                                        $session->getFlashBag()->add('error', 'Subject field cannot be blank');

                                        return $this->render('BBidsBBidsHomeBundle:Home:enquiry.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));

                                }

				$session = $this->container->get('session');
								
				$categoryid = $form['category']->getData();
				$subcategoryids  = $form['subcategory']->getData();
				$subj = $form['subj']->getData();
				$description = $form['description']->getData();
				$city = $form['city']->getData();
			
				$uid = $session->get('uid');		
				$created = new \DateTime();				

				$enquiry = new Enquiry();
				$enquiry->setSubj($subj);
				$enquiry->setDescription($description);
				$enquiry->setCity($city);
				$enquiry->setCategory($categoryid);
				$enquiry->setAuthorid($uid);
				$enquiry->setCreated($created);
				$enquiry->setUpdated($created);
				$enquiry->setStatus(1);
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($enquiry);
				$em->flush();


				$enquiryid = $enquiry->getId();

				$vendorArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Vendorcategoriesrel')->findBy(array('categoryid'=>$categoryid));
					

								foreach($vendorArray as $vendor){
									$vendorid = $vendor->getVendorid();
									$status = $vendor->getStatus();
							
									if($status == 1){
										$vendorenquiryrel = new Vendorenquiryrel();
		
										$vendorenquiryrel->setVendorid($vendorid);
										$vendorenquiryrel->setEnquiryid($enquiryid);
	
										$em = $this->getDoctrine()->getManager();
										$em->persist($vendorenquiryrel);
										$em->flush();
									}
								}

				$enquiryid = $enquiry->getId();
				$count = count($subcategoryids);
				
				for($i=0; $i<$count; $i++){
					$enquirysubrel = new EnquirySubcategoryRel();
					$enquirysubrel->setEnquiryid($enquiryid);
					$enquirysubrel->setSubcategoryid($subcategoryids[$i]);
		
								
					$em = $this->getDoctrine()->getManager();
					$em->persist($enquirysubrel);
					$em->flush();


				}
				$categoryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$categoryid));
                                foreach($categoryArray as $c){
                                        $cat = $c->getCategory();
                                }

                                $activity = new Activity();

                                $activity->setMessage('New Enquiry');
				$activity->setCity($city);
                                $activity->setCategory($cat);
                                $activity->setAuthorid($uid);
                                $activity->setCreated($created);
                                $activity->setType(1);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($activity);
                                $em->flush();

				return $this->redirect($this->generateUrl('b_bids_b_bids_enquiry_processing'));
			}
			else{
				return $this->render('BBidsBBidsHomeBundle:Home:enquiry.html.twig', array('form'=>$form->createView()));
			}
		}
		
		return $this->render('BBidsBBidsHomeBundle:Home:enquiry.html.twig', array('form'=>$form->createView()));
	}

	public function postquoteinlineAction(Request $request)
	{
		$enquiry = new Enquiry();
                $categories = new Categories();

                $categoriesArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('parentid'=>0));

                foreach($categoriesArray as $category){
                        $categoryname = $category->getCategory();
                       $categoryid = $category->getId();
                        $categorylist[$categoryid] = $categoryname;
                }

                $subcategoriesArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('segmentid'=>0));

                foreach($subcategoriesArray as $subcategory){
                        $subcategoryid = $subcategory->getId();
                        $subcategoryname = $subcategory->getCategory();
                        $subcategories[$subcategoryid] = $subcategoryname;
                }

		$cityArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:City')->findAll();
			
		$cityList = array();			

		foreach($cityArray as $city){
			$cityname = $city->getCity();
			$cityList[$cityname] = $cityname;
		}

		
		$form = $this->createFormBuilder($enquiry)
			->add('email','email',array('label'=>'Enter Your email *', 'attr'=>array('size'=>40), 'mapped'=>FALSE))
                        ->add('smsphone','text',array('label'=>'Enter your mobile number *','mapped'=>FALSE, 'invalid_message'=>'Mobile Number field cannot contain strings','attr'=>array('size'=>14)))
                        ->add('homecode','text',array('label'=>'Enter your home phone ', 'mapped'=>FALSE, 'required'=>FALSE, 'attr'=>array('size'=>5, 'placeholder'=>'Area code')))
                        ->add('homephone','text', array('mapped'=>FALSE, 'required'=>FALSE, 'attr'=>array('size'=>12, 'placeholder'=>'Phone number')))
			->add('contactname','text', array('label'=>'Enter your full name *', 'mapped'=>FALSE))
			->add('category','choice', array('choices'=>$categorylist, 'empty_value'=>'Select a Category', 'empty_data'=>null, 'label'=>'Select a category * '))
                        ->add('subcategory','choice', array('choices'=>$subcategories, 'multiple'=>TRUE,  'label'=>'Select a subcategory *', 'empty_value'=>'Choose subcategories', 'empty_data'=>null,  'mapped'=>FALSE, 'required'=>TRUE))
                        ->add('subj','text', array('label'=>'Enter Subject *'))
                        ->add('description','textarea', array('label'=>'Describe your enquiry *'))
                        ->add('city','choice', array('label'=>'Please choose your city *', 'choices'=>$cityList, 'empty_value'=>'Select city', 'empty_data'=>null))
                        ->add('Post Job','submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
                        ->getForm();

		$form->handleRequest($request);

		if($request->isMethod('POST')){
			if($form->isvalid()){
				$email = $form['email']->getData();
				 //$email = $form['email']->getData();

			$subj = $form['subj']->getData();

                                if($subj == ""){
                                        $session = $this->container->get('session');

                                        $session->getFlashBag()->add('error', 'Subject field cannot be blank');

                                        return $this->render('BBidsBBidsHomeBundle:Home:enquiryinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));

                                }


                                $userArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBY(array('email'=>$email));

                                foreach($userArray as $u){
                                        $id = $u->getId();
                                }

                                if(isset($id)){
                                        return $this->redirect($this->generateUrl('b_bids_b_bids_user_authenticate', array('email'=>$email)));
                                }

				$session  = $this->container->get('session');
                                $smsphone = $form['smsphone']->getData();
				$contactname = $form['contactname']->getData();
				$home = $form['homecode']->getData();
				$phone = $form['homephone']->getData();

				if(!preg_match('/^[a-zA-Z\s]+$/', $contactname)){
					$session = $this->container->get('session');
                                        
                                        $session->getFlashbag()->add('error', 'Please enter a valid contact name');
                                        
                                        return $this->render('BBidsBBidsHomeBundle:Home:enquiryinline.html.twig', array('form'=>$form->createView()));
				}

				if(!is_numeric($smsphone)){
					$session = $this->container->get('session');
					
					$session->getFlashbag()->add('error', 'Mobile number cannot contain strings');
					
					return $this->render('BBidsBBidsHomeBundle:Home:enquiryinline.html.twig', array('form'=>$form->createView()));
				}
                                if(!empty($form['homecode']->getData)){
                                        $homephone = $form['homecode']->getData()."-".$form['homephone']->getData();
                                }
                                else{
                                        $homephone = "";
                                }

				if (ctype_alpha(str_replace(' ', '', $contactname)) === false) {
					
                                        $session->getFlashBag()->add('error', 'Contact name should contain only alphabets and spaces');

                                        return $this->render('BBidsBBidsHomeBundle:Home:enquiryinline.html.twig', array('form'=>$form->createView()));

                                }

                                if(strlen($smsphone) != 10){
                                         $session->getFlashBag()->add('error', 'Mobile Number should be exactly 10 digits');

                                        return $this->render('BBidsBBidsHomeBundle:Home:enquiryinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));

                                }

                                if(!is_numeric($smsphone)){
                                         $session->getFlashBag()->add('error', 'Mobile phone field should contain only numbers.');

                                        return $this->render('BBidsBBidsHomeBundle:Home:enquiryinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));

                                }

                                if(!is_numeric($home) || !is_numeric($phone)){
                                        $session->getFlashBag()->add('error', 'Home Phone should contain only numbers');

                                        return $this->render('BBidsBBidsHomeBundle:Home:enquiryinline.html.twig', array('form'=>$form->createView(), 'category'=>$categoryname));

                                }

                                $user = new User();

                                $created = new \DateTime();
                                $userhash = $this->generateHash(16);
                                $mobilecode = rand(100000,999999);

                                $user->setEmail($email);
                                $user->setCreated($created);
                                $user->setUpdated($created);
                                $user->setUserhash($userhash);
                                $user->setStatus(0);
                                $user->setMobilecode($mobilecode);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($user);
                                $em->flush();

				$userid = $user->getId();

                                $account = new Account();

                                $account->setProfileid(2);
                                $account->setUserid($userid);
                                $account->setSmsphone($smsphone);
                                $account->setHomephone($homephone);
				$account->setContactname($contactname);				

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($account);
                                $em->flush();

				$categoryid = $form['category']->getData();
                                $subcategoryids  = $form['subcategory']->getData();
                                $subj = $form['subj']->getData();
                                $description = $form['description']->getData();
                                $city = $form['city']->getData();

				$created = new \DateTime();

                                $enquiry = new Enquiry();
                                $enquiry->setSubj($subj);
                                $enquiry->setDescription($description);
                                $enquiry->setCity($city);
                                $enquiry->setCategory($categoryid);
                                $enquiry->setAuthorid($userid);
                                $enquiry->setCreated($created);
                                $enquiry->setUpdated($created);
                                $enquiry->setStatus(0);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($enquiry);
                                $em->flush();
				
				$enquiryid = $enquiry->getId();
				
				$vendorArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Vendorcategoriesrel')->findBy(array('categoryid'=>$categoryid));
					

								foreach($vendorArray as $vendor){
									$vendorid = $vendor->getVendorid();
									$status = $vendor->getStatus();
							
									if($status == 1){
										$vendorenquiryrel = new Vendorenquiryrel();
		
										$vendorenquiryrel->setVendorid($vendorid);
										$vendorenquiryrel->setEnquiryid($enquiryid);
	
										$em = $this->getDoctrine()->getManager();
										$em->persist($vendorenquiryrel);
										$em->flush();
									}
								}
                                $count = count($subcategoryids);

                                for($i=0; $i<$count; $i++){
                                        $enquirysubrel = new EnquirySubcategoryRel();
                                        $enquirysubrel->setEnquiryid($enquiryid);
                                        $enquirysubrel->setSubcategoryid($subcategoryids[$i]);
		
					
                                        $em = $this->getDoctrine()->getManager();
                                        $em->persist($enquirysubrel);
                                        $em->flush();

					

		                 }
				$categoryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$categoryid));
                                foreach($categoryArray as $c){
                                        $cat = $c->getCategory();
                                }

                                $activity = new Activity();

                                $activity->setMessage('New Enquiry');
				$activity->setCity($city);
                                $activity->setCategory($cat);
                                $activity->setAuthorid($userid);
                                $activity->setCreated($created);
                                $activity->setType(1);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($activity);
                                $em->flush();

				return $this->redirect($this->generateUrl('b_bids_b_bids_user_sms_verify', array('smscode'=>$mobilecode, 'userid'=>$userid, 'path'=>'inline','enquiryid'=>$enquiryid)));
			}
			else{
				return $this->render('BBidsBBidsHomeBundle:Home:enquiryinline.html.twig',array('form'=>$form->createView()));
			}
		}
		return $this->render('BBidsBBidsHomeBundle:Home:enquiryinline.html.twig',array('form'=>$form->createView()));
	}

	public function chooseSubcategoriesByCategoryAction()
	{
		echo $html = $html . sprintf("<option value=\"%d\">%s</option>",1,'hi');
   		return new Response($html);
	}
		
	public function enquiryprocessAction()
	{
		return $this->render('BBidsBBidsHomeBundle:Home:enquiryprocessing.html.twig');
	}
}
