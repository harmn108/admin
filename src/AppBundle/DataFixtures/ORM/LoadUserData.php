<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $email = 'test_999@arattauna.com';
        $username = 'u' . md5($email);
        $token = md5('WQDFDS78SDFDSF67S5SF6FS6FSSDFS99');
        $tokenExpirationDate = time() + 30*86400;
        $registrationDate = time();
        $confirmationCode = 'DFKLSDFS87S8D7FSDDFSDFJSDF8F78QQ';
        $registrationIp = '127.0.0.1';

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setTokenExpirationDate($tokenExpirationDate);
        $user->setToken($token);
        $user->setRegistrationDate($registrationDate);
        $manager->persist($user);
        $manager->flush();
    }
}