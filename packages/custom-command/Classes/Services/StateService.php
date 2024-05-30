<?php

declare(strict_types=1);

namespace Internal\CustomCommand\Services;

use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;

final class StateService
{
    public function __construct(
        private SiteFinder $siteFinder,
        private ConnectionPool $connectionPool,
    ) {
    }

    public function enableSite(SymfonyStyle $io): void
    {
        foreach ($this->siteFinder->getAllSites() as $site) {
            $io->write(sprintf(
                'Enable site "%s"[RootPID: %s][URI: %s] ... ',
                $site->getIdentifier(),
                $site->getRootPageId(),
                $this->getSiteBaseUri($site),
            ));
            $record = $this->getRecord($site->getRootPageId());
            if ($record === null) {
                $io->writeln('<error>not found</error>');
                continue;
            }
            $this->updatePage($site->getRootPageId(), ['hidden' => 0]);
            $io->writeln('<info>enabled</info>');
        }
    }

    public function disableSite(SymfonyStyle $io): void
    {
        foreach ($this->siteFinder->getAllSites() as $site) {
            $io->write(sprintf(
                'Disable site "%s"[RootPID: %s][URI: %s] ... ',
                $site->getIdentifier(),
                $site->getRootPageId(),
                $this->getSiteBaseUri($site),
            ));
            $record = $this->getRecord($site->getRootPageId());
            if ($record === null) {
                $io->writeln('<error>not found</error>');
                continue;
            }
            $this->updatePage($site->getRootPageId(), ['hidden' => 1]);
            $io->writeln('<info>disabled</info>');
        }
    }

    private function getSiteBaseUri(Site $site): string
    {
        $primaryUrl = rtrim((string)(getenv('DDEV_PRIMARY_URL') ?: ''), '/');
        $baseUri = ltrim((string)$site->getBase(), '/');
        return ltrim($primaryUrl . '/' . $baseUri, '/');
    }

    public function getRecord(int $pageId): ?array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        return $queryBuilder
            ->select('*')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($pageId, Connection::PARAM_INT)),
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative() ?: null;
    }

    public function updatePage(int $pageId, array $data): void
    {
        $this->connectionPool->getConnectionForTable('pages')
            ->update('pages', $data, ['uid' => $pageId]);
    }
}