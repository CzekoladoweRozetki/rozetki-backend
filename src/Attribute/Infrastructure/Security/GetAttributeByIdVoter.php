<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Security;

use App\Attribute\Application\Command\CreateAttribute\CreateAttributeCommand;
use App\Attribute\Application\Query\GetAttributeByIdQuery\GetAttributeByIdQuery;
use App\Auth\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<string, CreateAttributeCommand>
 */
class GetAttributeByIdVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (GetAttributeByIdQuery::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof GetAttributeByIdQuery) {
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
