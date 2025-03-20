<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Security;

use App\Attribute\Application\Query\GetAttributeValuesQuery\GetAttributeValuesQuery;
use App\Auth\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @template-extends Voter<string, GetAttributeValuesQuery>
 */
class GetAttributeValuesVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (GetAttributeValuesQuery::class === $attribute) {
            return true;
        }

        if ($subject instanceof GetAttributeValuesQuery) {
            return true;
        }

        return false;
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
