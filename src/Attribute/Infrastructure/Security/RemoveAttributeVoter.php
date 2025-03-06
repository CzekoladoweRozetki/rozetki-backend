<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Security;

use App\Attribute\Application\Command\RemoveAttribute\RemoveAttributeCommand;
use App\Auth\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<string, RemoveAttributeCommand>
 */
class RemoveAttributeVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (RemoveAttributeCommand::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof RemoveAttributeCommand) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var (User&UserInterface)|null $user */
        $user = $token->getUser();

        if (null === $user) {
            return false;
        }

        return $user->hasRole('ROLE_ADMIN');
    }
}
