<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Role;

class LoadRoleData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $idParent = 0; // owner doesn't have parent
        $name = 'Owner';

        $role = new Role();
        $role->setIdParent($idParent);
        $role->setName($name);

        $manager->persist($role);
        $manager->flush();
    }
}