<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;

class LoadData implements FixtureInterface
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

        $email = 'test@test.com';
        $token = md5(random_bytes(16));
        $tokenEncoded = md5($token);
        $password = md5('test_user');
        $firstName = 'test_first_name';
        $lastName = 'test_last_name';

        $user = new User();
        $user->setEmail($email);
        $user->setRoleId($role);
        $user->setToken($tokenEncoded);
        $user->setPassword($password);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        $manager->persist($user);
        $manager->flush();
    }
}