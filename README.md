# Rozetki backend

## Modules

To configure new module you need to set:

- doctrine mappings in doctine.yaml
- api platform mappings in api_platform.yaml

Modules follow hexagonal architecture and CQRS pattern.
Base module structure:

- Domain
    - Entity
    - Repository
    - Service
    - ValueObject
    - Event
- Application
    - Command
    - Query
- Infrastructure
    - Repository
    - EventListener
    - API

## Domain

Entity should extend BaseEntity class.
Repository should extend Repository class.
Doctrine repository can use DoctrineRepositoryTrait for implementation of Repository interface.

## API

API documentation is available at /api/docs
To create new Api endpoint you need to:

- Create new Api resource in /module/Infrastructure/API/Resource
- Create providers for Get and GetCollection operations in /module/Infrastructure/API/Provider
- Create processors for Post, Patch and Delete operations in /module/Infrastructure/API/Processor
- Patch, Put, Delete ale need to point single provider

## Security

Each command and query should have security voter. They is messenger middleware for authorization.
Commands and queries have ExecutionContext argument that indicates context of calling like web, console, internal.
For web context authorization is on by default. If you want omit authorization you can use internal execution context.
