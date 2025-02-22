<?php

declare(strict_types=1);

namespace App\Test\Infrastructure\Security;

use App\Test\Application\Command\UpdateTestCommand;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, UpdateTestCommand>
 */
class UpdateTestVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (UpdateTestCommand::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof UpdateTestCommand) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true;
    }
}
