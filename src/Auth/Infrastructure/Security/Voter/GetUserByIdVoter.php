<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security\Voter;

use App\Auth\Application\Query\GetUserByIdQuery\GetUserByIdQuery;
use App\Auth\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<string, GetUserByIdQuery>
 */
class GetUserByIdVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (GetUserByIdQuery::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof GetUserByIdQuery) {
            return false;
        }

        return true;
    }

    /**
     * @param GetUserByIdQuery $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var (UserInterface&User)|null $user */
        $user = $token->getUser();

        if (null === $user) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())
            || $user->getId()->equals($subject->id)
        ) {
            return true;
        }

        return false;
    }
}
