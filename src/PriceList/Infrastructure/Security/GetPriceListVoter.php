<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Security;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\UserRole;
use App\PriceList\Application\Query\GetPriceListCommand\GetPriceListQuery;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<string, GetPriceListQuery>
 */
class GetPriceListVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (GetPriceListQuery::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof GetPriceListQuery) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var (User&UserInterface)|null $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($user->doesNotHaveRole(UserRole::ROLE_ADMIN->value)) {
            return false;
        }

        return true;
    }
}
