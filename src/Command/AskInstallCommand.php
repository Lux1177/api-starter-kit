<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Interfaces\GetOutputInterface;
use App\Command\Traits\RunCommandTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'ask:install',
    description: 'Do first settings for Api-Starter-Kit',
)]
class AskInstallCommand extends Command implements GetOutputInterface
{
    use RunCommandTrait;

    private OutputInterface $output;
    private SymfonyStyle $symfonyIO;

    public function __construct(
        private EntityManagerInterface $entityManager,
        string $name = null
    ) {
        parent::__construct($name);
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function getSymfonyStyleOutput(): SymfonyStyle
    {
        return $this->symfonyIO;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyIO = new SymfonyStyle($input, $output);
        $this->output = $output;

        $this->runCommandAndNotify('ask:deploy');
        $this->runCommandAndNotify('ask:generate:jwtKeys');
        $this->runCommandAndNotify('d:d:c');

        $this->symfonyIO->success('Successfully installed');

        return Command::SUCCESS;
    }
}
