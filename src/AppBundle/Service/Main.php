<?php

namespace AppBundle\Service;


use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class Main
{

    private  $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function isLoggedInAction()
    {
        if (isset($_COOKIE['X-TOKEN']) && $_COOKIE['X-TOKEN']) {
            $repository = $this->em->getRepository(User::class);

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

    public function getRolesHierarchy() {
        $repository = $this->em->getRepository(Role::class);

        /**
         * @var $user User
         */
        $roles = $repository->findAll();

        $session = new Session();
        $userInfo = $session->get($_COOKIE['X-TOKEN']);

        $rolesArray = [];
        foreach ($roles as $item){
            $rolesArray[$item->getId()] = [];

            $rolesArray[$item->getId()]['id'] = $item->getId();
            $rolesArray[$item->getId()]['idParent'] = $item->getIdParent();
            $rolesArray[$item->getId()]['name'] = $item->getName();
        }

        $rolesHierarchy = self::buildRolesTree($rolesArray, $userInfo['roleIdParent']);

        return $rolesHierarchy;
    }

    /**
     * @param array $elements
     * @param int $parentId
     * @return array
     */
    private static function buildRolesTree(array $elements, $parentId = 0) {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['idParent'] == $parentId) {
                $children = self::buildRolesTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}