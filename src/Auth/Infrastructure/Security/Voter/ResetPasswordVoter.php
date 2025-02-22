<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security\Voter;

use App\Auth\Application\Command\ResetPassword\ResetPasswordCommand;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, ResetPasswordCommand>
 */
class ResetPasswordVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (ResetPasswordCommand::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof ResetPasswordCommand) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true;
    }
}
