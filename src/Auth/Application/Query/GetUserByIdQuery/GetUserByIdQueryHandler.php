<?php

declare(strict_types=1);

namespace App\Auth\Application\Query\GetUserByIdQuery;

use App\Auth\Domain\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetUserByIdQueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(GetUserByIdQuery $query): ?UserDTO
    {
        $user = $this->userRepository->getUserById($query->id);

        return new UserDTO(
            $user->getId(),
            $user->getEmail(),
            $user->getPassword()
        );
    }
}
