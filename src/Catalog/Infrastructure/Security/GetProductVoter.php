<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\Security;

use App\Catalog\Application\Query\GetProduct\GetProductQuery;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, GetProductQuery>
 */
class GetProductVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (GetProductQuery::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof GetProductQuery) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true;
    }
}
