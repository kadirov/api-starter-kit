<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform\Extensions;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use LogicException;

/**
 * Class uses for change all queries to database.
 *
 * @package App\Controller\ApiPlatform\Extensions
 */
class InsertAppUserAndHideDeletedExtension extends AbstractController implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
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

        switch ($resourceClass) {
//            case Application::class:
//                $this->joinEntityAndAddUser($queryBuilder, $rootTable, 'company');
//                $this->hideDeleted($queryBuilder, "company");
//                break;
//
//            case Company::class:
//                $this->addUser($queryBuilder, $rootTable);
//                $this->hideDeleted($queryBuilder, $rootTable);
//                break;

            case User::class:
//          case Company::class:
                $this->hideDeleted($queryBuilder, $rootTable);
                // if you use microservices
                // $this->addApp($queryBuilder, $rootTable);
                break;

            default:
                throw new LogicException('Entity is not found');
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
        $queryBuilder->setParameter('user', $this->getUser()/** or $this->getJwtUser() */);
    }

    private function hideDeleted(QueryBuilder $queryBuilder, string $tableName): void
    {
        $queryBuilder->andWhere("{$tableName}.isDeleted = false");
    }

    private function addApp(QueryBuilder $queryBuilder, string $tableName): void
    {
        $queryBuilder->andWhere("{$tableName}.appId = :appId");
        $queryBuilder->setParameter('appId', $this->getJwtUser()->getAppId());
    }
}
