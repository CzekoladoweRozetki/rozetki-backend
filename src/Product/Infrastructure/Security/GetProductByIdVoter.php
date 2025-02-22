<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Security;

use App\Auth\Domain\Entity\User;
use App\Product\Application\Query\GetProductById\GetProductByIdQuery;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<string, GetProductByIdQuery>
 */
class GetProductByIdVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (GetProductByIdQuery::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof GetProductByIdQuery) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var UserInterface|User|null $user */
        $user = $token->getUser();

        if (null === $user) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        return false;
    }
}
