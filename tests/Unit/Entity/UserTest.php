<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testNewUserIsActiveAndCreatedInUtc(): void
    {
        $user = new User();

        self::assertTrue($user->isActive());
        self::assertSame('UTC', $user->getCreatedAt()->getTimezone()->getName());
    }

    public function testEveryUserHasRoleUserEvenWithNoStoredRoles(): void
    {
        $user = new User();

        self::assertSame(['ROLE_USER'], $user->getRoles());
    }

    public function testAdminRoleIsKeptAndRoleUserIsNotDuplicated(): void
    {
        $user = (new User())->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        self::assertSame(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
    }

    public function testEmailIsTrimmedAndLowercased(): void
    {
        $user = (new User())->setEmail('  Alice@Example.COM ');

        self::assertSame('alice@example.com', $user->getEmail());
    }

    public function testUserIdentifierIsTheEmail(): void
    {
        $user = (new User())->setEmail('alice@example.com');

        self::assertSame('alice@example.com', $user->getUserIdentifier());
    }

    public function testSerializationDoesNotLeakThePasswordHash(): void
    {
        $hash = '$2y$13$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ012';
        $user = (new User())->setEmail('alice@example.com')->setPassword($hash);

        $serialized = serialize($user);

        self::assertStringNotContainsString($hash, $serialized);
    }
}
