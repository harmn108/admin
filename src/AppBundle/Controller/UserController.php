<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{

    /**
     * @Route("/")
     * @Method({"GET"})
     *
     * @ApiDoc(
     *   resource=true,
     *   description="This REST is for first step of authentication",
     *   statusCodes={
     *     200="Success",
     *     404="Not found"
     *   }
     * )
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/login")
     * @Method({"GET", "POST"})
     *
     * @ApiDoc(
     *   resource=true,
     *   description="This REST is ",
     *   statusCodes={
     *     200="Success",
     *     404="Not found"
     *   }
     * )
     */
    public function loginAction(Request $request)
    {
        if($this->isLoggedInAction()){
            return $this->redirect('admin');
        }

        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('email', EmailType::class, [
                'attr'   =>  [
                    'class'   => 'form-control m-bottom-10', 
                    'required' => 'required',
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr'   =>  [
                    'class'   => 'form-control m-bottom-10', 
                    'required' => 'required'
                ]
            ])
            ->add('Sign In', SubmitType::class, ['attr'   =>  array('class'   => 'btn btn-primary btn-block m-top-10', 'label' => 'SignIn')])
            ->getForm();

        $errors = [];

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = $data['email'];
            $password = $data['password'];

            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(User::class);

            /**
             * @var $user User
             */
            $user = $repository->findOneBy(['email' => $email, 'password' => md5($password)]);

            if ($user === null) {
                $errors[] = 'Invalid Email or Password';
            }
            else{
                //  generate token
                $token = md5(random_bytes(16));
                $tokenEncoded = md5($token);
                $findUser = $repository->findOneBy(['token' => $tokenEncoded]);

                //  check if token is unique else generate new one
                while ($findUser !== null) {
                    $token = md5(random_bytes(16));
                    $tokenEncoded = md5($token);
                    $findUser = $repository->findOneBy(['token' => $tokenEncoded]);
                }

                $user->setToken($tokenEncoded);
                $em->persist($user);
                $em->flush();

                setcookie('X-TOKEN', $token, time()+3600, "/");

                $session = new Session();

                $userInfo = [];
                $userInfo['id'] = $user->getId();
                $userInfo['firstName'] = $user->getFirstName();
                $userInfo['lastName'] = $user->getLastName();
                $userInfo['email'] = $user->getEmail();
                $userInfo['roleId'] = $user->getRoleId()->getId();
                $userInfo['roleIdParent'] = $user->getRoleId()->getIdParent();
                $userInfo['roleName'] = $user->getRoleId()->getName();

                $userInfo['pages'] = ['Structure', 'Media', 'Article']; // from DB

                $session->set($token, $userInfo);

                return $this->redirect('admin');
            }
        }

        return $this->render('user/login.html.twig', array(
            'form' => $form->createView(),
            'errors' => $errors
        ));
    }
    
    /**
     * @Route("/admin")
     * @Method({"GET"})
     *
     * @ApiDoc(
     *   resource=true,
     *   description="This REST is for first step of authentication",
     *   statusCodes={
     *     200="Success",
     *     404="Not found"
     *   }
     * )
     */
    public function adminAction()
    {
        if(!$this->isLoggedInAction()){
            return $this->redirect('login');
        }

        return $this->render('user/admin.html.twig');
    }

    private function isLoggedInAction()
    {
       if(isset($_COOKIE['X-TOKEN']) && $_COOKIE['X-TOKEN']){
           $em = $this->getDoctrine()->getManager();
           $repository = $em->getRepository(User::class);

           /**
            * @var $user User
            */
           $user = $repository->findOneBy(['token' => md5($_COOKIE['X-TOKEN'])]);

           if ($user !== null) {
               return true;
           }
       }

        return false;
    }

    /**
     * @Route("/logout")
     * @Method({"GET", "POST"})
     *
     * @ApiDoc(
     *   resource=true,
     *   description="This REST is ",
     *   statusCodes={
     *     200="Success",
     *     404="Not found"
     *   }
     * )
     */
    public function logoutAction(){
        if (isset($_COOKIE['X-TOKEN'])) {
            $this->get('session')->remove($_COOKIE['X-TOKEN']);
            unset($_COOKIE['X-TOKEN']);
            setcookie('X-TOKEN', null, -1, '/');
            return $this->redirect('login');
        } else {
            return false;
        }
    }

    /**
     * @Route("/control")
     * @Method({"GET"})
     *
     * @ApiDoc(
     *   resource=true,
     *   description="This REST is ",
     *   statusCodes={
     *     200="Success",
     *     404="Not found"
     *   }
     * )
     */
    public function controlAction(){
        if($this->isLoggedInAction()){
            $rolesHierarchy = $this->getRolesHierarchy();
        }
        else{
            return $this->redirect('login');
        }

        return $this->render('user/control.html.twig', ['rolesHierarchy' => $rolesHierarchy]);
    }

    /**
     * @Route("/access")
     * @Method({"GET"})
     *
     * @ApiDoc(
     *   resource=true,
     *   description="This REST is ",
     *   statusCodes={
     *     200="Success",
     *     404="Not found"
     *   }
     * )
     */
    public function accessAction(){

        if($this->isLoggedInAction()){
            $rolesHierarchy = $this->getRolesHierarchy();
        }
        else{
            return $this->redirect('login');
        }

        return $this->render('user/access.html.twig', ['rolesHierarchy' => $rolesHierarchy]);
    }

    public function getRolesHierarchy() {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Role::class);

        /**
         * @var $user User
         */
        $roles = $repository->findAll();

        $userInfo = $this->get('session')->get($_COOKIE['X-TOKEN']);

        $rolesArray = [];
        foreach ($roles as $item){
            $rolesArray[$item->getId()] = [];

            $rolesArray[$item->getId()]['id'] = $item->getId();
            $rolesArray[$item->getId()]['idParent'] = $item->getIdParent();
            $rolesArray[$item->getId()]['name'] = $item->getName();
        }

        $rolesHierarchy = $this->buildTree($rolesArray, $userInfo['roleIdParent']);

        return $rolesHierarchy;
    }

    private function buildTree(array $elements, $parentId = 0) {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['idParent'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    /**
     * @Route("/get_user_by_id")
     * @Method({"POST"})
     *
     * @ApiDoc(
     *   resource=true,
     *   description="This REST is ",
     *   statusCodes={
     *     200="Success",
     *     404="Not found"
     *   }
     * )
     */
    public function getUserrByIdAction(Request $request){
        if(!$this->isLoggedInAction()) {
            return $this->redirect('login');
        }
        $data = $request->request->all();

        $errors = [];
        if (!isset($data['id'])){
           $errors[] = 'Invalid Request';
        }
        $id = $data['id'];
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);

        /**
         * @var $user User
         */
        $user = $repository->find($id);

        if ($user === null) {
            $errors[] = 'User Not Found';
        }
        else{
            $userInfo = [];
            $userInfo['id'] = $user->getId();
            $userInfo['firstName'] = $user->getFirstName();
            $userInfo['lastName'] = $user->getLastName();
            $userInfo['email'] = $user->getEmail();
            $userInfo['roleId'] = $user->getRoleId()->getId();
            $userInfo['roleIdParent'] = $user->getRoleId()->getIdParent();
            $userInfo['roleName'] = $user->getRoleId()->getName();

            return new JsonResponse($userInfo);
        }

        return new JsonResponse($errors, 400);
    }

    /**
     * @Route("/update_user")
     * @Method({"POST"})
     *
     * @ApiDoc(
     *   resource=true,
     *   description="This REST is ",
     *   statusCodes={
     *     200="Success",
     *     404="Not found"
     *   }
     * )
     */
    public function updateUserAction(Request $request){
        if(!$this->isLoggedInAction()) {
            return $this->redirect('login');
        }
        $data = $request->request->all();

        $errors = [];
        if (!isset($data['id'])){
           $errors[] = 'Invalid Request';
        }
        $id = $data['id'];
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);

        /**
         * @var $user User
         */
        $user = $repository->find($id);

        if ($user === null) {
            $errors[] = 'User Not Found';
        }
        else{
            if(isset($data['firstName']) && $data['firstName']){
                $user->setFirstName($data['firstName']);
            }
            if(isset($data['lastName']) && $data['lastName']){
                $user->setLastName($data['lastName']);
            }
            if(isset($data['email']) && $data['email']){
                $user->setEmail($data['email']);
            }
            if(isset($data['password']) && $data['password']){
                $user->setPassword($data['password']);
            }

            $em->persist($user);
            $em->flush();

            return new JsonResponse($user->getId());
        }

        return new JsonResponse($errors, 400);
    }
}
