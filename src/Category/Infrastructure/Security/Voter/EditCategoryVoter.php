<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Security\Voter;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\UserRole;
use App\Category\Application\Command\EditCommand\EditCategoryCommand;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<string, EditCategoryCommand>
 */
class EditCategoryVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (EditCategoryCommand::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof EditCategoryCommand) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var (UserInterface&User)|null $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (!$user->hasRole(UserRole::ROLE_ADMIN->value)) {
            return false;
        }

        return true;
    }
}
