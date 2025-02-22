<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Security;

use App\Product\Application\Command\CreateProduct\CreateProductCommand;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, CreateProductCommand>
 */
class CreateProductVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (CreateProductCommand::class !== $attribute) {
            return false;
        }

        if (!$subject instanceof CreateProductCommand) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
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
