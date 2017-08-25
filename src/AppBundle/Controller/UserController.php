<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
    public function loginAction()
    {
        $form = $this->createFormBuilder()
            ->setAction('login_check')
            ->setMethod('POST')
            ->add('email', TextType::class, ['attr'   =>  array('class'   => 'form-control m-bottom-10')])
            ->add('password', PasswordType::class, ['attr'   =>  array('class'   => 'form-control m-bottom-10')])
            ->add('Sign In', SubmitType::class, ['attr'   =>  array('class'   => 'btn btn-primary btn-block m-top-10', 'label' => 'SignIn')])
            ->getForm();

        return $this->render('user/login.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/login_check")
     * @Method({"POST"})
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
    public function loginCheckAction(Request $request)
    {
        $data = $request->request->get('form');
        $email = $data['email'];
        $password = $data['password'];

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);

        /**
         * @var $user User
         */
        $user = $repository->findOneBy(['email' => $email, 'password' => md5($password)]);

        if ($user === null) {
            return new JsonResponse(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

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

        setcookie('X-API-TOKEN', $token, time()+3600, "/");

        return new JsonResponse([
            'token' => $token,
            'email' => $user->getEmail(),
            'roleId'=>$user->getRoleId()->getId(),
            'roleName'=>$user->getRoleId()->getName(),
            'firstName' => $user->getFirstName(),
            'lastName'=>$user->getLastName()
        ]);
    }
}
