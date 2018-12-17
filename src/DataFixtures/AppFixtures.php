<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppFixtures extends Fixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $userData = [
        'ROLE_USER' => [
            'username' => 'alex',
            'email' => 'alex@host.com',
            'plainPassword' => 'alex'
        ],
        'ROLE_ADMIN' => [
            'username' => 'spam',
            'email' => 'spam@host.com',
            'plainPassword' => 'spam'
        ],
    ];

    protected function loadUserData()
    {
        /* @var $userManager \FOS\UserBundle\Model\UserManager */
        $userManager = $this->container->get('fos_user.user_manager');

        foreach ($this->userData as $role => $data) {
            $user = $userManager->createUser();

            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPlainPassword($data['plainPassword']);

            $user->setEnabled(true);

            $user->setRoles(['ROLE_USER', $role]);

            $userManager->updateUser($user);
        }
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->loadUserData();

        $manager->flush();
    }

    /**
     * Sets the container.
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
