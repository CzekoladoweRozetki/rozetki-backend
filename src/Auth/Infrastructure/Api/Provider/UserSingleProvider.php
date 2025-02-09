<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Auth\Application\Query\GetUserByIdQuery\GetUserByIdQuery;
use App\Auth\Application\Query\GetUserByIdQuery\UserDTO;
use App\Auth\Infrastructure\Api\Resource\User;
use App\Common\Application\Query\QueryBus;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProviderInterface<User>
 */
class UserSingleProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $query = new GetUserByIdQuery(Uuid::fromString($uriVariables['id']));

        /**
         * @var UserDTO $userDTO
         */
        $userDTO = $this->queryBus->query($query);

        return new User(
            $userDTO->id->toString(),
            $userDTO->email,
        );
    }
}
