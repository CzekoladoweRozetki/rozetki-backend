<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security\Voter;

use App\Auth\Application\Command\RequestPasswordReset\RequestPasswordResetCommand;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, RequestPasswordResetCommand>
 */
class RequestPasswordResetVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (RequestPasswordResetCommand::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof RequestPasswordResetCommand) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true;
    }
}
