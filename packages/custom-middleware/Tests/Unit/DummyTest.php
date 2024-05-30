<?php

declare(strict_types=1);

namespace Internal\CustomMiddleware\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DummyTest extends UnitTestCase
{
    #[Test]
    public function dummy(): void
    {
        self::assertTrue((new Typo3Version())->getMajorVersion() === 12);
    }
}