<?php

namespace App\Command;


use App\Entity\Config;
use App\Entity\DateLog;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InitialCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->encoder = $encoder;
    }


    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('config:initial')
            ->setDescription('Initial config');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->manager;
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

        if (!$manager->find(Config::class, 3)) {

            $config = new Config();
            $config->setName('months');
            $config->setValue(serialize([]));
            $manager->persist($config);
            $manager->flush();
        }

        $output->write('===Done== ');

        return Command::SUCCESS;
    }
}
