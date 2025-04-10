<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Security;

use App\Auth\Domain\Entity\User;
use App\PriceList\Application\Query\GetLowestPrice\GetLowestPriceQuery;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<string, GetLowestPriceQuery>
 */
class GetLowestPriceVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (GetLowestPriceQuery::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof GetLowestPriceQuery) {
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

        // This query is available to both admins and regular users
        // as it's needed for displaying prices on the frontend
        return true;
    }
}
