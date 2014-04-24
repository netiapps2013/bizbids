<?php
namespace BBids\BBidsHomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Type;
use BBids\BBidsHomeBundle\Entity\User;
use BBids\BBidsHomeBundle\Entity\Account;
use BBids\BBidsHomeBundle\Entity\Categories;
use BBids\BBidsHomeBundle\Entity\City;
use BBids\BBidsHomeBundle\Entity\Vendorcategoriesrel;
use BBids\BBidsHomeBundle\Entity\Vendorsubcategoriesrel;
use BBids\BBidsHomeBundle\Entity\Sessioncategoriesrel;
use Symfony\Component\Validator\Constraints as Assert;

class UserController extends Controller
{

	public function buyleadsAction()
	{
		return $this->render('BBidsBBidsHomeBundle:User:myleads.html.twig');
	}

	public function myenquiriesAction()
	{
		$session = $this->container->get('session');

		$uid = $session->get('uid');

		$enquiries = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Enquiry')->findBy(array('authorid'=>$uid));

		return $this->render('BBidsBBidsHomeBundle:User:consumerenquiries.html.twig', array('enquiries'=>$enquiries));
	}

	public function vendorenquiriesAction()
	{
		$session = $this->container->get('session');

		$uid = $session->get('uid');

		$em = $this->getDoctrine()->getManager();
		
		$query = $em->createQueryBuilder()
                         ->select(array('e.subj, e.description, e.city, e.category, e.created, e.id, v.leadstatus'))
			 ->from('BBidsBBidsHomeBundle:Enquiry','e')
			 ->innerJoin('BBidsBBidsHomeBundle:Vendorenquiryrel', 'v', 'WITH', 'e.id=v.enquiryid')
			->add('where','v.vendorid = :uid')
			->andWhere('e.status = 1')
			->add('orderBy', 'e.created ASC')
			->setParameter('uid', $uid);

		$enquiries = $query->getQuery()->getResult();
		return $this->render('BBidsBBidsHomeBundle:User:vendorenquiries.html.twig', array('enquiries'=>$enquiries));
	}
	
	public function choosepackAction()
	{
		$session = $this->container->get('session');
		
		$uid = $session->get('uid');
		
		$categoryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Sessioncategoriesrel')->findBy(array('userid'=>$uid));

		$categoryidArray = array();

		$categorynameArray = array();

		$leadCount = count($categoryArray);
		
		foreach($categoryArray as $category){
			array_push($categoryidArray, $category->getCategoryid());
			$catid = $category->getCategoryid();
			
			$categorylist= $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$catid));
			
			foreach($categorylist as $categoryname){
				$categorynameArray[$catid] = $categoryname->getCategory();
			}
		}
		
		
	 	return $this->render('BBidsBBidsHomeBundle:User:chooseleadpack.html.twig', array('count'=>$leadCount, 'categoryArray'=>$categorynameArray, 'uid'=>$uid));
	}

	public function consumerregisterAction(Request $request)
	{
		$user = new User();

		$form = $this->createFormBuilder($user)
			->add('contactname','text', array('label'=>'Enter your full name', 'mapped'=>FALSE))
			->add('email','email',array('label'=>'Enter Your email', 'attr'=>array('size'=>40)))		
			->add('smsphone','text',array('label'=>'Enter your mobile number','mapped'=>FALSE, 'invalid_message'=>'Mobile Number field cannot contain strings','attr'=>array('size'=>10), 'constraints'=> new Assert\Length(array('min'=>10, 'max'=>10, 'maxMessage'=>'Please enter a 10 digit mobile number', 'minMessage'=>'Please enter a 10 digit mobile number'))))
			->add('homecode','number',array('label'=>'Enter your home phone', 'mapped'=>FALSE, 'required'=>FALSE, 'attr'=>array('size'=>5, 'placeholder'=>'Area code')))
			->add('homephone','number', array('mapped'=>FALSE, 'required'=>FALSE, 'attr'=>array('size'=>12, 'placeholder'=>'Phone number')))
			->add('Register','submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
			->getForm();
		
		$form->handleRequest($request);

		if($request->isMethod('POST'))
		{
			if($form->isValid())
			{
				
				$smsphone = $form['smsphone']->getData();
				if(!is_numeric($smsphone)){
					$session = $this->container->get('session');
        
                                        $session->getFlashbag()->add('error','Mobile number should not contain strings');
                                        
                                        return $this->render('BBidsBBidsHomeBundle:Home:consumerregister.html.twig', array('error'=>'','form'=>$form->createView()));

				}
				$email = $form['email']->getData();
				
				$userArray  = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBy(array('email'=>$email));

                                foreach($userArray as $uses){
                                      $uid = $uses->getId();
                                }
					
				if(isset($uid)){
					$session = $this->container->get('session');
	
                                        $session->getFlashbag()->add('error','Email id already exists. Please try a different email');
					
					return $this->render('BBidsBBidsHomeBundle:Home:consumerregister.html.twig', array('error'=>'','form'=>$form->createView()));
                                }
				else {
					$smsphone = $form['smsphone']->getData();
					$home = $form['homecode']->getData();
					$name = $form['contactname']->getData();
					if(!empty($home)){
						$homephone = $form['homecode']->getData()."-".$form['homephone']->getData();	
					}
					else{
						$homephone = "";
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
					$account->setContactname($name);

					$em = $this->getDoctrine()->getManager();
					$em->persist($account);
					$em->flush();
			
					return $this->redirect($this->generateUrl('b_bids_b_bids_user_sms_verify', array('smscode'=>$mobilecode, 'userid'=>$userid, 'path'=>'login','enquiryid'=>0)));				
				}
			}
			else
			{
				return $this->render('BBidsBBidsHomeBundle:Home:consumerregister.html.twig', array('error'=>'','form'=>$form->createView()));
			}
		}
		return $this->render('BBidsBBidsHomeBundle:Home:consumerregister.html.twig', array('error'=>'','form'=>$form->createView()));
	}

	public function accountAction()
	{
		$session = $this->container->get('session');

		if($session->has('uid')){
		
			$uid = $session->get('uid');

			$user = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBy(array('id'=>$uid));

			$account = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Account')->findBy(array('userid'=>$uid));

			return $this->render('BBidsBBidsHomeBundle:User:account.html.twig', array('user'=>$user, 'account'=>$account));
		}
		
		return $this->redirect($this->generateUrl('b_bids_b_bids_home_homepage'));
		

	}
	public function smsverifyAction(Request $request, $smscode, $userid, $path, $enquiryid)
	{
		$user = new User();
		
		$form = $this->createFormBuilder($user)
                                        ->add('mobilecode','number',array('label'=>'Please enter SMS code to verify your mobile', 'attr'=>array('size'=>7)))
                                        ->add('userid','hidden', array('mapped'=>FALSE, 'data'=>$userid))
                                        ->add('Verify','submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
		                        ->getForm();
		
		$form->handleRequest($request);

		if($request->isMethod('POST')){	
			if($form->isValid()){
					
				$mobilecode = $form['mobilecode']->getData();
				$userid = $form['userid']->getData();

				$userArray  = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBy(array('id'=>$userid));
		
				foreach($userArray as $uses){
					$smscode = $uses->getMobilecode();
					$email = $uses->getEmail();
				}

				if($smscode == $mobilecode){
					$user = new User();
			
					$userhash = $this->generateHash(16);

					$em = $this->getDoctrine()->getManager();
					$user = $em->getRepository('BBidsBBidsHomeBundle:User')->find($userid);

					$user->setMobilecode('');
					$user->setStatus(2);
					$user->setUserhash($userhash);
					$em->flush();
						

					if($path == 'inline'){
						$em = $this->getDoctrine()->getManager();
						$enquiry = $em->getRepository('BBidsBBidsHomeBundle:Enquiry')->find($enquiryid);
						
						$enquiry->setStatus(1);
						
						$em->flush();					
					}
					
					$message = \Swift_Message::newInstance()
						->setSubject('New Account created - Business Bids')
        	                                ->setFrom('anup.vaze@gmail.com')
                	                        ->setTo($email)
                        	                ->setContentType("text/html")
						->setBody($this->render('BBidsBBidsHomeBundle:Home:activationemail.html.twig', array('userid'=>$userid, 'userhash'=>$userhash)));
						
					$this->get('mailer')->send($message);

					return $this->redirect($this->generateUrl('b_bids_b_bids_user_sms_verified'));
				}
				else{

					$session = $this->container->get('session');
                                
                                        $session->getFlashbag()->add('error','The code did not match at our end.');

                        		return $this->render('BBidsBBidsHomeBundle:Home:smsverify.html.twig', array('form'=>$form->createView(), 'smscode'=>$smscode));
                        
				}
			}
			else{
			 	return $this->render('BBidsBBidsHomeBundle:Home:smsverify.html.twig', array('form'=>$form->createView(), 'smscode'=>$smscode));
			}
			
		}
		
		return $this->render('BBidsBBidsHomeBundle:Home:smsverify.html.twig', array('form'=>$form->createView(), 'smscode'=>$smscode));
	}

	public function smsverifiedAction()
	{
		 $session = $this->container->get('session');

                 $session->getFlashbag()->add('success','Your mobile is verified. Please check your email for further explanation.');

	         return $this->render('::basesuccess.html.twig');

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

	public function emailverifyAction(Request $request, $userid, $userhash)
	{
		$user = new User();

		$activateAuth = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBy(array('id'=>$userid, 'userhash'=>$userhash));

                foreach($activateAuth as $user){
                        $uid = $user->getId();
			$email = $user->getEmail();
	
                }

		if(isset($uid)){
			$form = $this->createFormBuilder($user)
                                ->add('password','repeated', array(
					'type'=>'password',
					'invalid_message'=>'The password fields did not match', 
					'options'=>array('attr'=>array('class'=>'password-failed')),
					'first_options' => array('label'=>'Password'),
					'second_options'=> array('label'=>'Confirm password'),
					))
			        ->add('Activate','submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
                                ->getForm();
			
			$form->handleRequest($request);

			if($request->isMethod('POST')){
				if($form->isValid()){
					$password = md5($form['password']->getData());
					$em = $this->getDoctrine()->getManager();

                                        $user = $em->getRepository('BBidsBBidsHomeBundle:User')->find($userid);

	                                $user->setPassword($password);
                                        $user->setUserhash('');
                                        $user->setStatus(1);
					
                                        $em->flush();
						
					$accountArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Account')->findBy(array('userid'=>$userid));
					
					foreach($accountArray as $account){
						$pid = $account->getProfileid();
						$name = $account->getContactname();
					}
				
					$message = \Swift_Message::newInstance()
                                                ->setSubject('Account activated - Business Bids')
                                                ->setFrom('anup.vaze@gmail.com')
                                                ->setTo($email)
                                                ->setContentType("text/html")
                                                ->setBody('Your account is activated');

                                        $this->get('mailer')->send($message);
					
					$session = $this->container->get('session');
				
					$session->set('uid',$userid);
					$session->set('pid',$pid);
					$session->set('email',$email);
					$session->set('name', $name);

					if($pid == 2){
						return $this->redirect($this->generateUrl('b_bids_b_bids_home_homepage'));
					}
					if($pid == 3) {
						return $this->redirect($this->generateUrl('b_bids_b_bids_vendor_register_step_2'));
					}
				}
				else {
					return $this->render('BBidsBBidsHomeBundle:Home:accountactivate.html.twig', array('form'=>$form->createView()));
				}
			}
			return $this->render('BBidsBBidsHomeBundle:Home:accountactivate.html.twig', array('form'=>$form->createView()));
		}	
		else{
			return $this->render('::error.html.twig');
		}
	}	

	public function emailverifiedAction()
        {
                 $session = $this->container->get('session');

                 $session->getFlashbag()->add('success','Your account is now verified. Please check your email for further explanation.');

                 return $this->render('::basesuccess.html.twig');

        }

	public function vendorregisterAction(Request $request)
	{
		$user = new User();

                $form = $this->createFormBuilder($user)
                        ->add('email','email',array('label'=>'Enter Your email', 'attr'=>array('size'=>40)))
                         ->add('smsphone','text',array('label'=>'Enter your mobile number','mapped'=>FALSE, 'invalid_message'=>'Mobile Number field cannot contain strings','attr'=>array('size'=>10), 'constraints'=> new Assert\Length(array('min'=>10, 'max'=>10, 'maxMessage'=>'Please enter a 10 digit mobile number', 'minMessage'=>'Please enter a 10 digit mobile number'))))
                        ->add('homecode','number',array('label'=>'Enter your home phone', 'mapped'=>FALSE, 'required'=>FALSE, 'attr'=>array('size'=>5, 'placeholder'=>'Area code')))
                        ->add('homephone','number', array('mapped'=>FALSE, 'required'=>FALSE, 'attr'=>array('size'=>12, 'placeholder'=>'Phone number')))
			->add('address','textarea', array('label'=>'Enter your address', 'mapped'=>FALSE))
			->add('bizname','text', array('label'=>'Enter your business/company name', 'mapped'=>FALSE))
			->add('contactname','text', array('label'=>'Enter your full name', 'mapped'=>FALSE, 'constraints'=> new Assert\Regex(array('pattern'=>'/^[a-zA-Z]*$/', 'htmlPattern'=>'/^[a-zA-Z]*$/'))))
			->add('faxcode','number',array('label'=>'Enter your fax phone', 'mapped'=>FALSE, 'required'=>FALSE, 'attr'=>array('size'=>5, 'placeholder'=>'Area code')))
                        ->add('faxphone','number', array('mapped'=>FALSE, 'required'=>FALSE, 'attr'=>array('size'=>12, 'placeholder'=>'Phone number')))
                        
                        ->add('Register','submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
                        ->getForm();

		$form->handleRequest($request);
			
		if($request->isMethod('POST')){
			if($form->isValid()){
				$email = $form['email']->getData();
                                $smsphone = $form['smsphone']->getData();
				if(!is_numeric($smsphone)){
					$session = $this->container->get('session');

        				$session->getFlashBag()->add('error','Mobile number field cannot contain strings');
        
					return $this->render('BBidsBBidsHomeBundle:Home:vendorregister.html.twig', array('form'=>$form->createView()));        			
				}

                                $userArray  = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBy(array('email'=>$email));

                                foreach($userArray as $uses){
                                      $uid = $uses->getId();
                                }

                                if(isset($uid)){
                                        $session = $this->container->get('session');

                                        $session->getFlashbag()->add('error','Email id already exists. Please try a different email');

                                        return $this->render('BBidsBBidsHomeBundle:Home:consumerregister.html.twig', array('error'=>'','form'=>$form->createView()));
                                }

                                if(!empty($form['homecode']->getData)){
                                        $homephone = $form['homecode']->getData()."-".$form['homephone']->getData();
                                }
                                else{
                                        $homephone = "";
		                }
				$address = $form['address']->getData();
				$bizname = $form['bizname']->getData();
				$contactname = $form['contactname']->getData();
				if(!empty($form['faxcode']->getData)){
                                        $faxphone = $form['faxcode']->getData()."-".$form['faxphone']->getData();
                                }
                                else{
                                        $faxphone = "";
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

                                $account->setProfileid(3);
                                $account->setUserid($userid);
                                $account->setSmsphone($smsphone);
                                $account->setHomephone($homephone);
				$account->setAddress($address);
				$account->setBizname($bizname);
				$account->setContactname($contactname);
				$account->setFax($faxphone);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($account);
                                $em->flush();

				 return $this->redirect($this->generateUrl('b_bids_b_bids_user_sms_verify', array('smscode'=>$mobilecode, 'userid'=>$userid, 'path'=>'login','enquiryid'=>0)));
			}
			else {
				return $this->render('BBidsBBidsHomeBundle:Home:vendorregister.html.twig', array('form'=>$form->createView()));
			}
		}
		return $this->render('BBidsBBidsHomeBundle:Home:vendorregister.html.twig', array('form'=>$form->createView()));		
	}

	public function vendorcategoriesAction(Request $request)
	{
		$categories = new Categories();	

		$segmentArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('segmentid'=>111111));

		$segmentList = array();
		foreach($segmentArray as $segment){
			$segmentid = $segment->getId();
			$segmentname = $segment->getCategory();
			$segmentList[$segmentid] = $segmentname;
		}

		$categoriesArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('parentid'=>0));

		$categoryList = array();
		
		foreach($categoriesArray as $category){
			$categoryid = $category->getId();
			$categoryname = $category->getCategory();
			$categoryList[$categoryid] = $categoryname;
		}

		$subArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('segmentid'=>0));

		$subList = array();

		foreach($subArray as $sub){
			$subid = $sub->getId();
			$subname = $sub->getCategory();
			$subList[$subid] = $subname;
		}
		

		$cityArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:City')->findAll();
		
		$cityList = array();
			
		foreach($cityArray as $city){
			$cityid = $city->getId();
			$cityname = $city->getCity();
			$cityList[$cityid] = $cityname;
		
		}

		$form = $this->createFormBuilder($categories)
			->add('segment','choice', array('label'=>'Please choose your segment ', 'choices'=>$segmentList, 'multiple'=>TRUE, 'expanded'=>TRUE, 'mapped'=>FALSE, 'required'=>FALSE))
			->add('category','choice', array('label'=>'Please choose your category ', 'choices'=>$categoryList, 'multiple'=>TRUE, 'expanded'=>TRUE, 'required'=>FALSE))
			->add('subcategory','choice', array('label'=>'Please choose your segment ', 'choices'=>$subList, 'multiple'=>TRUE, 'expanded'=>TRUE, 'mapped'=>FALSE, 'required'=>FALSE))
			->add('city','choice', array('label'=>'Please choose your city', 'choices'=>$cityList, 'multiple'=>TRUE, 'expanded'=>TRUE, 'mapped'=>FALSE))
			->add('BuyLeads','submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center', 'value'=>'Buy Leads')))
			->getForm();

		$form->handleRequest($request);

		if($request->isMethod('POST')){
		
				$segmentcount = count($form['segment']->getData());
				$categorycount = count($form['category']->getData());
                                $subcategorycount = count($form['subcategory']->getData());
                                $citycount = count($form['city']->getData());
				
				if($segmentcount == 0 || $categorycount == 0 || $subcategorycount == 0 || $citycount == 0){
					$session = $this->container->get('session');

                                        $session->getFlashbag()->add('error','Selecting of one item each from Segment, Category, Subcategory, & City');

                                        return $this->render('BBidsBBidsHomeBundle:User:selectcategories.html.twig',array('form'=>$form->createView()));
				}

				$session  = $this->container->get('session');

				$uid = $session->get('uid');
				
				$categoryArray = $form['category']->getData();

				$sessionArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Sessioncategoriesrel')->findBy(array('userid'=>$uid));
				
				foreach($sessionArray as $sessiondata){
                                                $sid = $sessiondata->getId();

                                                $em = $this->getDoctrine()->getManager();
                                                $sessionrow = $em->getRepository('BBidsBBidsHomeBundle:Sessioncategoriesrel')->find($sid);

                                                $em->remove($sessionrow);
                                                $em->flush();
                                        }

				
				for($i = 0; $i < $categorycount; $i++){
					$sessioncategory = new Sessioncategoriesrel();
					
					$sessioncategory->setUserid($uid);
					$sessioncategory->setCategoryid($categoryArray[$i]);

					$em = $this->getDoctrine()->getManager();
					$em->persist($sessioncategory);
					$em->flush();
				}
				/*for($i = 0; $i < $categorycount; $i++){
					$vendor = new Vendorcategoriesrel();
					
					$vendor->setVendorid($uid);
					$vendor->setCategoryid($categoryArray[$i]);
					$vendor->setStatus(0);

					$em = $this->getDoctrine()->getManager();
					$em->persist($vendor);
					$em->flush();
				}*/
			
				$subcategoryArray = $form['subcategory']->getData();

                                for($i = 0; $i < $subcategorycount; $i++){
                                        $vendor = new Vendorsubcategoriesrel();

                                        $vendor->setVendorid($uid);
                                        $vendor->setSubcategoryid($subcategoryArray[$i]);
                                        $vendor->setStatus(0);

                                        $em = $this->getDoctrine()->getManager();
                                        $em->persist($vendor);
                                        $em->flush();
                                } 
				return $this->redirect($this->generateUrl('b_bids_b_bids_vendor_choose_lead_pack'));		
		}
		return $this->render('BBidsBBidsHomeBundle:User:selectcategories.html.twig',array('form'=>$form->createView()));
	}

	public function logoutAction()
	{
		$session = $this->container->get('session');
			
		$session->invalidate();

		return $this->redirect($this->generateUrl('b_bids_b_bids_home_homepage'));
	}		
		
	public function loginAction(Request $request){
		$session = $this->container->get('session');

		if($session->has('uid')){
			return $this->redirect($this->generateUrl('b_bids_b_bids_home_homepage'));
		}

		$user = new User();

		$form = $this->createFormBuilder($user)
			->add('email','email', array('label'=>'Enter your email id', 'attr'=>array('size'=>30, 'placeholder'=>'Email Id')))
			->add('password','password', array('label'=>'Enter your password', 'attr'=>array('size'=>18, 'placeholder'=>'Password')))
			->add('Login','submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
			->getForm();

		$form->handleRequest($request);

		if($request->isMethod('POST')){
			if($form->isValid()){
				$email = $form['email']->getData();
				$password = md5($form['password']->getData());
				
				$user = new User();
				
				$userArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBy(array('email'=>$email, 'password'=>$password));
				foreach($userArray as $user){
					$uid = $user->getId();
				}

				if(isset($uid)){
					
					$accountArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Account')->findBy(array('userid'=>$uid));

					foreach($accountArray as $account){
						$pid = $account->getProfileid();
						$name = $account->getContactname();
		
					}

					$session = $this->container->get('session');
					
					$session->set('uid',$uid);
					$session->set('email',$email);
					$session->set('pid', $pid);
					$session->set('name', $name);

					if($pid == 2){
						return $this->redirect($this->generateUrl('b_bids_b_bids_home_homepage'));
					}
					else{
						return $this->redirect($this->generateUrl('b_bids_b_bids_home'));
					}
				}
				else {
					$session = $this->container->get('session');

					$session->getFlashbag()->add('error','Email and password did not match');

					return $this->render('BBidsBBidsHomeBundle:User:login.html.twig',array('form'=>$form->createView()));
				}
				
			}
			else{
				return $this->render('BBidsBBidsHomeBundle:User:login.html.twig', array('form'=>$form->createView()));
			}
		}

		return $this->render('BBidsBBidsHomeBundle:User:login.html.twig', array('form'=>$form->createView()));
	}

	public function homeAction()
	{
		$session = $this->container->get('session');
			
		$pid = $session->get('pid');
		
		if($pid == 2){
			return $this->redirect($this->generateUrl('b_bids_b_bids_consumer_home'));
		}
		if($pid == 3){
			return $this->redirect($this->generateUrl('b_bids_b_bids_vendor_home'));
		}
	}

	public function vendorhomeAction()
	{
		return $this->render('BBidsBBidsHomeBundle:Home:vendor.html.twig');
	}

	public function consumerhomeAction()
	{
		return $this->render('BBidsBBidsHomeBundle:Home:consumer.html.twig');
	}

}
?>
