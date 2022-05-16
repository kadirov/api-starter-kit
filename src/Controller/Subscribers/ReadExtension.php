<?php declare(strict_types=1);

namespace App\Controller\Subscribers;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Controller\Base\AbstractController;
use App\Entity\Interfaces\IsDeletedSettableInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Class uses for change all queries to database.
 *
 * @package App\Controller\ApiPlatform\Extensions
 */
class ReadExtension extends AbstractController implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private array $resourceClassInterfaces = [];

    /**
     * Collection operations without id, like GET /users
     *
     * @param QueryBuilder                $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string                      $resourceClass
     * @param string|null                 $operationName
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    /**
     * Item operations with id, like GET /users/{id} or DELETE /users/{id}
     *
     * @param QueryBuilder                $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string                      $resourceClass
     * @param array                       $identifiers
     * @param string|null                 $operationName
     * @param array                       $context
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    /**
     * In this method you can join user table for all queries. So that users can see only their entities.
     * Also you should hide elements that marked as deleted.
     *
     * @param QueryBuilder $queryBuilder
     * @param string       $resourceClass
     */
    private function andWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $rootTable = $queryBuilder->getRootAliases()[0];
        $this->resourceClassInterfaces = class_implements($resourceClass);

        if ($this->hasResourceClassInterfaceOf(IsDeletedSettableInterface::class)) {
            $this->hideDeleted($queryBuilder, $rootTable);
        }

        switch ($resourceClass) {
//            case Application::class:
//                $this->joinEntityAndAddUser($queryBuilder, $rootTable, 'company');
//                $this->hideDeleted($queryBuilder, "company");
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
        $queryBuilder->andWhere("{$tableName}.user = :user");
        $queryBuilder->setParameter('user', $this->getUser());

        // or if you use microservices
        // $queryBuilder->andWhere("{$tableName}.userId = :userId");
        // $queryBuilder->setParameter('userId', $this->getJwtUser()->getId());
    }

    private function hideDeleted(QueryBuilder $queryBuilder, string $tableName): void
    {
        $queryBuilder->andWhere("{$tableName}.isDeleted = false");
    }

    private function hasResourceClassInterfaceOf(string $interfaceName): bool
    {
        return in_array($interfaceName, $this->resourceClassInterfaces, true);
    }
}
