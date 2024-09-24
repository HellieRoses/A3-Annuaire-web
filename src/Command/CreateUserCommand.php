<?php

namespace App\Command;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\CreateUserHelper;
use App\Service\UtilisateurManager;
use App\Service\UtilisateurManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
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
        private readonly CreateUserHelper $createUserHelper,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('login','l' ,InputOption::VALUE_REQUIRED, 'The login of the new user')
            ->addOption('password','p', InputOption::VALUE_REQUIRED, 'The password of the new user')
            ->addOption('email', null,InputOption::VALUE_REQUIRED, 'The email of the new user')
            ->addOption('code', null,InputOption::VALUE_OPTIONAL, 'The code of the new user')
            ->addOption('visible',null, InputOption::VALUE_NEGATABLE, 'Is the new user visible')
            ->addOption('admin', null,InputOption::VALUE_NEGATABLE, 'Give the new user the admin rights');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $login = $input->getOption('login');
        $password = $input->getOption('password');
        $email = $input->getOption('email');
        $code = $input->getOption('code');
        $visible = $input->getOption('visible');

        $user = new Utilisateur();
        $user->setLogin($login);
        $user->setEmail($email);
        $user->setVisible($visible);
        $this->utilisateurManager->createUser($user, $password, $code);

        if ($input->getOption('admin')) {
            $user->addRole("ROLE_ADMIN");
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('The user is now created');

        return Command::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $login = $input->getOption('login');
        $password = $input->getOption('password');
        $email = $input->getOption('email');
        $code = $input->getOption('code');
        $visible = $input->getOption('visible');
        $admin = $input->getOption('admin');

        while (!$this->createUserHelper->verifyLogin($login)) {
            $io->note('The login must be provided and not be already taken');

            $login = $io->ask('What is the login?');
        }
        $input->setOption('login', $login);

        while (!$this->createUserHelper->verifyPassword($password)) {
            $io->note('The Password must be provided and ...');

            $password = $io->askHidden('What is the password?');

        }
        $input->setOption('password', $password);

        while (!$this->createUserHelper->verifyEmail($email)){
            $io->note("The email must be provided and not be already taken");

            $email = $io->ask('What is the email?');
        }
        $input->setOption('email', $email);
        $generate=false;
        while (!$this->createUserHelper->verifyCode($code,$generate)) {
            $io->note("The code must be 8 alphanumeric characters long");

            $code = $io->ask('What is the code? (press <return> to generate a code)');
            if (is_null($code)) {
                $generate=true;
            }
        }
        $input->setOption('code', $code);

        if (is_null($visible)) {
            $visible = $io->confirm('Is the user visible? (press <return> to make it visible)');
            $input->setOption('visible', $visible);

        }
        if (is_null($admin)) {
            $admin=$io->confirm('Is the user admin? (press <return> to make it admin)',false);
            $input->setOption('admin', $admin);
        }
    }
}
