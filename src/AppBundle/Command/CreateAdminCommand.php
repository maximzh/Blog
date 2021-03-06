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
use Symfony\Component\Console\Question\Question;


class CreateAdminCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('admin:create')
            ->setDescription('Create blog administrator')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Admin name'
            )
            ->addArgument(
                'password',
                InputArgument::OPTIONAL,
                'Admin password'
            )
            ->addArgument(
                'email',
                InputArgument::OPTIONAL,
                'Admin email'
            );
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {

        if (null !== $input->getArgument('name') && null !== $input->getArgument(
                'password'
            ) && null !== $input->getArgument('email')
        ) {
            return;
        }

        $output->writeln('Admin creation');

        $console = $this->getHelper('question');

        $username = $input->getArgument('name');
        if (null === $username) {
            $question = new Question(' > <info>Username</info>: ');
            $question->setValidator(
                function ($answer) {
                    if (empty($answer)) {
                        throw new \RuntimeException('Username should not be empty');
                    }

                    return $answer;
                }
            );

            $username = $console->ask($input, $output, $question);
            $input->setArgument('name', $username);

        } else {
            $output->writeln(' > <info>Username</info>: '.$username);
        }

        $password = $input->getArgument('password');
        if (null === $password) {
            $question = new Question(' > <info>Password</info>: ');
            $question->setValidator(
                function ($answer) {
                    if (strlen($answer) < 6) {
                        throw new \Exception('The password must be at least 6 characters long');
                    }

                    return $answer;
                }
            );

            $password = $console->ask($input, $output, $question);
            $input->setArgument('password', $password);
        } else {
            $output->writeln(' > <info>Password</info>: '.$password);
        }

        $email = $input->getArgument('email');
        if (null === $email) {
            $question = new Question(' > <info>Email</info>: ');
            $question->setValidator(
                function ($answer) {
                    if (empty($answer) or false === strpos($answer, '@')) {
                        throw new \Exception('Wrong email');
                    }

                    return $answer;
                }
            );

            $email = $console->ask($input, $output, $question);
            $input->setArgument('email', $email);

        } else {
            $output->writeln(' > <info>Email</info>: '.$email);
        }

    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $users = $em->getRepository('AppBundle:User')
            ->findAll();
        if ($users) {
            foreach ($users as $user) {
                if ($user->getIsAdmin()) {
                    throw new \RuntimeException('Admin already exists!');
                }
            }
        }

        $name = $input->getArgument('name');
        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('password');

        $userExists = $em->getRepository('AppBundle:User')
            ->findOneBy(array('username' => $name));
        $userWithEmailExists = $em->getRepository('AppBundle:User')
            ->findOneBy(array('email' => $email));
        if ($userExists) {
            throw new \RuntimeException('User already exists: '.$name);
        }

        if ($userWithEmailExists) {
            throw new \RuntimeException('User with this email already exists: '.$email);
        }

        $admin = new User();
        $admin->setIsAdmin(true);
        $admin->setUsername($name);
        $admin->setEmail($email);
        $admin->setPlainPassword($plainPassword);

        $password = $this->getContainer()->get('security.password_encoder')
            ->encodePassword($admin, $admin->getPlainPassword());
        $admin->setPassword($password);

        $em->persist($admin);
        $em->flush();

        $output->writeln('Admin created!');

    }
}