<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AppCreateUser extends Command
{
    protected static $defaultName = 'app:create:user';

    private $manager;
    private $validator;

    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Allows you to create a user')
            ->addArgument('username', InputArgument::REQUIRED, 'Specify the username of the user')
            ->addArgument('apiToken', InputArgument::REQUIRED, 'Specify the API token of the user')
            ->addArgument('roles', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Specify the roles of the user', ['ROLE_ADMIN']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $user = new User();

        [$username, $apiToken, $roles] = [
            $input->getArgument('username'),
            $input->getArgument('apiToken'),
            $input->getArgument('roles')
        ];

        $user
            ->setApiToken($apiToken)
            ->setUsername($username)
            ->setRoles($roles);

        $errors = $this->validator->validate($user);
        if(\count($errors) > 0) {
            $io->error((string) $errors);
            return;
        }

        $this->manager->persist($user);
        $this->manager->flush();

        $io->success('Successfuly persisted ' . $user->getUsername() . ' with id "' . $user->getId() . '"');
    }
}
