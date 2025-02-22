<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security\Voter;

use App\Auth\Application\Command\ActivateUser\ActivateUserCommand;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, ActivateUserCommand>
 */
class ActivateUserVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (ActivateUserCommand::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof ActivateUserCommand) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true;
    }
}
