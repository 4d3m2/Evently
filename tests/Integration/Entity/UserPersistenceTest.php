<?php

namespace App\Tests\Integration\Entity;

use App\Entity\User;
use App\Tests\DatabaseTestCase;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserPersistenceTest extends DatabaseTestCase
{
    public function testUserCanBePersistedAndReloaded(): void
    {
        $user = $this->createUser('alice@example.com');
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $reloaded = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'alice@example.com']);

        self::assertInstanceOf(User::class, $reloaded);
        self::assertSame('Alice Martin', $reloaded->getFullName());
        self::assertNotNull($reloaded->getId());
    }

    public function testDatabaseRejectsDuplicateEmails(): void
    {
        $this->entityManager->persist($this->createUser('alice@example.com'));
        $this->entityManager->flush();

        $this->entityManager->persist($this->createUser('alice@example.com'));

        $this->expectException(UniqueConstraintViolationException::class);
        $this->entityManager->flush();
    }

    public function testValidatorReportsDuplicateEmailsBeforeTheDatabaseIsHit(): void
    {
        $this->entityManager->persist($this->createUser('alice@example.com'));
        $this->entityManager->flush();

        $violations = $this->getValidator()->validate($this->createUser('ALICE@example.com'));

        self::assertCount(1, $violations);
        self::assertSame('This email address is already registered.', $violations->get(0)->getMessage());
    }

    public function testInvalidEmailAndBlankNameAreRejected(): void
    {
        $user = (new User())->setEmail('not-an-email')->setFullName('');

        $violations = $this->getValidator()->validate($user);

        self::assertGreaterThanOrEqual(2, \count($violations));
    }

    private function createUser(string $email): User
    {
        return (new User())
            ->setEmail($email)
            ->setFullName('Alice Martin')
            ->setPassword('not-a-real-hash');
    }

    private function getValidator(): ValidatorInterface
    {
        $validator = self::getContainer()->get(ValidatorInterface::class);
        self::assertInstanceOf(ValidatorInterface::class, $validator);

        return $validator;
    }
}
