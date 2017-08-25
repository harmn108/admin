<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
        // replace this example code with whatever you need
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
            ->add('email', TextType::class)
            ->add('password', PasswordType::class, ['attr'   =>  array('class'   => 'c4')]
            )
            ->add('save', SubmitType::class, array('label' => 'Login'))
            ->getForm();

        return $this->render('user/login.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
