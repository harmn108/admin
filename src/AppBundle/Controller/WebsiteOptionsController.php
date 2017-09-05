<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\WebsiteOptions;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class WebsiteOptionsController extends Controller
{
    /**
     * @Route("/website_name")
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
    public function saveWebsiteNameAction(Request $request){
        $data = $request->request->all();
        if (!$this->isLoggedInAction()) {
            return $this->redirect($this->generateUrl('app_user_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $errors = [];
        if (isset($data['id']) && $data['id']) {
            $websiteOprions = $em->getRepository(WebsiteOptions::class)->find($data['id']);
        }
        else{
            $websiteOprions = new WebsiteOptions();
        }

        if(!isset($data['siteName']) && !$data['siteName']){
            $errors[] = Response::$statusTexts[Response::HTTP_BAD_REQUEST];
        }
        else{
            $websiteOprions->setName($data['siteName']);

            $userInfo = $this->get('session')->get($_COOKIE['X-TOKEN']);

            /**
             * @var $user User
             */
            $user = $em->getRepository(User::class)->find($userInfo['id']);

            if($user === null){
                $errors[] = Response::$statusTexts[Response::HTTP_NOT_FOUND];
            }
            else{
                $websiteOprions->setUpdatedBy($user);
                $websiteOprions->setCreatedBy($user);
            }
        }

        if (empty($errors)) {
            $em->persist($websiteOprions);
            $em->flush();

            /**
             * @var $user User
             */
            $websiteOprions = $em->getRepository(WebsiteOptions::class)->findOneBy(['createdBy' => $user]);

            /**
             * @var $websiteOprions WebsiteOptions
             */
            if($websiteOprions !== null) {
                $userInfo['websiteOptions']['id'] = $websiteOprions->getId();
                $userInfo['websiteOptions']['name'] = $websiteOprions->getName();

                $this->get('session')->set($_COOKIE['X-TOKEN'], $userInfo);
            }

            return new JsonResponse(['websiteOptions' => ['id' => $websiteOprions->getId()]]);
        }

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }

    private function isLoggedInAction()
    {
        if (isset($_COOKIE['X-TOKEN']) && $_COOKIE['X-TOKEN']) {
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
}
