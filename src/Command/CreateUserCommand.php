<?php

namespace App\Command;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\UtilisateurManager;
use App\Service\UtilisateurManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'create:user',
    description: 'Create an user',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UtilisateurManagerInterface $utilisateurManager,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('login', InputArgument::OPTIONAL, 'The login of the new user')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password of the new user')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the new user')
            ->addArgument('code', InputArgument::OPTIONAL, 'The code of the new user')
            ->addArgument('visible', InputArgument::OPTIONAL, 'Is the new user visible')
            ->addArgument('admin', InputArgument::OPTIONAL, 'Give the new user the admin rights');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $login = $input->getArgument('login');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');
        $code = $input->getArgument('code');
        $visible = $input->getArgument('visible');

        $user = new Utilisateur();
        $user->setLogin($login);
        $user->setEmail($email);
        $user->setVisible($visible);
        $this->utilisateurManager->createUser($user, $password, $code);

        if ($input->getOption('admin')) {

        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('The user is now created');

        return Command::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $login = $input->getArgument('login');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');
        $code = $input->getArgument('code');
        $visible = $input->getArgument('visible');
        $admin = $input->getArgument('admin');

        if (is_null($login)) {
            $login = $io->ask('What is the login?');
            $input->setArgument('login', $login);
        }
        if (is_null($password)) {
            $password = $io->ask('What is the password?');
            $input->setArgument('password', $password);
        }
        if (is_null($email)) {
            $email = $io->ask('What is the email?');
            $input->setArgument('email', $email);
        }
        if (is_null($code)) {
            $code = $io->ask('What is the code? (press <return> to generate a code)');

            //VERIF
            $input->setArgument('code', $code);
        }
        if (is_null($visible)) {
            $visible = $io->ask('Is the user visible? (press <return> to make it visible)');
            if (is_null($visible)) {
                $visible = true;
            }
            $input->setArgument('visible', $visible);

        }
        if (is_null($admin)) {
            $admin=$io->ask('Is the user admin? (press <return> to make it normal)');
            if (is_null($admin)) {
                $admin = true;
            }
            $input->setArgument('admin', $admin);
        }
    }
}
