<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 06.02.16
 * Time: 14:09
 */

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CreateAdminCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('admin:create')
            ->setDescription('Create blog administrator')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Admin name required'
            )
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'Admin email required'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'Admin password required'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('password');

        $admin = new User();
        $admin->setIsAdmin(true);
        $admin->setUsername($name);
        $admin->setEmail($email);
        $admin->setPlainPassword($plainPassword);

        $password = $this->getContainer()->get('security.password_encoder')
            ->encodePassword($admin, $admin->getPlainPassword());
        $admin->setPassword($password);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($admin);
        $em->flush();

        $output->writeln('Admin created!');

    }
}