<?php

namespace App\Command;

use App\Component\User\UserManager;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsCommand(
    name: 'ask:roles:delete-from-user',
    description: 'Deletes a role from the user',
)]
class AskRolesDeleteFromUserCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private UserManager $userManager,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $questionHelper = $this->getHelper('question');
        $userIdQuestion = new Question('User id: ');
        $roleQuestion = new Question('Role: ');

        $user = null;
        $role = '';

        while ($user === null) {
            $userId = $questionHelper->ask($input, $output, $userIdQuestion);

            $user = $this->userRepository->find((int)$userId);

            if ($user === null) {
                $io->warning('User is not found by id: #' . $userId);
            }
        }

        while (empty($role)) {
            $role = $questionHelper->ask($input, $output, $roleQuestion);

            if (!$this->hasRole($user, $role)) {
                $io->warning('The user have not a role: ' . $role);
                return 0;
            }
        }

        $user->deleteRole($role);
        $this->userManager->save($user, true);

        return Command::SUCCESS;
    }

    private function hasRole(UserInterface $user, string $roleName): bool
    {
        return in_array($roleName, $user->getRoles(), true);
    }
}
