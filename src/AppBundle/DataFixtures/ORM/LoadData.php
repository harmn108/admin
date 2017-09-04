<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Module;
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

        // add default role
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

        // add default user
        $user = new User();
        $user->setEmail($email);
        $user->setRoleId($role);
        $user->setToken($tokenEncoded);
        $user->setPassword($password);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        $manager->persist($user);
        $manager->flush();

        // add default modules
        $modulesList = [
            "control" => "Control Panel",
            "access" => "Access",
            "logout" => "Sign Out",
            "structure" => "Structure",
            "media" => "Media",
            "article" => "Article"
        ];

        $counter = 0;
        foreach ($modulesList as $key => $value) {
            $module = new Module();
            $module->setName($value);
            $module->setRoute($key);
            $module->setOrder($counter);
            $module->setCreatedBy($user);
            $module->setUpdatedBy($user);

            $counter++;
            $manager->persist($module);
            $manager->flush();

        }

    }
}