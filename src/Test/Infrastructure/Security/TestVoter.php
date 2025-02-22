<?php

declare(strict_types=1);

namespace App\Test\Infrastructure\Security;

use App\Test\Application\Command\TestCommand;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, TestCommand>
 */
class TestVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (TestCommand::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof TestCommand) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true;
    }
}
