<?php

namespace App\DataFixtures;

use App\Entity\Config;
use App\Entity\DateLog;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->encoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $roles = [
            "ROLE_SUPERUSER" => "Super Admin",
            "ROLE_EDITORIAL" => "Manager",
            "ROLE_ADMINISTRATOR" => "Admin",
            "ROLE_WRITER" => "Redacteur"
        ];

        foreach ($roles as $key => $value) {
            if (!$manager->getRepository(Role::class)->findByRoleName([$key])) {
                $role = new Role();
                $role->setRoleName($key);
                $role->setLibelle($value);
                $manager->persist($role);
                $manager->flush();
            }
        }

        $user = new User();
        if (!$manager->find(User::class, 1)) {
            $user->setUsername('admin');
            $user->setRoles(["ROLE_SUPERUSER"]);
            $user->setPassword($this->encoder->encodePassword($user, 'admin'));
            $user->setNomComplet('Admin');
            $user->setEmail('admin@example.com');
            $user->setValid(true);
            $user->setDeleted(false);
            $user->setAdmin(true);
            $manager->persist($user);

            $manager->flush();
        }


        $config = new Config();
        if (!$manager->find(Config::class, 1)) {
            $config = new Config();
            $config->setName('deduct');
            $config->setValue(20);
            $manager->persist($config);

            $manager->flush();
        }
        if (!$manager->find(Config::class, 2)) {
            $deductGroup = DateLog::DEFAULT_DEDUCT_GROUP;
            $config = new Config();
            $config->setName('deduct_group');
            $config->setValue(serialize($deductGroup));
            $manager->persist($config);
            $manager->flush();
        }

    }
}

