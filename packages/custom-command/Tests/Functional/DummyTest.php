<?php

declare(strict_types=1);

namespace Internal\CustomCommand\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class DummyTest extends FunctionalTestCase
{
    protected function setUp(): void
    {
        $this->testExtensionsToLoad = array_merge(
            $this->testExtensionsToLoad,
            [
                'internal/custom-command',
            ]
        );
        parent::setUp();
    }

    #[Test]
    public function customCommandExtensionLoaded(): void
    {
        self::assertTrue(ExtensionManagementUtility::isLoaded('custom_command'));
    }

    #[Test]
    public function customMiddlewareExtensionNotLoaded(): void
    {
        self::assertFalse(ExtensionManagementUtility::isLoaded('custom_middleware'));
    }
}