<?php

declare(strict_types=1);

namespace Internal\CustomCommand\Command;

use Internal\CustomCommand\Services\StateService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Bootstrap;

final class SiteStateCommand extends Command
{
    public function __construct(
        private StateService $stateService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('type', InputArgument::OPTIONAL, 'Control frontend site state, valid arguments are "on" and "off" and "state"', 'on');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Make sure the _cli_ user is loaded
        Bootstrap::initializeBackendAuthentication();
        $io = new SymfonyStyle($input, $output);

        return $this->handle($io, (string)$input->getArgument('type'));
    }

    private function handle(SymfonyStyle $io, string $type): int
    {
        try {
            switch($type) {
                case 'on':
                    $this->stateService->enableSite($io);
                    return Command::SUCCESS;
                    break;
                case 'off':
                    $this->stateService->disableSite($io);
                    return Command::SUCCESS;
                    break;
                default:
                    $io->error('Invalid type provided: ' . $type);
                    return Command::INVALID;
            }
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return Command::FAILURE;
        }
    }
}