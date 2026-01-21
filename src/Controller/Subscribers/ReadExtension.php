<?php

declare(strict_types=1);

namespace App\Controller\Subscribers;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Interfaces\DeletedAtSettableInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Class uses for change all queries to database.
 *
 * @package App\Controller\ApiPlatform\Extensions
 */
class ReadExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private array $resourceClassInterfaces = [];

    public function __construct(
        private Security $security
    ) {
    }

    /**
     * Collection operations without id, like GET /users
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    /**
     * Item operations with id, like GET /users/{id} or DELETE /users/{id}
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    /**
     * In this method you can join user table for all queries. So that users can see only their entities.
     * Also, you should hide elements that marked as deleted.
     *
     * @param QueryBuilder $queryBuilder
     * @param string $resourceClass
     */
    private function andWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $rootTable = $queryBuilder->getRootAliases()[0];
        $this->resourceClassInterfaces = class_implements($resourceClass);

        if ($this->hasResourceClassInterfaceOf(DeletedAtSettableInterface::class)) {
            $this->hideDeleted($queryBuilder, $rootTable);
        }

        $user = $this->security->getUser();

        if ($user === null) {
            return;
        }

        switch ($resourceClass) {
//            case Application::class:
//                $this->joinEntityAndAddUser($queryBuilder, $rootTable, 'company');
//                break;
//
//            case Company::class:
//                $this->addUser($queryBuilder, $rootTable);
//                break;
        }
    }

    private function joinEntityAndAddUser(
        QueryBuilder $queryBuilder,
        string $rootTable,
        string $joinTable
    ): void {
        $queryBuilder->join("{$rootTable}.{$joinTable}", $joinTable);

        $this->addUser($queryBuilder, $joinTable);
    }

    private function addUser(QueryBuilder $queryBuilder, string $tableName): void
    {
        $user = $this->security->getUser();

        $queryBuilder->andWhere("{$tableName}.createdBy = :user");
        $queryBuilder->setParameter('user', $user);

        // or if you use microservices
        // $queryBuilder->andWhere("{$tableName}.userId = :userId");
        // $queryBuilder->setParameter('userId', $this->getJwtUser()->getId());
    }

    private function hideDeleted(QueryBuilder $queryBuilder, string $tableName): void
    {
        $queryBuilder->andWhere("{$tableName}.deletedAt is null");
    }

    private function hasResourceClassInterfaceOf(string $interfaceName): bool
    {
        return in_array($interfaceName, $this->resourceClassInterfaces, true);
    }
}
