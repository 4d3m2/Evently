<?php

namespace App\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Safety net: proves the test suite can never run against the development database.
 */
class TestEnvironmentTest extends KernelTestCase
{
    public function testKernelBootsInTestEnvironment(): void
    {
        // Arrange + Act
        $kernel = self::bootKernel();

        // Assert
        self::assertSame('test', $kernel->getEnvironment());
    }

    public function testTestsUseTheDedicatedTestDatabase(): void
    {
        // Arrange
        self::bootKernel();
        $registry = self::getContainer()->get('doctrine');
        self::assertInstanceOf(ManagerRegistry::class, $registry);
        $connection = $registry->getConnection();
        self::assertInstanceOf(Connection::class, $connection);

        // Act: ask PostgreSQL which database this connection is really using
        $databaseName = $connection->fetchOne('SELECT current_database()');

        // Assert
        self::assertSame('evently_test', $databaseName);
    }
}
