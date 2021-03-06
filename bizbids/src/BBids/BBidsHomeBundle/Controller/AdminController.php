<?php 
namespace BBids\BBidsHomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BBids\BBidsHomeBundle\Entity\User;
use BBids\BBidsHomeBundle\Entity\Categories;
use BBids\BBidsHomeBundle\Entity\Account;
use BBids\BBidsHomeBundle\Form\CategoryType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use BBids\BBidsHomeBundle\Entity\Keyword;
use Ob\HighchartsBundle\Highcharts\Highchart;

class AdminController extends Controller
{

	public function keywordsAction($categoryid)
	{
		$session = $this->container->get('session');

                if($session->has('uid') && $session->get('pid') == 1){
                        $keywords = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Keyword')->findBy(array('parentid'=>$categoryid));

                        return $this->render('BBidsBBidsHomeBundle:Admin:keywords.html.twig', array('keywords'=>$keywords, 'categoryid'=>$categoryid));
                }
                else {
                        $session = $this->container->get('session');

                        $session->getFlashBag()->add('error','You are not authorised to access this page');
      
                        return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
                }

	}

	public function subcategoriesAction($categoryid)
	{
		$session = $this->container->get('session');

                if($session->has('uid') && $session->get('pid') == 1){
			$subcategories = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('parentid'=>$categoryid));

			return $this->render('BBidsBBidsHomeBundle:Admin:subcategories.html.twig', array('subcategories'=>$subcategories, 'categoryid'=>$categoryid));
		}
		else {
                        $session = $this->container->get('session');

                        $session->getFlashBag()->add('error','You are not authorised to access this page');

                        return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
                }

		
	}

	public function addkeywordAction(Request $request, $categoryid)
	{
		$session = $this->container->get('session');

                if($session->has('uid') && $session->get('pid') == 1){

                        $categoryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$categoryid));

                        foreach($categoryArray as $c){
                                $category = $c->getCategory();
                        }

			$keywords = new Keyword();

			$form = $this->createFormBuilder($keywords)
                                ->add('category','text', array('label'=>'Parent Category', 'mapped'=>false, 'data'=>$category, 'read_only'=>TRUE))
                                ->add('keyword','text', array('label'=>'Add a Keyword', 'attr'=>array('placeholder'=>'Keyword')))
                                ->add('submit', 'submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
                                ->getForm();
			
			$form->handleRequest($request);

                        if($request->isMethod('POST')){
                                if($form->isValid()){
                                        $key = $form['keyword']->getData();
                                        $cat = $form['category']->getData();

                                        $em = $this->getDoctrine()->getManager();

                                        $keyword = new Keyword();

                                        $keyword->setParentid($categoryid);
                                        $keyword->setKeyword($key);
                                        
                                        $em->persist($keyword);

                                        $em->flush();

                                        $session = $this->container->get('session');
                                        $session->getFlashBag()->add('message','New Keyword '.$key.' added under '.$cat.' successfully!!');
                                        return $this->redirect($this->generateUrl('b_bids_b_bids_admin_categories'));
                                }
                                else {
                                        return $this->render('BBidsBBidsHomeBundle:Admin:addkeyword.html.twig', array('form'=>$form->createView()));
                                }
                        }
			return $this->render('BBidsBBidsHomeBundle:Admin:addkeyword.html.twig', array('form'=>$form->createView()));
		}
		else {
			$session = $this->container->get('session');
                        
                        $session->getFlashBag()->add('error','You are not authorised to access this page');
                        
                        return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
		}
	}

	public function addsubcategoryAction(Request $request, $categoryid)
	{	
		$session = $this->container->get('session');

		if($session->has('uid') && $session->get('pid') == 1){
	
			$categoryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$categoryid));
		
			foreach($categoryArray as $c){
				$category = $c->getCategory();
			}
		
			$categories = new Categories();

			$form = $this->createFormBuilder($categories)
				->add('category','text', array('label'=>'Parent Category', 'data'=>$category, 'read_only'=>TRUE))
				->add('subcategory','text', array('label'=>'Subcategory Name', 'mapped'=>false, 'attr'=>array('placeholder'=>'Subcategory')))
				->add('description', 'textarea', array('label'=>'Enter description', 'required'=>FALSE))
				->add('submit', 'submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
			
				->getForm();
		
			$form->handleRequest($request);
			
			if($request->isMethod('POST')){
			   	if($form->isValid()){
					$sub = $form['subcategory']->getData();
					$cat = $form['category']->getData();

					$em = $this->getDoctrine()->getManager();
					
					$categories = new Categories();

					$categories->setParentid($categoryid);	
					$categories->setCategory($sub);			
					$categories->setStatus(1);
					$categories->setDescription($desc);
						
					$em->persist($categories);
					
					$em->flush();
					
					$session = $this->container->get('session');
					$session->getFlashBag()->add('message','New subcategory '.$sub.' added under '.$cat.' successfully!!');
					return $this->redirect($this->generateUrl('b_bids_b_bids_admin_categories'));
				}
				else {
					return $this->render('BBidsBBidsHomeBundle:Admin:addsubcategory.html.twig', array('form'=>$form->createView()));
				}
			}
			return $this->render('BBidsBBidsHomeBundle:Admin:addsubcategory.html.twig', array('form'=>$form->createView()));
		}
		else {
			$session = $this->container->get('session');

                        $session->getFlashBag()->add('error','You are not authorised to access this page');

                        return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
		}

			
	}
	
	public function categoriesAction()
	{
		$session = $this->container->get('session');

                if($session->has('uid') && $session->get('pid') == 1){
                        $categories  = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('parentid'=>0));

			return $this->render('BBidsBBidsHomeBundle:Admin:categorylist.html.twig', array('categories'=>$categories));
                }
                else {
                        $session = $this->container->get('session');
                
                        $session->getFlashBag()->add('error','You are not authorised to access this page');

                        return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
                }

	}
	
	public function enquirydeleteAction($enquiryid)
	{
		$em = $this->getDoctrine()->getManager();
		
		$enquiry = $em->getRepository('BBidsBBidsHomeBundle:Enquiry')->find($enquiryid);

		$em->remove($enquiry);

		$em->flush();

		$vendorArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Vendorenquiryrel')->findBy(array('enquiryid'=>$enquiryid));

		foreach($vendorArray as $v){
			$id = $v->getId();
			
			$em = $this->getDoctrine()->getManager();
			
			$vendor = $em->getRepository('BBidsBBidsHomeBundle:Vendorenquiryrel')->find($id);

			$em->remove($vendor);

			$em->flush();
		}

		$categoryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Enquirycategoriesrel')->findBy(array('enquiryid'=>$enquiryid));
	
		foreach($categoryArray as $c){
			$id = $c->getId();

			$em = $this->getDoctrine()->getManager();

                        $category = $em->getRepository('BBidsBBidsHomeBundle:Enquirycategoriesrel')->find($id);

                        $em->remove($category);

                        $em->flush();
		}

		$subcategoryArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Enquirysubcategoriesrel')->findBy(array('enquiryid'=>$enquiryid));
        
                foreach($subcategoryArray as $c){
                        $id = $c->getId();

                        $em = $this->getDoctrine()->getManager();
                
                        $subcategory = $em->getRepository('BBidsBBidsHomeBundle:Enquirysubcategoriesrel')->find($id);

                        $em->remove($subcategory);

                        $em->flush();
                }

		$session = $this->container->get('session');

		$session->getFlashBag()->add('message','The record has been successfully deleted');
		
		return $this->redirect($this->generateUrl('b_bids_b_bids_admin_enquiries', array('offset'=>1)));
		
		
			
	}

	public function mappedvendorsAction($enquiryid)
	{
		$em = $this->getDoctrine()->getManager();
			
		$query = $em->createQueryBuilder()
			->select(array('e.enquiryid, e.vendorid, e.leadstatus, u.email, a.contactname, a.bizname'))
			->from('BBidsBBidsHomeBundle:Vendorenquiryrel', 'e')
			->innerJoin('BBidsBBidsHomeBundle:User', 'u', 'WITH', 'e.vendorid=u.id')
			->innerJoin('BBidsBBidsHomeBundle:Account', 'a', 'WITH', 'a.userid=e.vendorid')
			->add('where', 'e.enquiryid= :enquiryid')
			->setParameter('enquiryid', $enquiryid);

		$mapping = $query->getQuery()->getResult();

		return $this->render('BBidsBBidsHomeBundle:Admin:viewmappedvendors.html.twig', array('mapping'=>$mapping));
	}
	
	public function enquiryviewAction($enquiryid)
	{
		$session = $this->container->get('session');

                if($session->has('uid') && $session->get('pid')==1){

			$enquiries = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Enquiry')->findBy(array('id'=>$enquiryid));
	
       		        foreach($enquiries as $e){
                	        $category = $e->getCategory();
				$authorid = $e->getAuthorid();
             		}

	                $categories = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Categories')->findBy(array('id'=>$category));
                
        	        foreach($categories as $c){
                	        $cat = $c->getCategory();
	                }

			$users = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBy(array('id'=>$authorid));
			
			foreach($users as $u){
				$email = $u->getEmail();
			}

        	        return $this->render('BBidsBBidsHomeBundle:Admin:enquiryview.html.twig', array('enquiries'=>$enquiries, 'category'=>$cat, 'email'=>$email));
		}
		else {
			$session = $this->container->get('session');

			$session->getFlashBag()->add('error','You are not authorised to access this page');
				
			return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
		}
	
	}
	
	public function enquiriesAction($offset)
	{
		$session = $this->container->get('session');

		if($session->has('uid') && $session->get('pid')==1){

			$session = $this->container->get('session');

        	        $start = (($offset * 10) - 10);

                	$em = $this->getDoctrine()->getManager();

			$query = $em->createQueryBuilder()
				->select('e.id, e.subj, e.description, e.city, e.category, e.authorid, e.status, e.created, c.category, u.email')
				->from('BBidsBBidsHomeBundle:Enquiry', 'e')
				->innerJoin('BBidsBBidsHomeBundle:Categories', 'c', 'WITH', 'e.category=c.id')
				->innerJoin('BBidsBBidsHomeBundle:User', 'u', 'WITH', 'e.authorid=u.id')
				->add('orderBy', 'e.id DESC')
				->setFirstResult($start)
				->setMaxResults(10);
			$enquiries = $query->getQuery()->getResult();

			$from = $start + 1;

			$to = $start + 10;
	
			$em = $this->getDoctrine()->getManager();
		
			$query = $em->createQueryBuilder()
				->select('count(e.id)')
				->from('BBidsBBidsHomeBundle:Enquiry', 'e');
		
			$count = $query->getQuery()->getSingleScalarResult();

			if($to > $count)
				$to = $count;

			return $this->render('BBidsBBidsHomeBundle:Admin:enquirylist.html.twig', array('enquiries'=>$enquiries, 'from'=>$from, 'to'=>$to, 'count'=>$count));
		}
		else {
			$session = $this->container->get('session');

                        $session->getFlashBag()->add('error','You are not authorised to access this page');

                        return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
		}
	}

	public function viewuserAction($userid)
	{
		$uid = $userid;
		$session = $this->container->get('session');
                
                if($session->has('uid') && $session->get('pid')==1){
			$users = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBy(array('id'=>$uid));
			$accounts = $accountArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Account')->findBy(array('userid'=>$uid));
			return $this->render('BBidsBBidsHomeBundle:Admin:viewuser.html.twig', array('users'=>$users, 'accounts'=>$accounts));
		}
		else {
			return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
		}

	}

	public function edituserAction(Request $request, $userid)
	{
		$session = $this->container->get('session');
		
		if($session->has('uid') && $session->get('pid')==1){
			$uid = $userid;
		
			$account = new Account();

			$userArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBy(array('id'=>$uid));
			foreach($userArray as $u){
				$email  = $u->getEmail();
				$status = $u->getStatus();
			}

			$accountArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Account')->findBy(array('userid'=>$uid));
		
			foreach($accountArray as $a){
				$profileid = $a->getProfileid();
				$address = $a->getAddress();
				$smsphone = $a->getSmsphone();
				$homephone = $a->getHomephone();
				$bizname = $a->getBizname();
				$contactname = $a->getContactname();
				$fax = $a->getFax();
			}	

			$form = $this->createFormBuilder($account)
				->add('address', 'textarea', array('label'=>'Address', 'required'=> false, 'data'=>$address))
				->add('smsphone', 'text', array('label'=>'SMS-Phone', 'data'=>$smsphone, 'invalid_message'=>'Mobile Number field cannot contain strings', 'attr'=>array('size'=>10), 'constraints'=> new Assert\Length(array('min'=>10, 'max'=>10, 'minMessage'=>'Please enter a 10 digit mobile number', 'maxMessage'=>'Please enter a 10 digit mobile number'))))
				->add('homephone','text', array('label'=>'Home Phone', 'required'=>false, 'data'=>$homephone,'attr'=>array('size'=>10), 'constraints'=> new Assert\Length(array('min'=>10, 'max'=>10, 'minMessage'=>'Please enter 10 digit home phone', 'maxMessage'=>'Please enter 10 digit home phone'))))
				->add('bizname', 'text', array('label'=>'Business name', 'data'=>$bizname, 'required'=>false))
			 	->add('contactname', 'text', array('label'=>'Contact name', 'data'=>$contactname, 'required'=>false))
				->add('fax', 'text', array('label'=>'Fax Phone', 'required'=>false, 'data'=>$fax,'attr'=>array('size'=>10), 'constraints'=> new Assert\Length(array('min'=>10, 'max'=>10, 'minMessage'=>'Please enter 10 digit fax number', 'maxMessage'=>'Please enter 10 digit fax number'))))
				->add('status','choice', array('label'=>'Status', 'data'=>$status, 'mapped'=>false, 'choices'=>array(1=>'Active', 0=>'Inactive')))
				->add('update', 'submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))
				->getForm();

				$form->handleRequest($request);

				if($request->isMethod('POST')){
					if($form->isValid()){
						$smsphone = $form['smsphone']->getData();
						$homephone = $form['homephone']->getData();
						$fax = $form['fax']->getData();
						$status = $form['status']->getData();
						$address = $form['address']->getData();
						$bizname = $form['bizname']->getData();
						$contactname = $form['contactname']->getData();
						
						if(!is_numeric($smsphone)){
							$session = $this->container->get('session');

							$session->getFlashbag()->add('error','Mobile number should not contain strings');
	       		                                
							return $this->render('BBidsBBidsHomeBundle:Admin:edituser.html.twig', array('email'=>$email,'form'=>$form->createView()));
						}
						if($homephone!="" && !is_numeric($homephone)){
							$session = $this->container->get('session');
                                                
                                                        $session->getFlashbag()->add('error','Home phone should not contain strings');
                                                        
                                                        return $this->render('BBidsBBidsHomeBundle:Admin:edituser.html.twig', array('email'=>$email,'form'=>$form->createView()));

						}
						if($fax!="" && !is_numeric($fax)){
							$session = $this->container->get('session');
                                                
                                                        $session->getFlashbag()->add('error','Fax number should not contain strings');
                                                        
                                                        return $this->render('BBidsBBidsHomeBundle:Admin:edituser.html.twig', array('email'=>$email,'form'=>$form->createView()));
						}
							
						$em = $this->getDoctrine()->getManager();
							
						$user = $em->getRepository('BBidsBBidsHomeBundle:User')->find($uid);
					
						$user->setStatus($status);

						$em->flush();

						$accountArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Account')->findBy(array('userid'=>$uid));
						foreach($accountArray as $a){
							$acocuntid = $a->getId();	
						}
						
						$em = $this->getDoctrine()->getManager();
						
						$account = $em->getRepository('BBidsBBidsHomeBundle:Account')->find($accountid);	
						
						$account->setSmsphone($smsphone);
						$account->setAddress($address);
						$account->setContactname($contactname);
						$account->setHomephone($homephone);
						$account->setBizname($bizname);
						$account->setFax($fax);
						
						$em->flush();
						
						$session = $this->container->get('session');
						
						$session->getFlashBag()->add('message','The record has been updated successfully!!');
						
						return $this->redirect($this->generateUrl('b_bids_b_bids_admin_user_edit', array('userid'=>$uid)));

					
					}
				}
				
				return $this->render('BBidsBBidsHomeBundle:Admin:useredit.html.twig', array('form'=>$form->createView(), 'email'=>$email));
		}
		else{
			$session = $this->container->get('session');
			
			$session->getFlashBag()->add('error','You are not authorised to access this page');
			
			return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
		}	
	}

	public function deleteuserAction($uid)
	{
		$em = $this->getDoctrine()->getManager();
			
		$user = $em->getRepository('BBidsBBidsHomeBundle:User')->find($uid);

		$em->remove($user);
	
		$em->flush();

		$accountArray = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Account')->findBy(array('userid'=>$uid));
		
		foreach($accountArray as $a){
			$accountid = $a->getId();
		}

		$em = $this->getDoctrine()->getManager();
		
		$account = $em->getRepository('BBidsBBidsHomeBundle:account')->find($accountid);

		$em->remove($account);

		$em->flush();

		$session = $this->container->get('session');

		$session->getFlashBag()->add('message', 'User record deleted successfully!!');

		return $this->redirect($this->generateUrl('b_bids_b_bids_admin_user_list'));
	}

	public function indexAction()
	{
		$session = $this->container->get('session');

		if($session->has('uid')){
			return $this->redirect($this->generateUrl('b_bids_b_bids_admin_home'));
		}
		else {
			return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
		}
	}

	public function usersAction($offset)
	{
		$session = $this->container->get('session');
				
		$start = (($offset * 10) - 10);

		$em = $this->getDoctrine()->getManager();
		
		$query = $em->createQueryBuilder()
			->select('u.id, u.email, u.created, u.updated, u.lastaccess, u.status, a.profileid')
			->from('BBidsBBidsHomeBundle:User', 'u')
			->innerJoin('BBidsBBidsHomeBundle:Account', 'a', 'WITH', 'u.id = a.userid')
			->add('orderBy', 'u.id DESC')
			->setFirstResult($start)
                        ->setMaxResults(10);

		$users = $query->getQuery()->getResult();

		$from = $start+1;
			
		$to = $start + 10;

		$query = $em->createQueryBuilder()
			->select('count(u.id)')
			->from('BBidsBBidsHomeBundle:User', 'u');

		$count = $query->getQuery()->getSingleScalarResult();
		
		if($to > $count)
			$to = $count;	

		return $this->render('BBidsBBidsHomeBundle:Admin:userlist.html.twig', array('users'=>$users, 'from'=>$from, 'to'=>$to, 'count'=>$count));
		
	}

	public function homeAction()
	{
		$session = $this->container->get('session');

                if($session->has('uid')){
			if($session->get('pid')!=1){
				$session->getFlashBag()->add('error','Your are not authorised to access this area');
				
				return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
			}

			$year = date('Y');
			
			$em = $this->getDoctrine()->getmanager();

			$query = $em->createQueryBuilder()
				->select('count(e.id)')
				->from('BBidsBBidsHomeBundle:Enquiry', 'e');

			$totalenquirycount = $query->getQuery()->getSingleScalarResult();

			$query = $em->createQueryBuilder()
				->select('count(o.id)')
				->from('BBidsBBidsHomeBundle:Order','o');

			$totalordercount = $query->getQuery()->getSingleScalarResult();		

			$query = $em->createQueryBuilder()
				->select('count(u.id)')
				->from('BBidsBBidsHomeBundle:User', 'u')
				->add('where' , 'u.status=0');

			$totalusercountforapproval = $query->getQuery()->getSingleScalarResult();

			$query = $em->createQueryBuilder()
				->select('o.ordernumber, u.email, o.status, o.created, o.id, o.amount, o.author')
				->from('BBidsBBidsHomeBundle:Order', 'o')
				->innerJoin('BBidsBBidsHomeBundle:User', 'u', 'WITH', 'o.author=u.email')
				->add('orderBy', 'o.id DESC')
				->setFirstResult(0)
				->setMaxResults(10);

			$orders = $query->getQuery()->getResult();
		
			$query = $em->createQueryBuilder()
				->select('e.subj', 'e.id', 'c.category', 'e.authorid', 'u.email', 'e.created' ,'e.status')	
				->from('BBidsBBidsHomeBundle:Enquiry', 'e')
				->innerJoin('BBidsBBidsHomeBundle:Categories', 'c', 'with', 'e.category = c.id')
				->innerJoin('BBidsBBidsHomeBundle:User', 'u', 'with', 'e.authorid=u.id')
				->add('orderBy','e.id DESC')
				->setFirstResult(0)
				->setMaxResults(10);

			$enquiries = $query->getQuery()->getResult();
	
			$query = $em->createQueryBuilder()
				->select('count(e.id)')
				->from('BBidsBBidsHomeBundle:Enquiry', 'e')
				->add('groupBy','e.created');
			
			$enquirygraphcount = $query->getQuery()->getResult();

			$series = array(
				array('name'=> "Number of Enquiries: ", "data"=>$enquirygraphcount)
			);
			
			$ob = new HighChart();	
			$ob->chart->renderTo('linechart');
			$ob->title->text('Enquiry per day');
			$ob->xAxis->title(array('text'=> "Days"));
			$ob->yAxis->title(array('text'=>'Number of enquiries'));
			$ob->series($series);
			
		
			return $this->render('BBidsBBidsHomeBundle:Admin:home.html.twig', array('totalenquirycount'=>$totalenquirycount, 'totalordercount'=>$totalordercount, 'totalusercountforapproval'=>$totalusercountforapproval, 'orders'=>$orders, 'enquiries'=>$enquiries, 'chart'=>$ob));
                }
                else {
                        return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
                }

	}

	public function loginAction(Request $request)
	{ 	
		$USER = new User();

		$session = $this->container->get('session');

		if($session->has('uid')){
			return $this->redirect($this->generateUrl('b_bids_b_bids_admin_home'));
		}
		else{
			$form = $this->createFormBuilder($USER)
				->add('email','email', array('label'=>'Enter your email: ', 'required'=>TRUE, 'attr'=>array('size'=>20, 'placeholder'=>'Your Email Address')))
				->add('password','password', array('label'=>'Your Password: ','required'=>TRUE, 'attr'=>array('size'=>20,'placeholder'=>'Your password')))
                                ->add('Login','submit', array('attr'=>array('class'=>'btn btn-success btn-getquotes text-center')))

				->getForm();
			$form->handleRequest($request);

                        if($request->isMethod('POST')){

                                $email = $form['email']->getData();
                                $password = md5($form['password']->getData());

                                $userAuth = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBy(array('email'=>$email, 'password'=>$password));
                                foreach($userAuth as $u){
                                        $uid = $u->getId();
                                 }

                                if(isset($uid)){

                                        $userStatus = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:User')->findBy(array('id'=>$uid));

                                        foreach($userStatus as $u){
                                                $status = $u->getStatus();
                                        }

                                        if($status == 1){
                                                $accountAuth = $this->getDoctrine()->getRepository('BBidsBBidsHomeBundle:Account')->findBy(array('userid'=>$uid));

                                                foreach($accountAuth as $a){
                                                        $accountid = $a->getId();
                                                        $name = $a->getContactname();
							$pid = $a->getProfileid();
                                                }
						
						if($pid != 1){
							$session = $this->container->get('session');
							$session->getFlashBag()->add('error', 'You are not authorised to access this area');
							return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
						}

                                                $session->set('uid',$uid);
                                                $session->set('pid',$pid);
                                                $session->set('aid',$accountid);
                                                $session->Set('email',$email);
                                                $session->set('name',$name);
					
						$em = $this->getDoctrine()->getManager();
                                                $u = $em->getRepository('BBidsBBidsHomeBundle:User')->find($uid);

                                                $dateNow = new \DateTime();

                                                $u->setLastaccess($dateNow);

                                                $em->flush();

                                                return $this->redirect($this->generateUrl('b_bids_b_bids_admin_home'));
                                        }
                                        else{
                                                return $this->render('::error.html.twig', array('error'=>'Your account is Blocked. Please contact the system administrator'));
                                        }
                                }
				else{
					$session = $this->container->get('session');
					
					$session->getFlashBag()->add('error','Wrong email and password entered. Please enter the correct credentials');
					return $this->redirect($this->generateUrl('b_bids_b_bids_admin_login'));
				}
			}
		}	
		return $this->render('BBidsBBidsHomeBundle:Home:login.html.twig', array('form'=>$form->createView()));
 	}
	public function categorylistAction()
	{
		$categoryList = $this->getDoctrine()->getRepository();
	} 
}
?>
