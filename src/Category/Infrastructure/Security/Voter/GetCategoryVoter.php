<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Security\Voter;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\UserRole;
use App\Category\Application\Query\GetCategory\GetCategoryQuery;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<string, GetCategoryQuery>
 */
class GetCategoryVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (GetCategoryQuery::class !== $attribute) {
            return false;
        }

        if ($subject instanceof GetCategoryQuery) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var (UserInterface&User)|null $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (!in_array(UserRole::ROLE_ADMIN->value, $user->getRoles())) {
            return false;
        }

        return true;
    }
}
