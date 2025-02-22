<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security\Voter;

use App\Auth\Application\Command\RegisterUserCommand\RegisterUserCommand;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, RegisterUserCommand>
 */
class RegisterUserVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (RegisterUserCommand::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof RegisterUserCommand) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true;
    }
}
